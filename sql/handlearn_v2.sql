-- =============================================================
-- HandLearn — Migrazione v2 (idempotente)
-- Aggiunge:
--   - tabella relazione lezioni_segni (N-N)
--   - popolamento ricco di segni e collegamenti
-- Esegui DOPO handlearn.sql:
--   mysql -u root -p < sql/handlearn_v2.sql
-- =============================================================

USE handlearn;

-- -------------------------------------------------------------
-- N-N tra lezioni e segni
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lezioni_segni (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lezione_id  INT NOT NULL,
    segno_id    INT NOT NULL,
    ordine      INT DEFAULT 0,
    UNIQUE KEY unique_pair (lezione_id, segno_id),
    FOREIGN KEY (lezione_id) REFERENCES lezioni(id) ON DELETE CASCADE,
    FOREIGN KEY (segno_id)   REFERENCES segni(id)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Popolamento alfabeto (A-Z) come "segni" coerenti col modello AI
-- -------------------------------------------------------------
INSERT IGNORE INTO segni (parola, tipo, descrizione, immagine, categoria_id, livello) VALUES
    ('A','Lettera','Pugno chiuso, pollice di lato.',         'A',1,'principiante'),
    ('B','Lettera','Mano aperta, pollice piegato sul palmo.', 'B',1,'principiante'),
    ('C','Lettera','Mano curva a forma di C.',                'C',1,'principiante'),
    ('D','Lettera','Indice esteso, le altre dita chiuse a O.','D',1,'principiante'),
    ('E','Lettera','Dita piegate sul palmo, pollice sotto.',  'E',1,'principiante'),
    ('F','Lettera','Pollice e indice si toccano, le altre 3 dita estese.','F',1,'principiante'),
    ('G','Lettera','Pugno con indice e pollice paralleli.',   'G',1,'principiante'),
    ('H','Lettera','Indice e medio uniti ed estesi.',         'H',1,'principiante'),
    ('I','Lettera','Pugno con il mignolo esteso.',            'I',1,'principiante'),
    ('L','Lettera','Indice e pollice formano una L.',         'L',1,'principiante'),
    ('M','Lettera','Tre dita chiuse sopra il pollice.',       'M',1,'principiante'),
    ('N','Lettera','Due dita chiuse sopra il pollice.',       'N',1,'principiante'),
    ('O','Lettera','Dita curve a formare una O.',             'O',1,'principiante'),
    ('P','Lettera','Variante della K, rivolta verso il basso.','P',1,'principiante'),
    ('Q','Lettera','Variante della G, rivolta verso il basso.','Q',1,'principiante'),
    ('R','Lettera','Indice e medio incrociati.',              'R',1,'principiante'),
    ('S','Lettera','Pugno chiuso con il pollice davanti.',    'S',1,'principiante'),
    ('T','Lettera','Pugno con il pollice tra indice e medio.','T',1,'principiante'),
    ('U','Lettera','Indice e medio uniti, estesi in alto.',   'U',1,'principiante'),
    ('V','Lettera','Indice e medio aperti a V.',              'V',1,'principiante'),
    ('Z','Lettera','Indice che traccia una Z.',               'Z',1,'principiante');

-- Numeri (cat 2)
INSERT IGNORE INTO segni (parola, tipo, descrizione, immagine, categoria_id, livello) VALUES
    ('Uno',     'Numero','Indice esteso, altre dita chiuse.',  'Uno',2,'principiante'),
    ('Due',     'Numero','Indice e medio aperti.',             'Due',2,'principiante'),
    ('Tre',     'Numero','Indice, medio e pollice aperti.',    'Tre',2,'principiante'),
    ('Quattro', 'Numero','Quattro dita estese, pollice piegato.','Quattro',2,'principiante'),
    ('Cinque',  'Numero','Mano aperta, tutte le dita estese.', 'Cinque',2,'principiante'),
    ('Dieci',   'Numero','Pugno con il pollice esteso.',       'Dieci',2,'principiante');

-- Saluti (cat 3) - se non già esistono
INSERT IGNORE INTO segni (parola, tipo, descrizione, immagine, categoria_id, livello) VALUES
    ('Buongiorno','Saluto','Mano dalla fronte verso l''esterno.','Buongiorno',3,'principiante'),
    ('Buonasera', 'Saluto','Mano che scende leggermente, palmo aperto.','Buonasera',3,'principiante'),
    ('Arrivederci','Saluto','Mano aperta che saluta lateralmente.','Arrivederci',3,'principiante'),
    ('Per favore','Saluto','Palmo aperto, movimento circolare sul petto.','Per favore',3,'principiante'),
    ('Scusa',     'Saluto','Pugno chiuso, movimento circolare sul petto.','Scusa',3,'principiante');

-- -------------------------------------------------------------
-- Collegamento lezioni-segni
-- (uso INSERT IGNORE perché la coppia ha UNIQUE KEY)
-- -------------------------------------------------------------

-- Lezione 1 (Alfabeto A-M)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 1, id, 0 FROM segni WHERE parola IN ('A','B','C','D','E','F','G','H','I','L','M');

-- Lezione 2 (Alfabeto N-Z)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 2, id, 0 FROM segni WHERE parola IN ('N','O','P','Q','R','S','T','U','V','Z');

-- Lezione 3 (Numeri 1-10)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 3, id, 0 FROM segni WHERE parola IN ('Uno','Due','Tre','Quattro','Cinque','Dieci');

-- Lezione 5 (Saluti)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 5, id, 0 FROM segni WHERE parola IN ('Ciao','Grazie','Buongiorno','Buonasera','Arrivederci','Per favore','Scusa');

-- Lezione 7 (Famiglia)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 7, id, 0 FROM segni WHERE parola IN ('Mamma','Papà','Amico');

-- Lezione 8 (Emozioni)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 8, id, 0 FROM segni WHERE parola IN ('Felice','Triste','Amore');

-- Lezione 9 (Cibo)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id, ordine)
SELECT 9, id, 0 FROM segni WHERE parola IN ('Acqua','Pane','Caffè','Arancia');
