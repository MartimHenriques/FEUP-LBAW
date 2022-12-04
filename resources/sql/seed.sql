create schema if not exists public;

SET search_path TO public;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS event CASCADE;
DROP TABLE IF EXISTS poll CASCADE;
DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS option CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS attendee CASCADE;
DROP TABLE IF EXISTS choose_option CASCADE;
DROP TABLE IF EXISTS event_organizer CASCADE;
DROP TABLE IF EXISTS event_tag CASCADE;
DROP TABLE IF EXISTS invite CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS message_file CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS vote CASCADE;
DROP TYPE IF EXISTS reportState CASCADE;
DROP type if EXISTS notificationTypes CASCADE;

CREATE TYPE reportState as ENUM ('Pending','Rejected','Banned');
CREATE TYPE notificationTypes as ENUM ('Invite','Message','Report');


-- Table: user

CREATE TABLE users (
    id        SERIAL PRIMARY KEY,
    username  TEXT UNIQUE NOT NULL,
    password  TEXT NOT NULL,
    email     TEXT UNIQUE NOT NULL,
    picture   TEXT,
    is_blocked TEXT,
    is_admin   BOOLEAN DEFAULT (False),
    remember_token VARCHAR
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
    id      SERIAL PRIMARY KEY,
    title       TEXT NOT NULL,
    description TEXT,
    date        DATE NOT NULL,
    is_open      BOOLEAN NOT NULL DEFAULT (True),
    id_event     INTEGER NOT NULL REFERENCES event (id),
    id_user          INTEGER NOT NULL REFERENCES users (id)        
);


-- Table: report

CREATE TABLE report (
    id 	SERIAL PRIMARY KEY,
    id_event  	INTEGER NOT NULL REFERENCES event (id),
    id_manager   	INTEGER REFERENCES users (id),
    id_reporter  	INTEGER NOT NULL REFERENCES users (id),
    date     	DATE NOT NULL,
    motive   	TEXT NOT NULL,
  	STATE    	reportState NOT NULL DEFAULT ('Pending')
);

-- Table: option

CREATE TABLE option (
    id	 SERIAL PRIMARY KEY,
    text     TEXT NOT NULL,
    id_poll   INTEGER NOT NULL REFERENCES poll
);


-- Table: tag

CREATE TABLE tag (
    id   SERIAL PRIMARY KEY,
    tag_name TEXT UNIQUE NOT NULL
                    
);

-- Table: attendee
CREATE TABLE attendee (
    id_user      INTEGER NOT NULL REFERENCES users (id),
    id_event INTEGER NOT NULL REFERENCES event (id),
    PRIMARY KEY (
        id_user,
        id_event
    )
);


-- Table: choose_option

CREATE TABLE choose_option (
    id_user       INTEGER NOT NULL REFERENCES users (id),
    id_option INTEGER NOT NULL REFERENCES option,
    PRIMARY KEY (
        id_user,
        id_option
    )
);



-- Table: event_organizer

CREATE TABLE event_organizer (
    id_user      INTEGER NOT NULL REFERENCES users (id),
    id_event INTEGER NOT NULL REFERENCES event (id),
    PRIMARY KEY (
        id_user,
        id_event
    )
);


-- Table: event_Tag

CREATE TABLE event_tag (
    id_tag   INTEGER NOT NULL REFERENCES tag (id),
    id_event INTEGER NOT NULL REFERENCES event (id),
    PRIMARY KEY (
        id_tag,
        id_event
    )
);


-- Table: invite

CREATE TABLE invite (
    id_event     INTEGER NOT NULL REFERENCES event (id),
    id_invitee   INTEGER NOT NULL REFERENCES users (id),
    id_organizer INTEGER NOT NULL REFERENCES users (id),
    accepted    BOOLEAN,
    PRIMARY KEY (
        id_event,
        id_invitee
    )
);


-- Table: message

CREATE TABLE message (
    id SERIAL PRIMARY KEY,
    content      TEXT,
    date      DATE NOT NULL,
    like_count INTEGER NOT NULL DEFAULT (0),
    id_event   INTEGER NOT NULL REFERENCES event (id),
    id_user	   INTEGER NOT NULL REFERENCES users (id),
    parent    INTEGER REFERENCES message (id)
);


-- Table: message_File

CREATE TABLE message_file (
    id    SERIAL PRIMARY KEY,
    file      TEXT,
    id_message INTEGER NOT NULL REFERENCES message (id) 
);


-- Table: notification

CREATE TABLE notification (
    id        SERIAL PRIMARY KEY,
    content   TEXT NOT NULL,
    date      DATE NOT NULL,
    read      BOOLEAN NOT NULL DEFAULT (False),
    id_user    INTEGER NOT NULL REFERENCES users (id),
    type   notificationTypes,
    id_report  INTEGER REFERENCES report (id) CHECK ((id_report = NULL) or (id_report != NULL and type = 'Report')),
    id_event   INTEGER CHECK ((id_event = NULL) or (id_event != NULL and type = 'Invite')),
    id_invitee INTEGER CHECK ((id_invitee = NULL) or (id_invitee != NULL and type = 'Invite')),
    id_message INTEGER REFERENCES message (id) CHECK ((id_message = NULL) or (id_message != NULL and type = 'Message')),
    FOREIGN KEY (
        id_event,
        id_invitee
    )
    REFERENCES invite
);


-- Table: vote

CREATE TABLE vote (
    id_user        INTEGER NOT NULL REFERENCES users (id),
    id_message INTEGER NOT NULL REFERENCES message (id),
    PRIMARY KEY (
        id_user,
        id_message
    )
);


-----------------------------------------
-- INDEXES
-----------------------------------------

CREATE INDEX event_poll ON poll USING hash (id_event);
CREATE INDEX date_message ON message USING btree (date);
CREATE INDEX tag_alphabetic ON tag USING btree (tag_name);

-- FTS INDEXES

ALTER TABLE event ADD COLUMN search TSVECTOR;

CREATE OR REPLACE FUNCTION event_search_update() RETURNS TRIGGER AS
$BODY$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.search = ( SELECT
         setweight(to_tsvector(NEW.title), 'A') ||
         setweight(to_tsvector(NEW.description), 'B') FROM event WHERE NEW.id=event.id
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.title <> OLD.title OR NEW.description <> OLD.description) THEN
           NEW.search = ( SELECT
             setweight(to_tsvector(NEW.title), 'A') ||
             setweight(to_tsvector(NEW.description), 'B') FROM event WHERE NEW.id=event.id
           );
         END IF;
 END IF;
 RETURN NEW;
END $BODY$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS event_search_update on event CASCADE;
CREATE TRIGGER event_search_update
 BEFORE INSERT OR UPDATE ON event
 FOR EACH ROW
 EXECUTE PROCEDURE event_search_update();

 CREATE INDEX search_idx ON event USING GIN (search); 


-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

--- TRIGGER01
CREATE OR REPLACE FUNCTION check_event_organizer() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF OLD.id_user IN (SELECT id_user from event_organizer WHERE id_event = OLD.id_event) and (select count(*) from event_organizer WHERE id_event = OLD.id_event) = 1 AND (SELECT COUNT(*) FROM attendee WHERE id_event = OLD.id_event) > 1
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
INSERT INTO notification(content, date, id_user, type, id_invitee, id_event)
    VALUES (concat('You have been invited to a new event: ', (select title from event where event.id = new.id_event)) , now(),  NEW.id_invitee, 'Invite', NEW.id_invitee, NEW.id_event);
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
    INSERT INTO notification(content, date, id_user, type, id_message)
    VALUES (concat('New notification: ', NEW.content), NEW.date, 
            (SELECT event_organizer.id_user FROM event_organizer WHERE event_organizer.id_event = NEW.id_event), 'Message', NEW.id);
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
    INSERT INTO notification(content, date, id_user, type, id_report)
    VALUES (concat('New report notification: ',NEW.motive), NEW.date, 
            (SELECT users.id from users WHERE is_admin = TRUE ORDER BY random() LIMIT 1), 'Report', NEW.id);
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
    INSERT INTO attendee (id_user, id_event) SELECT NEW.id_user, NEW.id_event;
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
        INSERT INTO attendee (id_user, id_event) SELECT NEW.id_invitee, NEW.id_event;
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
    	INSERT INTO notification(content, date, id_user, type, id_report)
        VALUES ('Your event was banned!', NEW.date, 
                (SELECT event_organizer.id_user from event_organizer WHERE event_organizer.id_event = New.id_event LIMIT 1),
                'Report', NEW.id_report);
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

INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('admin', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'admin1_wemeet@gmail.com', '', NULL, TRUE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('mariamota22', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'mariamota2002@gmail.com', 'mariamota22.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('joaosilva1', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'jojo21@gmail.com', 'joaosilva1.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('manuelaesteves33', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'manuelassteves33@gmail.com', 'manuelaesteves33.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('raulferreira21', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'raul_ferreira_21@gmail.com', 'raulferreira21.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('nunomaciel77', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'nunomaciel77@gmail.com', 'nunomaciel77.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('maraneves45', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'maraneves32@gmail.com', 'maraneves45.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('mcarlotacarneiro20', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'mcarlotaccar20@gmail.com', 'mcarlotacarneiro20.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('andreoliveira56', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'dreoliveira56@gmail.com', 'andreoliveira56.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('aefeup', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'aefeup@gmail.com', 'aefeup.png', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('associacaoanimalareosa', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'aanimalareosa@gmail.com', 'associacaoanimalareosa.png', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('apav', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'apav@gmail.com', 'apav.jpg', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('ligaportuguesacontraocancro', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'info@ligacontracancro.pt', 'ligaportuguesacontraocancro.png', NULL, FALSE);
INSERT INTO users (username, password, email, picture, is_blocked, is_admin) VALUES ('manel142', '$2a$06$ARWKUty/arov5m7rDSnonOQHwu.cXcZg5TvtJhefx2A7kk3hwzGLq', 'manel142@gmail.com', 'manel142.jpg', NULL, FALSE);



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

INSERT INTO poll (title, description, date, is_open, id_event, id_user) VALUES ('MADFest - Piruka?', 'Querem o Piruka a atuar?', '2021-10-05 01:00:00', TRUE, 1, 6);
INSERT INTO poll (title, description, date, is_open, id_event, id_user) VALUES ('MADFest - Rui Veloso?', 'Querem o Rui Veloso a atuar?', '2021-10-05 01:00:00', TRUE, 1, 6);
INSERT INTO poll (title, description, date, is_open, id_event, id_user) VALUES ('Arrail - Quim Barreiros?', 'Querem o Quim Barreiros a atuar?', '2021-10-05 01:00:00', TRUE, 3, 10);
INSERT INTO poll (title, description, date, is_open, id_event, id_user) VALUES ('Dia do Animal - Gatos ou Cães?', 'Preferem visitar canis ou gatis?', '2021-10-05 01:00:00', TRUE, 5, 11);
INSERT INTO poll (title, description, date, is_open, id_event, id_user) VALUES ('Marantona', 'Querem música durante a maratona?', '2021-10-05 01:00:00', TRUE, 10, 13);

INSERT INTO report (id_event, id_manager, id_reporter, date, motive, STATE) VALUES (4, NULL, 7, '2021-10-05 06:30:00', 'O tema do evento pareceu-me desadequado e sem sentido', 'Pending');

INSERT INTO option (text, id_poll) VALUES ('Sim', 1);
INSERT INTO option (text, id_poll) VALUES ('Não', 1);
INSERT INTO option (text, id_poll) VALUES ('Sim', 2);
INSERT INTO option (text, id_poll) VALUES ('Não', 2);
INSERT INTO option (text, id_poll) VALUES ('Sim', 3);
INSERT INTO option (text, id_poll) VALUES ('Não', 3);
INSERT INTO option (text, id_poll) VALUES ('Gatos', 4);
INSERT INTO option (text, id_poll) VALUES ('Cães', 4);
INSERT INTO option (text, id_poll) VALUES ('Sim - Tecno', 5);
INSERT INTO option (text, id_poll) VALUES ('Sim - Pop', 5);
INSERT INTO option (text, id_poll) VALUES ('Não', 5);

INSERT INTO tag (tag_name) VALUES ('Desporto');
INSERT INTO tag (tag_name) VALUES ('Família');
INSERT INTO tag (tag_name) VALUES ('Festival');
INSERT INTO tag (tag_name) VALUES ('Comédia');
INSERT INTO tag (tag_name) VALUES ('Aquático');
INSERT INTO tag (tag_name) VALUES ('Música');
INSERT INTO tag (tag_name) VALUES ('Anime');
INSERT INTO tag (tag_name) VALUES ('Internacional');
INSERT INTO tag (tag_name) VALUES ('Arte e Cultura');
INSERT INTO tag (tag_name) VALUES ('Carreira e Emprego');
INSERT INTO tag (tag_name) VALUES ('Ensino');
INSERT INTO tag (tag_name) VALUES ('Carros');
INSERT INTO tag (tag_name) VALUES ('Corrida');
INSERT INTO tag (tag_name) VALUES ('Dança');
INSERT INTO tag (tag_name) VALUES ('Gaming');
INSERT INTO tag (tag_name) VALUES ('Saúde');
INSERT INTO tag (tag_name) VALUES ('Manifestação');
INSERT INTO tag (tag_name) VALUES ('Política');
INSERT INTO tag (tag_name) VALUES ('Animais');
INSERT INTO tag (tag_name) VALUES ('Solidariedade');
INSERT INTO tag (tag_name) VALUES ('Religião e Espiritualidade');
INSERT INTO tag (tag_name) VALUES ('Ciência');
INSERT INTO tag (tag_name) VALUES ('Tecnologia');
INSERT INTO tag (tag_name) VALUES ('Viagens');
INSERT INTO tag (tag_name) VALUES ('Robótica');
INSERT INTO tag (tag_name) VALUES ('Computadores');
INSERT INTO tag (tag_name) VALUES ('Escrita e Leitura');
INSERT INTO tag (tag_name) VALUES ('Comida e bebida');
INSERT INTO tag (tag_name) VALUES ('Engenharia');
INSERT INTO tag (tag_name) VALUES ('Infantil');

INSERT INTO event_organizer (id_user, id_event) VALUES (6,1);
INSERT INTO event_organizer (id_user, id_event) VALUES (10,3);
INSERT INTO event_organizer (id_user, id_event) VALUES (11,5);
INSERT INTO event_organizer (id_user, id_event) VALUES (13,10);
INSERT INTO event_organizer (id_user, id_event) VALUES (4,1);
INSERT INTO event_organizer (id_user, id_event) VALUES (4,2);
INSERT INTO event_organizer (id_user, id_event) VALUES (14,4);
INSERT INTO event_organizer (id_user, id_event) VALUES (3,6);
INSERT INTO event_organizer (id_user, id_event) VALUES (5,7);
INSERT INTO event_organizer (id_user, id_event) VALUES (9,8);
INSERT INTO event_organizer (id_user, id_event) VALUES (9,9);

INSERT INTO attendee (id_user, id_event) VALUES (9,1);
INSERT INTO attendee (id_user, id_event) VALUES (3,1);
INSERT INTO attendee (id_user, id_event) VALUES (8,5);
INSERT INTO attendee (id_user, id_event) VALUES (2,3);
INSERT INTO attendee (id_user, id_event) VALUES (9,3);
INSERT INTO attendee (id_user, id_event) VALUES (5,5);
INSERT into attendee (id_user, id_event) VALUES (3,2);
INSERT into attendee (id_user, id_event) VALUES (3,4);
INSERT into attendee (id_user, id_event) VALUES (8,1);
INSERT into attendee (id_user, id_event) VALUES (4,3);
INSERT into attendee (id_user, id_event) VALUES (4,5);
INSERT into attendee (id_user, id_event) VALUES (4,6);
INSERT into attendee (id_user, id_event) VALUES (9,2);


INSERT INTO choose_option (id_user, id_option) VALUES (9, 1);
INSERT INTO choose_option (id_user, id_option) VALUES (9, 4);
INSERT INTO choose_option (id_user, id_option) VALUES (3, 2);
INSERT INTO choose_option (id_user, id_option) VALUES (3, 3);
INSERT INTO choose_option (id_user, id_option) VALUES (2, 6);
INSERT INTO choose_option (id_user, id_option) VALUES (9, 5);
INSERT INTO choose_option (id_user, id_option) VALUES (2, 7);
INSERT INTO choose_option (id_user, id_option) VALUES (5, 7);


INSERT INTO event_tag (id_tag, id_event) VALUES (2,1);
INSERT INTO event_tag (id_tag, id_event) VALUES (3,1);
INSERT INTO event_tag (id_tag, id_event) VALUES (6,1);
INSERT INTO event_tag (id_tag, id_event) VALUES (14,1);
INSERT INTO event_tag (id_tag, id_event) VALUES (2,2);
INSERT INTO event_tag (id_tag, id_event) VALUES (9,2);
INSERT INTO event_tag (id_tag, id_event) VALUES (6,2);
INSERT INTO event_tag (id_tag, id_event) VALUES (28,2);
INSERT INTO event_tag (id_tag, id_event) VALUES (3,3);
INSERT INTO event_tag (id_tag, id_event) VALUES (6,3);
INSERT INTO event_tag (id_tag, id_event) VALUES (14,3);
INSERT INTO event_tag (id_tag, id_event) VALUES (28,3);
INSERT INTO event_tag (id_tag, id_event) VALUES (29,3);
INSERT INTO event_tag (id_tag, id_event) VALUES (4,4);
INSERT INTO event_tag (id_tag, id_event) VALUES (9,4);
INSERT INTO event_tag (id_tag, id_event) VALUES (2,5);
INSERT INTO event_tag (id_tag, id_event) VALUES (16,5);
INSERT INTO event_tag (id_tag, id_event) VALUES (19,5);
INSERT INTO event_tag (id_tag, id_event) VALUES (20,5);
INSERT INTO event_tag (id_tag, id_event) VALUES (2,6);
INSERT INTO event_tag (id_tag, id_event) VALUES (28,6);
INSERT INTO event_tag (id_tag, id_event) VALUES (30,6);
INSERT INTO event_tag (id_tag, id_event) VALUES (2,7);
INSERT INTO event_tag (id_tag, id_event) VALUES (5,7);
INSERT INTO event_tag (id_tag, id_event) VALUES (30,7);
INSERT INTO event_tag (id_tag, id_event) VALUES (28,7);
INSERT INTO event_tag (id_tag, id_event) VALUES (7,8);
INSERT INTO event_tag (id_tag, id_event) VALUES (8,8);
INSERT INTO event_tag (id_tag, id_event) VALUES (9,8);
INSERT INTO event_tag (id_tag, id_event) VALUES (15,8);
INSERT INTO event_tag (id_tag, id_event) VALUES (23,8);
INSERT INTO event_tag (id_tag, id_event) VALUES (26,8);
INSERT INTO event_tag (id_tag, id_event) VALUES (7,9);
INSERT INTO event_tag (id_tag, id_event) VALUES (8,9);
INSERT INTO event_tag (id_tag, id_event) VALUES (9,9);
INSERT INTO event_tag (id_tag, id_event) VALUES (15,9);
INSERT INTO event_tag (id_tag, id_event) VALUES (23,9);
INSERT INTO event_tag (id_tag, id_event) VALUES (26,9);
INSERT INTO event_tag (id_tag, id_event) VALUES (1,10);
INSERT INTO event_tag (id_tag, id_event) VALUES (13,10);
INSERT INTO event_tag (id_tag, id_event) VALUES (16,10);
INSERT INTO event_tag (id_tag, id_event) VALUES (17,10);
INSERT INTO event_tag (id_tag, id_event) VALUES (20,10);

----
INSERT INTO invite (id_event, id_invitee, id_organizer, accepted) VALUES (1,8,4, FALSE);
INSERT INTO invite (id_event, id_invitee, id_organizer, accepted) VALUES (3,8,10, FALSE);
INSERT INTO invite (id_event, id_invitee, id_organizer, accepted) VALUES (10,8,13, FALSE);
INSERT INTO invite (id_event, id_invitee, id_organizer, accepted) VALUES (5,5,11, TRUE);
INSERT INTO invite (id_event, id_invitee, id_organizer, accepted) VALUES (5,8,11, TRUE);
-----
INSERT INTO message (content, date, like_count, id_event, id_user) VALUES ('Boa noite, é possível levar o meu marido na visita? Ele é ex-sócio da associação. Obrigada', '2022-10-30 21:00:00', 1, 5, 8);
INSERT INTO message (content, date, like_count, id_event, id_user) VALUES ('Boa tarde, há lugares de refeições dentro do parque? Se sim, quais (o que servem?)', '2021-10-05 13:20:04', 0, 7, 3);
INSERT INTO message (content, date, like_count, id_event, id_user, parent) VALUES ('Boa noite, sim venham!' , '2022-10-30 21:10:00', 1, 5, 11, 1);
INSERT INTO message (content, date, like_count, id_event, id_user, parent) VALUES ('Tragam biscoitos', '2022-10-30 23:00:00', 2, 5, 8, 1);

INSERT INTO message_File (file, id_message) VALUES ('https://drive.google.com/file/d/1ew6LkiYFrDw5enUUaU47hNEgxGiPC5M_/view?usp=sharing', 3);

INSERT INTO notification (content, date, read, id_user, type, id_report, id_event, id_invitee, id_message) VALUES ('You have a new message!', '2022-10-30 21:00:00', FALSE, 3, 'Message', NULL, NULL, NULL, 1);
INSERT INTO notification (content, date, read, id_user, type, id_report, id_event, id_invitee, id_message) VALUES ('You have a new message!', '2022-10-30 21:00:00', FALSE, 9, 'Message', NULL, NULL, NULL, 1);
INSERT INTO notification (content, date, read, id_user, type, id_report, id_event, id_invitee, id_message) VALUES ('We have been invited!', '2021-10-05 13:20:04', FALSE, 2, 'Message', NULL, NULL, NULL, 2);
INSERT INTO notification (content, date, read, id_user, type, id_report, id_event, id_invitee, id_message) VALUES ('We have been invited!', '2021-10-05 13:20:04', FALSE, 9, 'Message', NULL, NULL, NULL, 2);

INSERT INTO vote (id_user, id_message) VALUES (11, 1);
INSERT INTO vote (id_user, id_message) VALUES (8, 3);
INSERT INTO vote (id_user, id_message) VALUES (11, 4);
INSERT INTO vote (id_user, id_message) VALUES (5, 4);
