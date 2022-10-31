DROP TABLE IF EXISTS attendee CASCADE;
DROP TABLE IF EXISTS choose_Option CASCADE;
DROP TABLE IF EXISTS EVENT CASCADE;
DROP TABLE IF EXISTS event_Organizer CASCADE;
DROP TABLE IF EXISTS event_Tag CASCADE;
DROP TABLE IF EXISTS invite CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS message_File CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS option CASCADE;
DROP TABLE IF EXISTS poll CASCADE;
DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS "user" CASCADE;
DROP TABLE IF EXISTS vote CASCADE;

DROP TYPE IF EXISTS reportState;
DROP type if EXISTS notificationTypes;

CREATE TYPE reportState as ENUM ('Pending','Rejected','Banned');
CREATE TYPE notificationTypes as ENUM ('Invite','Message','Report');


-- Table: user

CREATE TABLE "user" (
    id        SERIAL PRIMARY KEY,
    username  TEXT UNIQUE NOT NULL,
    password  TEXT NOT NULL,
    email     TEXT UNIQUE NOT NULL,
    picture   TEXT,
    isBlocked TEXT,
    isAdmin   BOOLEAN DEFAULT (False) 
);

-- Table: event

CREATE TABLE event (
    idEvent     SERIAL PRIMARY KEY,
    title       TEXT NOT NULL,
    description TEXT,
    visibility  BOOLEAN NOT NULL,
    picture     TEXT,
    local       TEXT NOT NULL,
  	publish_date DATE NOT NULL,
    start_date DATE NOT NULL,
    final_date DATE NOT NULL
);


-- Table: poll

CREATE TABLE poll (
    idPoll      SERIAL PRIMARY KEY,
    title       TEXT NOT NULL,
    description TEXT,
    date        DATE NOT NULL,
    isOpen      BOOLEAN NOT NULL DEFAULT (True),
    idEvent     INTEGER NOT NULL REFERENCES event (idEvent),
    id          INTEGER NOT NULL REFERENCES "user" (id)        
);


-- Table: report

CREATE TABLE report (
    idReport 	SERIAL PRIMARY KEY,
    idEvent  	INTEGER NOT NULL REFERENCES event (idEvent),
    idManager   	INTEGER REFERENCES "user" (id),
    idReporter  	INTEGER NOT NULL REFERENCES "user" (id),
    date     	DATE NOT NULL,
    motive   	TEXT NOT NULL,
  	STATE    	reportState NOT NULL DEFAULT ('Pending')
);

-- Table: option

CREATE TABLE option (
    idOption SERIAL PRIMARY KEY,
    text     TEXT NOT NULL,
    idPoll   INTEGER NOT NULL REFERENCES poll
);


-- Table: tag

CREATE TABLE tag (
    idTag   SERIAL PRIMARY KEY,
    tagName TEXT UNIQUE NOT NULL
                    
);

-- Table: attendee
CREATE TABLE attendee (
    id      INTEGER NOT NULL REFERENCES "user" (id),
    idEvent INTEGER NOT NULL REFERENCES event (idEvent),
    PRIMARY KEY (
        id,
        idEvent
    )
);


-- Table: choose_Option

CREATE TABLE choose_Option (
    id       INTEGER NOT NULL REFERENCES "user" (id),
    idOption INTEGER NOT NULL REFERENCES option,
    PRIMARY KEY (
        id,
        idOption
    )
);



-- Table: event_Organizer

CREATE TABLE event_Organizer (
    id      INTEGER NOT NULL REFERENCES "user" (id),
    idEvent INTEGER NOT NULL REFERENCES event (idEvent),
    PRIMARY KEY (
        id,
        idEvent
    )
);


-- Table: event_Tag

CREATE TABLE event_Tag (
    idTag   INTEGER NOT NULL REFERENCES tag (idTag),
    idEvent INTEGER NOT NULL REFERENCES event (idEvent),
    PRIMARY KEY (
        idTag,
        idEvent
    )
);


-- Table: invite

CREATE TABLE invite (
    idEvent     INTEGER NOT NULL REFERENCES event (idEvent),
    idInvitee   INTEGER NOT NULL REFERENCES "user" (id),
    idOrganizer INTEGER NOT NULL REFERENCES "user" (id),
    accepted    BOOLEAN,
    PRIMARY KEY (
        idEvent,
        idInvitee
    )
);


-- Table: message

CREATE TABLE message (
    idMessage SERIAL PRIMARY KEY,
    text      TEXT,
    date      DATE NOT NULL,
    likeCount INTEGER NOT NULL DEFAULT (0),
    idEvent   INTEGER NOT NULL REFERENCES event (idEvent),
    id	   INTEGER NOT NULL REFERENCES "user" (id),
    parent    INTEGER REFERENCES message (idMessage)
);


-- Table: message_File

CREATE TABLE message_File (
    idFile    SERIAL PRIMARY KEY,
    file      TEXT,
    idMessage INTEGER NOT NULL REFERENCES message (idMessage) 
);


-- Table: notification

CREATE TABLE notification (
    idNotif   SERIAL PRIMARY KEY,
    text      TEXT NOT NULL,
    date      DATE NOT NULL,
    read      BOOLEAN NOT NULL DEFAULT (False),
    id        INTEGER NOT NULL REFERENCES "user" (id),
    Type	   notificationTypes,
    idReport  INTEGER REFERENCES report (idReport) CHECK ((idReport = NULL) or (idReport != NULL and type = 'Report')),
    idEvent   INTEGER CHECK ((idEvent = NULL) or (idEvent != NULL and type = 'Invite')),
    idInvitee INTEGER CHECK ((idInvitee = NULL) or (idInvitee != NULL and type = 'Invite')),
    idMessage INTEGER REFERENCES message (idMessage) CHECK ((idMessage = NULL) or (idMessage != NULL and type = 'Message')),
    FOREIGN KEY (
        idEvent,
        idInvitee
    )
    REFERENCES invite
);


-- Table: vote

CREATE TABLE vote (
    id        INTEGER NOT NULL REFERENCES "user" (id),
    idMessage INTEGER NOT NULL REFERENCES message (idMessage),
    PRIMARY KEY (
        id,
        idMessage
    )
);


-----------------------------------------
-- INDEXES
-----------------------------------------

CREATE INDEX event_poll ON poll USING hash (idEvent);
CREATE INDEX date_message ON message USING btree (date);
CREATE INDEX tag_alphabetic ON tag USING btree (tagName);

-- FTS INDEXES

ALTER TABLE event ADD COLUMN tsvectors TSVECTOR;

CREATE OR REPLACE FUNCTION event_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('english', NEW.title), 'A') ||
         setweight(to_tsvector('english', NEW.description), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.title <> OLD.title OR NEW.description <> OLD.description) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('english', NEW.title), 'A') ||
             setweight(to_tsvector('english', NEW.description), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS event_search_update on event CASCADE;
CREATE TRIGGER event_search_update
 BEFORE INSERT OR UPDATE ON event
 FOR EACH ROW
 EXECUTE PROCEDURE event_search_update();

 CREATE INDEX search_idx ON event USING GIN (tsvectors); 


-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

--- TRIGGER01
CREATE OR REPLACE FUNCTION check_event_organizer() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF OLD.id IN (SELECT id from event_organizer WHERE idevent = OLD.idEvent) and (select count(*) from event_organizer WHERE idevent = OLD.idEvent) = 1 AND (SELECT COUNT(*) FROM attendee WHERE idevent = OLD.idEvent) > 1
    THEN
        RAISE EXCEPTION 'Event with attendees must have at least one event organizer!';
    END IF;
    RETURN OLD;
END;
$BODY$
    LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS check_event_organizer on attendee CASCADE;
CREATE TRIGGER check_event_organizer
    BEFORE DELETE ON attendee
    FOR EACH ROW
EXECUTE PROCEDURE check_event_organizer();


--- TRIGGER02 
CREATE OR REPLACE FUNCTION add_invite_notification() RETURNS TRIGGER AS
$BODY$
BEGIN
INSERT INTO notification(text, date, id, type, idinvitee, idevent)
    VALUES (concat('You have been invited to a new event: ', (select title from event where event.idevent = new.idevent)) , now(),  NEW.idinvitee, 'Invite', NEW.idinvitee, NEW.idevent);
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS add_invite_notification on invite CASCADE;
CREATE TRIGGER add_invite_notification
    AFTER INSERT
    ON invite
    FOR EACH ROW
EXECUTE PROCEDURE add_invite_notification(); 

--- TRIGGER03 
CREATE OR REPLACE FUNCTION add_message_notification() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO notification(text, date, id, type, idmessage)
    VALUES (concat('New notification: ', NEW.text), NEW.date, 
            (SELECT event_organizer.id FROM event_Organizer WHERE event_Organizer.idEvent = NEW.idEvent), 'Message', NEW.idmessage);
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS add_message_notification on message CASCADE;
CREATE TRIGGER add_message_notification
AFTER INSERT ON message
FOR EACH ROW
EXECUTE PROCEDURE add_message_notification();

--- TRIGGER04 
CREATE OR REPLACE FUNCTION add_report_admin_notification() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO notification(text, date, id, type, idreport)
    VALUES (concat('New report notification: ',NEW.motive), NEW.date, 
            (SELECT "user".id from "user" WHERE isadmin = TRUE ORDER BY random() LIMIT 1), 'Report', NEW.idreport);
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;
    
DROP TRIGGER IF EXISTS add_report_admin_notification on report CASCADE;
CREATE TRIGGER add_report_admin_notification
AFTER INSERT ON report
FOR EACH ROW
EXECUTE PROCEDURE add_report_admin_notification();

--- TRIGGER05 
CREATE OR REPLACE FUNCTION edit_message() RETURNS trigger AS
$BODY$
    BEGIN
    UPDATE message SET text = NEW.text WHERE id = OLD.id;
RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS edit_message on message CASCADE;
CREATE TRIGGER edit_message
    AFTER UPDATE ON message
    FOR EACH ROW
EXECUTE PROCEDURE edit_message();

--- TRIGGER06 
CREATE OR REPLACE FUNCTION create_event_organizer() RETURNS TRIGGER AS
$BODY$
    BEGIN
    INSERT INTO attendee (id, idEvent) SELECT NEW.id, NEW.idEvent;
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;
    
DROP TRIGGER IF EXISTS create_event_organizer on event_organizer CASCADE;
CREATE TRIGGER create_event_organizer
    AFTER INSERT ON event_organizer
    FOR EACH ROW
EXECUTE PROCEDURE create_event_organizer();

--- TRIGGER07 
CREATE OR REPLACE FUNCTION accept_invite() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF OLD.accepted != TRUE AND NEW.accepted = TRUE
    THEN
        INSERT INTO attendee (id, idevent) SELECT NEW.idInvitee, NEW.idevent;
    END IF;
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS accept_invite on invite CASCADE;
CREATE TRIGGER accept_invite
    AFTER UPDATE
    ON invite
    FOR EACH ROW
EXECUTE PROCEDURE accept_invite(); 


--- TRIGGER08 
CREATE OR REPLACE FUNCTION add_banned_notification() RETURNS TRIGGER AS
$BODY$
BEGIN
	IF NEW.state = 'Banned' THEN
    	INSERT INTO notification(text, date, id, type, idreport)
        VALUES ('Your event was banned!', NEW.date, 
                (SELECT event_organizer.id from event_organizer WHERE event_organizer.idevent = New.idevent LIMIT 1),
                'Report', NEW.idreport);
    END IF;
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS add_banned_notification on report CASCADE;
CREATE TRIGGER add_banned_notification
AFTER UPDATE ON report
FOR EACH ROW
EXECUTE PROCEDURE add_banned_notification();