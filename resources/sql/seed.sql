create schema if not exists public;

SET search_path TO public;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS password_resets CASCADE;
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

CREATE TABLE password_resets (
    email      VARCHAR NOT NULL,
    token      VARCHAR NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);

-- Table: event

CREATE TABLE event (
    id     SERIAL PRIMARY KEY,
    title       TEXT NOT NULL,
    description TEXT,
    visibility  BOOLEAN NOT NULL,
    picture     TEXT,
    local       TEXT NOT NULL,
  	publish_date TIMESTAMP NOT NULL,
    start_date TIMESTAMP NOT NULL,
    final_date TIMESTAMP NOT NULL,
    is_canceled BOOLEAN
);


-- Table: poll

CREATE TABLE poll (
    id      SERIAL PRIMARY KEY,
    title       TEXT NOT NULL,
    description TEXT,
    date        TIMESTAMP NOT NULL,
    is_open      BOOLEAN NOT NULL DEFAULT (True),
    id_event     INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    id_user          INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE       
);


-- Table: report

CREATE TABLE report (
    id 	SERIAL PRIMARY KEY,
    id_event  	INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    id_manager   	INTEGER REFERENCES users (id) ON DELETE CASCADE,
    id_reporter  	INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    date     	TIMESTAMP NOT NULL,
    motive   	TEXT NOT NULL,
  	STATE    	reportState NOT NULL DEFAULT ('Pending')
);

-- Table: option

CREATE TABLE option (
    id	 SERIAL PRIMARY KEY,
    text     TEXT NOT NULL,
    id_poll   INTEGER NOT NULL REFERENCES poll ON DELETE CASCADE
);


-- Table: tag

CREATE TABLE tag (
    id   SERIAL PRIMARY KEY,
    tag_name TEXT UNIQUE NOT NULL
                    
);

-- Table: attendee
CREATE TABLE attendee (
    id_user      INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    id_event INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    PRIMARY KEY (
        id_user,
        id_event
    )
);


-- Table: choose_option

CREATE TABLE choose_option (
    id_user       INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    id_option INTEGER NOT NULL REFERENCES option ON DELETE CASCADE,
    PRIMARY KEY (
        id_user,
        id_option
    )
);



-- Table: event_organizer

CREATE TABLE event_organizer (
    id_user      INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    id_event INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    PRIMARY KEY (
        id_user,
        id_event
    )
);


-- Table: event_Tag

CREATE TABLE event_tag (
    id_tag   INTEGER NOT NULL REFERENCES tag (id) ON DELETE CASCADE,
    id_event INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    PRIMARY KEY (
        id_tag,
        id_event
    )
);


-- Table: invite

CREATE TABLE invite (
    id_event     INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    id_invitee   INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    id_organizer INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    accepted     BOOLEAN,
    to_attend    BOOLEAN NOT NULL DEFAULT(True),
    PRIMARY KEY (
        id_event,
        id_invitee
    )
);


-- Table: message

CREATE TABLE message (
    id SERIAL PRIMARY KEY,
    content      TEXT,
    date      TIMESTAMP NOT NULL,
    id_event   INTEGER NOT NULL REFERENCES event (id) ON DELETE CASCADE,
    id_user	   INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    parent    INTEGER REFERENCES message (id) ON DELETE CASCADE
);


-- Table: message_File

CREATE TABLE message_file (
    id    SERIAL PRIMARY KEY,
    file      TEXT,
    id_message INTEGER NOT NULL REFERENCES message (id) ON DELETE CASCADE
);


-- Table: notification

CREATE TABLE notification (
    id        SERIAL PRIMARY KEY,
    content   TEXT NOT NULL,
    date      TIMESTAMP NOT NULL,
    read      BOOLEAN NOT NULL DEFAULT (False),
    id_user    INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    type   notificationTypes,
    id_report  INTEGER REFERENCES report (id)  ON DELETE CASCADE CHECK ((id_report = NULL) or (id_report != NULL and type = 'Report')),
    id_event   INTEGER CHECK ((id_event = NULL) or (id_event != NULL and (type = 'Invite' OR type = 'Message'))),
    id_invitee INTEGER CHECK ((id_invitee = NULL) or (id_invitee != NULL and type = 'Invite')),
    id_message INTEGER REFERENCES message (id)  ON DELETE CASCADE CHECK ((id_message = NULL) or (id_message != NULL and type = 'Message')),
    FOREIGN KEY (
        id_event,
        id_invitee
    )
    REFERENCES invite ON DELETE CASCADE
);


-- Table: vote

CREATE TABLE vote (
    id_user        INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    id_message INTEGER NOT NULL REFERENCES message (id) ON DELETE CASCADE,
    PRIMARY KEY (
        id_user,
        id_message
    )
);


-----------------------------------------
-- LARAVEL INDEXES
-----------------------------------------

DROP INDEX IF EXISTS password_resets_email_index;
DROP INDEX IF EXISTS password_resets_token_index;

CREATE INDEX password_resets_email_index ON password_resets (email);
CREATE INDEX password_resets_token_index ON password_resets (token);

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
    INSERT INTO notification(content, date, id_user, type, id_message, id_event)
    VALUES (concat('New notification: ', NEW.content), NEW.date, 
            (SELECT event_organizer.id_user FROM event_organizer WHERE event_organizer.id_event = NEW.id_event), 'Message', NEW.id, (SELECT event_organizer.id_event FROM event_organizer WHERE event_organizer.id_event = NEW.id_event));
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
    IF OLD.accepted != TRUE AND NEW.accepted = TRUE AND NEW.to_attend = TRUE
    THEN
        INSERT INTO attendee (id_user, id_event) SELECT NEW.id_invitee, NEW.id_event;
    ELSEIF OLD.accepted != TRUE AND NEW.accepted = TRUE AND NEW.to_attend = FALSE
    THEN
        INSERT INTO event_organizer (id_user, id_event) SELECT NEW.id_invitee, NEW.id_event;
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


INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('David Bowie Tributo', 'É do conhecimento de todos os clientes que frequentam o Griffons, que não há uma noite em que as musicas do Bowie fiquem esquecidas e por isso todos os anos realizamos esta festa em memória de david Bowie. Em janeiro de 2023, faz 7 anos que o nosso ídolo musical nos deixou. A saudade ficou e as suas obras infinitas também. Por esta razão, no próximo Sábado, dia 14 de Janeiro de 2023 a noite será dedicada a David Bowie.
Neste tributo ao Bowie pretende-se passar grande parte dos temas realizados entre 1966 a 2016 num ambiente repleto de Fãs Bowie.
Nota: entrando no evento e clicando no botão discussão, partilhem as vossas músicas favoritas do Bowie para que no evento sejam ouvidas.', TRUE, 'bowie.jpg', '
Rua Conde de Vizela 95, 4050-640 Porto, Portugal' , '2022-10-01 10:00:00', '2022-10-05 10:00:00', '2022-10-05 16:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Festas de São Gonçalinho - Aveiro', 'Neste dia, visitaremos Aveiro, terra dos Moliceiros, das salinas e Ovos moles, para assistirmos a uma das mais remotas festividades portuguesas, as festas de São Gonçalinho..
Todos os anos, no fim de semana mais próximo de 10 de Janeiro, dia dedicado a São Gonçalinho, o mais típico bairro aveirense, o belo bairro da Beira-Mar, acolhe as tradicionais estas em honra ao Santo, promovidas pela Mordomia de São Gonçalinho..
Celebração muito peculiar, que desde há séculos diverte novos e menos novos, a festividade é marcada pelo pagamento de promessas ao Santo, agradecendo o seu poder de cura para as doenças, particularmente as ósseas, bem como a sua imensa capacidade de resolver problemas conjugais e amorosos, e de arranjar namoro para os encalhados..
Um dos pontos altos da esta são os muitos quilos de cavacas doces que se atiram da cúpula da Capela de São Gonçalinho, e que são recolhidas de todas as formas pelo muito público que assiste, com as mãos, com redes, guarda chuvas e qualquer tipo de objecto que sirva para o efeito..
Entre muitos outros destaques que ocorrem nesta esta surpreendente, de referir a delirante "Dança dos Mancos", assim como a Entrega do Ramo, acontecimento..
Venha desfrutar de um dia fantástico e muito divertido, a festejar o maior acontecimento de cultura popular aveirense, e levar com umas deliciosas cavacas nessa cabecinha !
Data: Sábado, 7 Janeiro.
Partida: 8h30 Tribunal da Relação do Porto (Cordoaria, nas escadas em frente ao Tribunal). Chegada pelas 20h.
Valor: 51€ (inclui viagem em autocarro, almoço com bebidas, visita à festa de São Gonçalinho e eventos associados do dia, Guia Alma At Porto e seguros).
Necessário reserva, inscrições limitadas.
Para inscrição, escolha a opção "bilhetes disponíveis", envie email para info@almaatporto.pt ou ligue 963 382 659.
Alma At Porto Marca Nacional Registada*
* Cancelamento de reservas até 15 dias antes da actividade.
** A actividade poderá ser adiada no caso de condições meteorológicas adversas.
Empresa Licenciada pelo Turismo de Portugal
Rnaat 428/2016
Rnavt 7882', TRUE, 'aveiro.jpg' , 'Aveiro' , '2022-09-29 01:00:00', '2022-11-29 21:00:00', '2022-11-29 03:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Carnaval 23 - Orquestra Bamba Social - Festa de lançamento do álbum "Mundo Novo"', 'Na comemoração da celebração de 10 anos da Orquestra Bamba Social, nada melhor que uma festa de Carnaval, lançamento do novo álbum "Mundo Novo" e uma atuação dupla da Orquestra Bamba Social no Super Bock Arena.
Será uma atuação no formato orquestra no palco e depois roda de samba no meio da plateia e com o público em redor da banda.
Para continuar a celebração carnavalesca Farofa (Dj set)
Bilhetes já à venda com o 1° lote a um preço promocional de 10€
Pontos de venda:workshop
- Ticketline.pt
- FNAC
- Worten', TRUE, 'orquestra.jpg', 'Super Bock Arena' , '2021-10-05 01:00:00', '2022-10-31 22:00:00', '2022-11-03 06:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Carolina Deslandes - Porto', 'Carolina Deslandes ao vivo no Porto. Concerto do ano!', TRUE, 'carolina.jpg', 'Super Bock Arena' , '2022-12-30 22:30:00',  '2023-12-07 21:30:00', '2023-12-08 01:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Dia do Animal', 'Visita de pessoas a abrigos de animais abandonados', FALSE, 'animal.jpeg', 'Camara de Lobos' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Mellow Mood - Mañana Tour', 'Mellow Mood lançam o seu sexto álbum, Mañana. Escrito e gravado entre 2020 e o início de 2022, é composto por 12 faixas, incluindo 7 colaborações internacionais. Aparece a este espetáculo musical!!', TRUE, 'mellow_mood.jpg', 'CAA Centro de Artes de Águeda' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Primavera Sound - Porto', 'Primavera Sound Porto 2023
7th June - 10th June.
Line-Up:
Shellac, Le Tigre, Karate
Primavera Sound Fan Page!', TRUE, 'primavera.jpg', 'Porto' , '2022-12-05 01:00:00', '2023-07-30 21:00:00', '2023-08-5 03:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Workshop Consciência & Filosofia Sistémica', 'Workshop de Consciência e Filosofia Sistémica
✔️ Sabias que as nossas origens poderão influenciar e interferir com o nosso destino?
A Psicogenealogia baseia-se no estudo do inconsciente colectivo familiar, percepcionar a influência da vida dos nossos familiares sobre a nossa a vida. 
Vem descobrir mais...', TRUE, 'filosofia.jpg', 'Lisboa - Clínica Essência da Alma Terapias' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('DEVIR no Iberanime Porto 2022', 'O Iberanime está de regresso à Exponor, em Matosinhos, e a DEVIR vai lá estar, com muitos jogos de tabuleiro e trading card games para experimentares, como não podia deixar de ser...
Além das mais recentes novidades do nosso catálogo, como The Red Cathedral Contractors e Get on Board, poderás jogar também Magic The Gathering e Yu-Gi-Oh!, tanto no formato tradicional, como Speed Duel, mais simples e mais rápido, para começares a jogar em instantes. Visita-nos! ', TRUE,'iberanime.jpg',  'Porto' , '2021-10-05 01:00:00', '2022-10-22 15:00:00', '2022-10-23 22:00:00');
INSERT INTO event (title, description, visibility, picture, local, publish_date, start_date, final_date) VALUES ('Pedro Abrunhosa - Porto', 'Pedro Abrunhosa ao vivo na Casa da Música no Porto pela celebração dos 125 Anos da Delegação do Porto da Cruz Vermelha Portuguesa.', TRUE, 'pedro.jpg', 'Casa da Música' , '2021-10-05 01:00:00', '2022-10-30 21:00:00', '2022-10-31 03:00:00');


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
INSERT INTO message (content, date, id_event, id_user) VALUES ('Boa noite, é possível levar o meu marido na visita? Ele é ex-sócio da associação. Obrigada', '2022-10-30 21:00:00', 5, 8);
INSERT INTO message (content, date, id_event, id_user) VALUES ('Boa tarde, há lugares de refeições dentro do parque? Se sim, quais (o que servem?)', '2021-10-05 13:20:04', 7, 3);
INSERT INTO message (content, date, id_event, id_user, parent) VALUES ('Boa noite, sim venham!' , '2022-10-30 21:10:00', 5, 11, 1);
INSERT INTO message (content, date, id_event, id_user, parent) VALUES ('Levo biscoitos!', '2022-10-30 23:00:00', 5, 8, 1);
INSERT INTO message (content, date, id_event, id_user) VALUES ('Posso levar o meu cão?', '2022-10-30 21:00:00', 5, 8);


INSERT INTO message_File (file, id_message) VALUES ('https://drive.google.com/file/d/1ew6LkiYFrDw5enUUaU47hNEgxGiPC5M_/view?usp=sharing', 3);


INSERT INTO vote (id_user, id_message) VALUES (11, 1);
INSERT INTO vote (id_user, id_message) VALUES (8, 3);
INSERT INTO vote (id_user, id_message) VALUES (11, 4);
INSERT INTO vote (id_user, id_message) VALUES (6, 4);
INSERT INTO vote (id_user, id_message) VALUES (5, 4);
