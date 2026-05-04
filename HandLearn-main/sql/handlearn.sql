-- =============================================================
-- HandLearn - Schema database
-- Esegui questo file in phpMyAdmin o da CLI:
--   mysql -u root -p < sql/handlearn.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS handlearn
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE handlearn;

-- -------------------------------------------------------------
-- Tabella utenti
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utenti (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(50)  NOT NULL,
    cognome       VARCHAR(50)  NOT NULL,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    token_reset   VARCHAR(64)  DEFAULT NULL,
    livello       ENUM('principiante','intermedio','avanzato') DEFAULT 'principiante',
    xp            INT NOT NULL DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Tabella categorie del dizionario / lezioni
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorie (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    slug   VARCHAR(50) NOT NULL UNIQUE,
    nome   VARCHAR(80) NOT NULL,
    icona  VARCHAR(10) DEFAULT NULL
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Tabella segni del dizionario
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS segni (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    parola        VARCHAR(80) NOT NULL,
    tipo          VARCHAR(40) DEFAULT 'Sostantivo',
    descrizione   TEXT,
    immagine      VARCHAR(20) DEFAULT '🤟',
    video_url     VARCHAR(255) DEFAULT NULL,
    categoria_id  INT DEFAULT NULL,
    livello       ENUM('principiante','intermedio','avanzato') DEFAULT 'principiante',
    FOREIGN KEY (categoria_id) REFERENCES categorie(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Tabella lezioni
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lezioni (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    titolo        VARCHAR(120) NOT NULL,
    descrizione   TEXT,
    icona         VARCHAR(10) DEFAULT '📘',
    durata_min    INT DEFAULT 10,
    livello       ENUM('principiante','intermedio','avanzato') DEFAULT 'principiante',
    categoria_id  INT DEFAULT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorie(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Tabella progressi utente (lezioni completate)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS progressi (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    utente_id   INT NOT NULL,
    lezione_id  INT NOT NULL,
    percentuale INT DEFAULT 0,
    completata  TINYINT(1) DEFAULT 0,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_lesson (utente_id, lezione_id),
    FOREIGN KEY (utente_id)  REFERENCES utenti(id)  ON DELETE CASCADE,
    FOREIGN KEY (lezione_id) REFERENCES lezioni(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Tabella punteggi giochi
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS punteggi (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    utente_id   INT NOT NULL,
    gioco       VARCHAR(50) NOT NULL,
    punteggio   INT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================================
-- DATI DI ESEMPIO
-- =============================================================

INSERT INTO categorie (slug, nome, icona) VALUES
    ('alfabeto',   'Alfabeto',     '🅰️'),
    ('numeri',     'Numeri',       '🔢'),
    ('saluti',     'Saluti',       '👋'),
    ('famiglia',   'Famiglia',     '👨‍👩‍👧'),
    ('cibo',       'Cibo',         '🍎'),
    ('emozioni',   'Emozioni',     '😊'),
    ('conversazione','Conversazione','💬')
ON DUPLICATE KEY UPDATE nome=VALUES(nome);

INSERT INTO lezioni (titolo, descrizione, icona, durata_min, livello, categoria_id) VALUES
    ('L''Alfabeto LIS - Parte 1','Lettere dalla A alla M','🅰️',15,'principiante',1),
    ('L''Alfabeto LIS - Parte 2','Lettere dalla N alla Z','🅱️',15,'principiante',1),
    ('I Numeri da 1 a 10','Impara a contare in LIS','1️⃣',10,'principiante',2),
    ('I Numeri da 11 a 100','Numeri più grandi e quantità','🔟',12,'principiante',2),
    ('Saluti Quotidiani','Ciao, buongiorno, arrivederci','👋',8,'principiante',3),
    ('Presentarsi','Mi chiamo, piacere, come ti chiami?','🙋',10,'principiante',3),
    ('La Famiglia','Mamma, papà, fratello, sorella','👨‍👩‍👧',12,'principiante',4),
    ('Le Emozioni Base','Felice, triste, arrabbiato, stanco','😊',15,'intermedio',6),
    ('Cibo e Bevande','Pane, acqua, caffè, frutta','🍎',15,'intermedio',5),
    ('Frasi Complesse','Costruire frasi soggetto + verbo','💬',25,'avanzato',7);

INSERT INTO segni (parola, tipo, descrizione, immagine, categoria_id, livello) VALUES
    ('Acqua',   'Sostantivo','Indicare con la mano un movimento ondulatorio.','💧',5,'principiante'),
    ('Amore',   'Sostantivo','Mani incrociate sul cuore.','💙',6,'principiante'),
    ('Amico',   'Sostantivo','Mani che si stringono.','🤝',3,'principiante'),
    ('Abitare', 'Verbo',     'Tetto formato con le mani sopra la testa.','🏠',NULL,'intermedio'),
    ('Aereo',   'Sostantivo','Mano a forma di aereo che si sposta in avanti.','✈️',NULL,'principiante'),
    ('Albero',  'Sostantivo','Avambraccio verticale e mano aperta.','🌳',NULL,'principiante'),
    ('Animale', 'Sostantivo','Movimento delle dita davanti al naso.','🐶',NULL,'principiante'),
    ('Anno',    'Sostantivo','Pugno chiuso che ruota in avanti.','📅',NULL,'intermedio'),
    ('Arancia', 'Sostantivo','Mano che spreme un agrume.','🍊',5,'principiante'),
    ('Auto',    'Sostantivo','Mani che simulano un volante.','🚗',NULL,'principiante'),
    ('Arte',    'Sostantivo','Pennello immaginario sulla mano aperta.','🎨',NULL,'intermedio'),
    ('Adesso',  'Avverbio',  'Mani che indicano il presente verso il basso.','⏰',NULL,'principiante'),
    ('Ciao',    'Saluto',    'Mano aperta con piccolo movimento laterale.','👋',3,'principiante'),
    ('Grazie',  'Saluto',    'Punta delle dita dal mento in avanti.','🙏',3,'principiante'),
    ('Mamma',   'Sostantivo','Pollice e indice toccano il mento.','👩',4,'principiante'),
    ('Papà',    'Sostantivo','Pollice e indice toccano la fronte.','👨',4,'principiante'),
    ('Felice',  'Aggettivo', 'Mani aperte che salgono dal petto verso l''alto.','😊',6,'principiante'),
    ('Triste',  'Aggettivo', 'Mani che scendono lungo le guance.','😢',6,'principiante'),
    ('Pane',    'Sostantivo','Mani che simulano taglio del pane.','🍞',5,'principiante'),
    ('Caffè',   'Sostantivo','Pugno che ruota sopra l''altro pugno.','☕',5,'principiante');
