create schema if not exists lbaw22102;

SET search_path TO lbaw22102;
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
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS vote CASCADE;

DROP TYPE IF EXISTS reportState;
DROP type if EXISTS notificationTypes;

CREATE TYPE reportState as ENUM ('Pending','Rejected','Banned');
CREATE TYPE notificationTypes as ENUM ('Invite','Message','Report');


-- Table: user

CREATE TABLE users (
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
    id     SERIAL PRIMARY KEY,
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
    idEvent     INTEGER NOT NULL REFERENCES event (id),
    id          INTEGER NOT NULL REFERENCES users (id)        
);


-- Table: report

CREATE TABLE report (
    idReport 	SERIAL PRIMARY KEY,
    idEvent  	INTEGER NOT NULL REFERENCES event (id),
    idManager   	INTEGER REFERENCES users (id),
    idReporter  	INTEGER NOT NULL REFERENCES users (id),
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
    idUser      INTEGER NOT NULL REFERENCES users (id),
    idEvent INTEGER NOT NULL REFERENCES event (id),
    PRIMARY KEY (
        idUser,
        idEvent
    )
);


-- Table: choose_Option

CREATE TABLE choose_Option (
    id       INTEGER NOT NULL REFERENCES users (id),
    idOption INTEGER NOT NULL REFERENCES option,
    PRIMARY KEY (
        id,
        idOption
    )
);



-- Table: event_Organizer

CREATE TABLE event_Organizer (
    id      INTEGER NOT NULL REFERENCES users (id),
    idEvent INTEGER NOT NULL REFERENCES event (id),
    PRIMARY KEY (
        id,
        idEvent
    )
);


-- Table: event_Tag

CREATE TABLE event_Tag (
    idTag   INTEGER NOT NULL REFERENCES tag (idTag),
    idEvent INTEGER NOT NULL REFERENCES event (id),
    PRIMARY KEY (
        idTag,
        idEvent
    )
);


-- Table: invite

CREATE TABLE invite (
    idEvent     INTEGER NOT NULL REFERENCES event (id),
    idInvitee   INTEGER NOT NULL REFERENCES users (id),
    idOrganizer INTEGER NOT NULL REFERENCES users (id),
    accepted    BOOLEAN,
    PRIMARY KEY (
        idEvent,
        idInvitee
    )
);


-- Table: message

CREATE TABLE message (
    id SERIAL PRIMARY KEY,
    content      TEXT,
    date      DATE NOT NULL,
    likeCount INTEGER NOT NULL DEFAULT (0),
    idEvent   INTEGER NOT NULL REFERENCES event (id),
    idUser	   INTEGER NOT NULL REFERENCES users (id),
    parent    INTEGER REFERENCES message (id)
);


-- Table: message_File

CREATE TABLE message_File (
    idFile    SERIAL PRIMARY KEY,
    file      TEXT,
    idMessage INTEGER NOT NULL REFERENCES message (id) 
);


-- Table: notification

CREATE TABLE notification (
    id        SERIAL PRIMARY KEY,
    content   TEXT NOT NULL,
    date      DATE NOT NULL,
    read      BOOLEAN NOT NULL DEFAULT (False),
    idUser    INTEGER NOT NULL REFERENCES users (id),
    Type	   notificationTypes,
    idReport  INTEGER REFERENCES report (idReport) CHECK ((idReport = NULL) or (idReport != NULL and type = 'Report')),
    idEvent   INTEGER CHECK ((idEvent = NULL) or (idEvent != NULL and type = 'Invite')),
    idInvitee INTEGER CHECK ((idInvitee = NULL) or (idInvitee != NULL and type = 'Invite')),
    idMessage INTEGER REFERENCES message (id) CHECK ((idMessage = NULL) or (idMessage != NULL and type = 'Message')),
    FOREIGN KEY (
        idEvent,
        idInvitee
    )
    REFERENCES invite
);


-- Table: vote

CREATE TABLE vote (
    id        INTEGER NOT NULL REFERENCES users (id),
    idMessage INTEGER NOT NULL REFERENCES message (id),
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
    IF OLD.id IN (SELECT id from event_organizer WHERE idEvent = OLD.idEvent) and (select count(*) from event_organizer WHERE idEvent = OLD.idEvent) = 1 AND (SELECT COUNT(*) FROM attendee WHERE idEvent = OLD.idEvent) > 1
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
INSERT INTO notification(content, date, idUser, type, idInvitee, idEvent)
    VALUES (concat('You have been invited to a new event: ', (select title from event where event.id = new.idEvent)) , now(),  NEW.idInvitee, 'Invite', NEW.idInvitee, NEW.idEvent);
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
    INSERT INTO notification(content, date, idUser, type, idMessage)
    VALUES (concat('New notification: ', NEW.content), NEW.date, 
            (SELECT event_organizer.id FROM event_Organizer WHERE event_Organizer.idEvent = NEW.idEvent), 'Message', NEW.id);
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
    INSERT INTO notification(content, date, idUser, type, idReport)
    VALUES (concat('New report notification: ',NEW.motive), NEW.date, 
            (SELECT users.id from users WHERE isadmin = TRUE ORDER BY random() LIMIT 1), 'Report', NEW.idreport);
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
    INSERT INTO attendee (idUser, idEvent) SELECT NEW.idUser, NEW.idEvent;
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
        INSERT INTO attendee (idUser, idEvent) SELECT NEW.idInvitee, NEW.idEvent;
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
    	INSERT INTO notification(content, date, idUser, type, idReport)
        VALUES ('Your event was banned!', NEW.date, 
                (SELECT event_organizer.id from event_Organizer WHERE event_organizer.idEvent = New.idEvent LIMIT 1),
                'Report', NEW.idReport);
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


---POPULATE

INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('admin', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'admin1_wemeet@gmail.com', '', NULL, TRUE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('mariamota22', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'mariamota2002@gmail.com', 'https://drive.google.com/file/d/1rh0RDPgLJvjbfxuv6TKvp_ekTEASTPnI/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('joaosilva1', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'jojo21@gmail.com', 'https://drive.google.com/file/d/10sSg9mua_y9J4KcgmnCZlL_Cj0LQTeWY/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('manuelaesteves33', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'manuelassteves33@gmail.com', 'https://drive.google.com/file/d/11LHjxNDDVlsa9iJRbWYdCVyuWtYCalX2/view?usp=share_link', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('raulferreira21', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'raul_ferreira_21@gmail.com', 'https://drive.google.com/file/d/1D8GL8FJY11M4pesov2puGVCZt8Cp5gmX/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('nunomaciel77', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'nunomaciel77@gmail.com', 'https://drive.google.com/file/d/1_BQ4r_2R7k0b-rdnbPcl4P_boMDKz6Dn/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('maraneves45', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'maraneves32@gmail.com', 'https://drive.google.com/file/d/12_IzCtKTRRHTveIVSp03SgkrDCsroiVj/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('mcarlotacarneiro20', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'mcarlotaccar20@gmail.com', 'https://drive.google.com/file/d/1M8D9xsFxmkUaCptyTGrz2D7O069tKFVR/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('andreoliveira56', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'dreoliveira56@gmail.com', 'https://drive.google.com/file/d/15hPo_tlOat5wn1oJl4EkDMokwUhh-5HR/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('aefeup', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'aefeup@gmail.com', 'https://drive.google.com/file/d/1RCBXeNsklPGVDFJzwlZih-5ylSoR0gqO/view?usp=share_link', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('associacaoanimalareosa', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'aanimalareosa@gmail.com', 'https://drive.google.com/file/d/165yURoFQ6mAR245nFmoB37a4rmc5qI2e/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('apav', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'apav@gmail.com', 'https://drive.google.com/file/d/1Cb14cSP2B2lGxCQq2MD0pOOljM4pYHs3/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('ligaportuguesacontraocancro', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'info@ligacontracancro.pt', 'https://drive.google.com/file/d/1EF1-75IKM8QoKPgidzWolQRbX-yUtDeh/view?usp=sharing', NULL, FALSE);
INSERT INTO users (username, password, email, picture, isBlocked, isAdmin) VALUES ('manel142', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'manel142@gmail.com', 'https://drive.google.com/file/d/1H_AgmJKKt9lz3_xJx2o-XKRwb1Lls5mi/view?usp=sharing', NULL, FALSE);

INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('MADFest', 'Festival de música', TRUE, 'Madalena' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Pinheiro', 'Festa popular em Guimarães', TRUE, 'Guimaraes' , '2022-09-29 01:00:00', '2022-11-29 21:00:00', '2022-11-29 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Arraial', 'Festival de Engenharia', TRUE, 'Exponor' , '2021-10-05 01:00:00', '2022-10-31 22:00:00', '2022-11-03 06:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Tutorial de como beber um copo de agua', 'Stand-up Comedy', TRUE, 'Salvaterra de Magos' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Dia do Animal', 'Visita de pessoas a abrigos de animais abandonados', FALSE, 'Camara de Lobos' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Chikipark', 'Festa de piscina de bolas e trampolins', TRUE, 'Coimbra' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Abertura do AquaSlide', 'Parque aquático para familias', TRUE, 'Lisboa' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Comicon', 'Festival de Cultura japonesa', TRUE, 'Lisboa' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Iberanime', 'Festival de Animes', TRUE, 'Porto' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, local, publish_date, start_date, final_date) VALUES ('Maratona a favor da luta contra o cancro da mama', 'Maratona a favor da luta contra o cancro da mama', TRUE, 'Aveiro' , '2021-10-05 11:00:00', '2022-10-30 11:00:00', '2022-10-31 14:00:00');

INSERT INTO poll (title, description, date, isOpen, idEvent, id) VALUES ('MADFest - Piruka?', 'Querem o Piruka a atuar?', '2021-10-05 01:00:00', TRUE, 1, 6);
INSERT INTO poll (title, description, date, isOpen, idEvent, id) VALUES ('MADFest - Rui Veloso?', 'Querem o Rui Veloso a atuar?', '2021-10-05 01:00:00', TRUE, 1, 6);
INSERT INTO poll (title, description, date, isOpen, idEvent, id) VALUES ('Arrail - Quim Barreiros?', 'Querem o Quim Barreiros a atuar?', '2021-10-05 01:00:00', TRUE, 3, 10);
INSERT INTO poll (title, description, date, isOpen, idEvent, id) VALUES ('Dia do Animal - Gatos ou Cães?', 'Preferem visitar canis ou gatis?', '2021-10-05 01:00:00', TRUE, 5, 11);
INSERT INTO poll (title, description, date, isOpen, idEvent, id) VALUES ('Marantona', 'Querem música durante a maratona?', '2021-10-05 01:00:00', TRUE, 10, 13);

INSERT INTO report (idEvent, idManager, idReporter, date, motive, STATE) VALUES (4, NULL, 7, '2021-10-05 06:30:00', 'O tema do evento pareceu-me desadequado e sem sentido', 'Pending');

INSERT INTO option (text, idPoll) VALUES ('Sim', 1);
INSERT INTO option (text, idPoll) VALUES ('Não', 1);
INSERT INTO option (text, idPoll) VALUES ('Sim', 2);
INSERT INTO option (text, idPoll) VALUES ('Não', 2);
INSERT INTO option (text, idPoll) VALUES ('Sim', 3);
INSERT INTO option (text, idPoll) VALUES ('Não', 3);
INSERT INTO option (text, idPoll) VALUES ('Gatos', 4);
INSERT INTO option (text, idPoll) VALUES ('Cães', 4);
INSERT INTO option (text, idPoll) VALUES ('Sim - Tecno', 5);
INSERT INTO option (text, idPoll) VALUES ('Sim - Pop', 5);
INSERT INTO option (text, idPoll) VALUES ('Não', 5);

INSERT INTO tag (tagName) VALUES ('Desporto');
INSERT INTO tag (tagName) VALUES ('Família');
INSERT INTO tag (tagName) VALUES ('Festival');
INSERT INTO tag (tagName) VALUES ('Comédia');
INSERT INTO tag (tagName) VALUES ('Aquático');
INSERT INTO tag (tagName) VALUES ('Música');
INSERT INTO tag (tagName) VALUES ('Anime');
INSERT INTO tag (tagName) VALUES ('Internacional');
INSERT INTO tag (tagName) VALUES ('Arte e Cultura');
INSERT INTO tag (tagName) VALUES ('Carreira e Emprego');
INSERT INTO tag (tagName) VALUES ('Ensino');
INSERT INTO tag (tagName) VALUES ('Carros');
INSERT INTO tag (tagName) VALUES ('Corrida');
INSERT INTO tag (tagName) VALUES ('Dança');
INSERT INTO tag (tagName) VALUES ('Gaming');
INSERT INTO tag (tagName) VALUES ('Saúde');
INSERT INTO tag (tagName) VALUES ('Manifestação');
INSERT INTO tag (tagName) VALUES ('Política');
INSERT INTO tag (tagName) VALUES ('Animais');
INSERT INTO tag (tagName) VALUES ('Solidariedade');
INSERT INTO tag (tagName) VALUES ('Religião e Espiritualidade');
INSERT INTO tag (tagName) VALUES ('Ciência');
INSERT INTO tag (tagName) VALUES ('Tecnologia');
INSERT INTO tag (tagName) VALUES ('Viagens');
INSERT INTO tag (tagName) VALUES ('Robótica');
INSERT INTO tag (tagName) VALUES ('Computadores');
INSERT INTO tag (tagName) VALUES ('Escrita e Leitura');
INSERT INTO tag (tagName) VALUES ('Comida e bebida');
INSERT INTO tag (tagName) VALUES ('Engenharia');
INSERT INTO tag (tagName) VALUES ('Infantil');

INSERT INTO event_Organizer (id, idEvent) VALUES (6,1);
INSERT INTO event_Organizer (id, idEvent) VALUES (10,3);
INSERT INTO event_Organizer (id, idEvent) VALUES (11,5);
INSERT INTO event_Organizer (id, idEvent) VALUES (13,10);
INSERT INTO event_Organizer (id, idEvent) VALUES (4,1);
INSERT INTO event_Organizer (id, idEvent) VALUES (4,2);
INSERT INTO event_Organizer (id, idEvent) VALUES (14,4);
INSERT INTO event_Organizer (id, idEvent) VALUES (3,6);
INSERT INTO event_Organizer (id, idEvent) VALUES (5,7);
INSERT INTO event_Organizer (id, idEvent) VALUES (9,8);
INSERT INTO event_Organizer (id, idEvent) VALUES (9,9);

INSERT INTO attendee (idUser, idEvent) VALUES (9,1);
INSERT INTO attendee (idUser, idEvent) VALUES (3,1);
INSERT INTO attendee (idUser, idEvent) VALUES (8,5);
INSERT INTO attendee (idUser, idEvent) VALUES (2,3);
INSERT INTO attendee (idUser, idEvent) VALUES (9,3);
INSERT INTO attendee (idUser, idEvent) VALUES (5,5);
INSERT into attendee (idUser, idevent) VALUES (3,2);
INSERT into attendee (idUser, idevent) VALUES (3,4);
INSERT into attendee (idUser, idevent) VALUES (8,1);
INSERT into attendee (idUser, idevent) VALUES (4,3);
INSERT into attendee (idUser, idevent) VALUES (4,5);
INSERT into attendee (idUser, idevent) VALUES (4,6);
INSERT into attendee (idUser, idevent) VALUES (9,2);


INSERT INTO choose_Option (id, idOption) VALUES (9, 1);
INSERT INTO choose_Option (id, idOption) VALUES (9, 4);
INSERT INTO choose_Option (id, idOption) VALUES (3, 2);
INSERT INTO choose_Option (id, idOption) VALUES (3, 3);
INSERT INTO choose_Option (id, idOption) VALUES (2, 6);
INSERT INTO choose_Option (id, idOption) VALUES (9, 5);
INSERT INTO choose_Option (id, idOption) VALUES (2, 7);
INSERT INTO choose_Option (id, idOption) VALUES (5, 7);


INSERT INTO event_Tag (idTag, idEvent) VALUES (2,1);
INSERT INTO event_Tag (idTag, idEvent) VALUES (3,1);
INSERT INTO event_Tag (idTag, idEvent) VALUES (6,1);
INSERT INTO event_Tag (idTag, idEvent) VALUES (14,1);
INSERT INTO event_Tag (idTag, idEvent) VALUES (2,2);
INSERT INTO event_Tag (idTag, idEvent) VALUES (9,2);
INSERT INTO event_Tag (idTag, idEvent) VALUES (6,2);
INSERT INTO event_Tag (idTag, idEvent) VALUES (28,2);
INSERT INTO event_Tag (idTag, idEvent) VALUES (3,3);
INSERT INTO event_Tag (idTag, idEvent) VALUES (6,3);
INSERT INTO event_Tag (idTag, idEvent) VALUES (14,3);
INSERT INTO event_Tag (idTag, idEvent) VALUES (28,3);
INSERT INTO event_Tag (idTag, idEvent) VALUES (29,3);
INSERT INTO event_Tag (idTag, idEvent) VALUES (4,4);
INSERT INTO event_Tag (idTag, idEvent) VALUES (9,4);
INSERT INTO event_Tag (idTag, idEvent) VALUES (2,5);
INSERT INTO event_Tag (idTag, idEvent) VALUES (16,5);
INSERT INTO event_Tag (idTag, idEvent) VALUES (19,5);
INSERT INTO event_Tag (idTag, idEvent) VALUES (20,5);
INSERT INTO event_Tag (idTag, idEvent) VALUES (2,6);
INSERT INTO event_Tag (idTag, idEvent) VALUES (28,6);
INSERT INTO event_Tag (idTag, idEvent) VALUES (30,6);
INSERT INTO event_Tag (idTag, idEvent) VALUES (2,7);
INSERT INTO event_Tag (idTag, idEvent) VALUES (5,7);
INSERT INTO event_Tag (idTag, idEvent) VALUES (30,7);
INSERT INTO event_Tag (idTag, idEvent) VALUES (28,7);
INSERT INTO event_Tag (idTag, idEvent) VALUES (7,8);
INSERT INTO event_Tag (idTag, idEvent) VALUES (8,8);
INSERT INTO event_Tag (idTag, idEvent) VALUES (9,8);
INSERT INTO event_Tag (idTag, idEvent) VALUES (15,8);
INSERT INTO event_Tag (idTag, idEvent) VALUES (23,8);
INSERT INTO event_Tag (idTag, idEvent) VALUES (26,8);
INSERT INTO event_Tag (idTag, idEvent) VALUES (7,9);
INSERT INTO event_Tag (idTag, idEvent) VALUES (8,9);
INSERT INTO event_Tag (idTag, idEvent) VALUES (9,9);
INSERT INTO event_Tag (idTag, idEvent) VALUES (15,9);
INSERT INTO event_Tag (idTag, idEvent) VALUES (23,9);
INSERT INTO event_Tag (idTag, idEvent) VALUES (26,9);
INSERT INTO event_Tag (idTag, idEvent) VALUES (1,10);
INSERT INTO event_Tag (idTag, idEvent) VALUES (13,10);
INSERT INTO event_Tag (idTag, idEvent) VALUES (16,10);
INSERT INTO event_Tag (idTag, idEvent) VALUES (17,10);
INSERT INTO event_Tag (idTag, idEvent) VALUES (20,10);

----
INSERT INTO invite (idEvent, idInvitee, idOrganizer, accepted) VALUES (1,8,4, FALSE);
INSERT INTO invite (idEvent, idInvitee, idOrganizer, accepted) VALUES (3,8,10, FALSE);
INSERT INTO invite (idEvent, idInvitee, idOrganizer, accepted) VALUES (10,8,13, FALSE);
INSERT INTO invite (idEvent, idInvitee, idOrganizer, accepted) VALUES (5,5,11, TRUE);
INSERT INTO invite (idEvent, idInvitee, idOrganizer, accepted) VALUES (5,8,11, TRUE);
-----
INSERT INTO message (content, date, likeCount, idEvent, idUser) VALUES ('Boa noite, é possível levar o meu marido na visita? Ele é ex-sócio da associação. Obrigada', '2022-10-30 21:00:00', 1, 5, 8);
INSERT INTO message (content, date, likeCount, idEvent, idUser) VALUES ('Boa tarde, há lugares de refeições dentro do parque? Se sim, quais (o que servem?)', '2021-10-05 13:20:04', 0, 7, 3);
INSERT INTO message (content, date, likeCount, idEvent, idUser, parent) VALUES ('Boa noite, sim venham!' , '2022-10-30 21:10:00', 1, 5, 11, 1);
INSERT INTO message (content, date, likeCount, idEvent, idUser, parent) VALUES (NULL, '2022-10-30 23:00:00', 2, 5, 8, 1);

INSERT INTO message_File (file, idMessage) VALUES ('https://drive.google.com/file/d/1ew6LkiYFrDw5enUUaU47hNEgxGiPC5M_/view?usp=sharing', 3);

INSERT INTO notification (content, date, read, idUser, type, idReport, idEvent, idInvitee, idMessage) VALUES ('You have a new message!', '2022-10-30 21:00:00', FALSE, 3, 'Message', NULL, NULL, NULL, 1);
INSERT INTO notification (content, date, read, idUser, type, idReport, idEvent, idInvitee, idMessage) VALUES ('You have a new message!', '2022-10-30 21:00:00', FALSE, 9, 'Message', NULL, NULL, NULL, 1);
INSERT INTO notification (content, date, read, idUser, type, idReport, idEvent, idInvitee, idMessage) VALUES ('We have been invited!', '2021-10-05 13:20:04', FALSE, 2, 'Message', NULL, NULL, NULL, 2);
INSERT INTO notification (content, date, read, idUser, type, idReport, idEvent, idInvitee, idMessage) VALUES ('We have been invited!', '2021-10-05 13:20:04', FALSE, 9, 'Message', NULL, NULL, NULL, 2);

INSERT INTO vote (id, idMessage) VALUES (11, 1);
INSERT INTO vote (id, idMessage) VALUES (8, 3);
INSERT INTO vote (id, idMessage) VALUES (11, 4);
INSERT INTO vote (id, idMessage) VALUES (5, 4);
