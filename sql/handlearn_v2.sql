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
    UNIQUE KEY unique_pair (lezione_id, segno_id),
    FOREIGN KEY (lezione_id) REFERENCES lezioni(id) ON DELETE CASCADE,
    FOREIGN KEY (segno_id)   REFERENCES segni(id)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- Popolamento alfabeto (A-Z) come "segni" coerenti col modello AI
-- -------------------------------------------------------------
INSERT IGNORE INTO segni (parola, tipo, descrizione, immagine, categoria_id, livello) VALUES
    ('A','Lettera','Pugno chiuso, pollice di lato.',          NULL,1,'principiante'),
    ('B','Lettera','Mano aperta, pollice piegato sul palmo.', NULL,1,'principiante'),
    ('C','Lettera','Mano curva a forma di C.',                NULL,1,'principiante'),
    ('D','Lettera','Indice esteso, le altre dita chiuse a O.',NULL,1,'principiante'),
    ('E','Lettera','Dita piegate sul palmo, pollice sotto.',  NULL,1,'principiante'),
    ('F','Lettera','Pollice e indice si toccano, le altre 3 dita estese.',NULL,1,'principiante'),
    ('G','Lettera','Pugno con indice e pollice paralleli.',   NULL,1,'principiante'),
    ('H','Lettera','Indice e medio uniti ed estesi.',         NULL,1,'principiante'),
    ('I','Lettera','Pugno con il mignolo esteso.',            NULL,1,'principiante'),
    ('L','Lettera','Indice e pollice formano una L.',         NULL,1,'principiante'),
    ('M','Lettera','Tre dita chiuse sopra il pollice.',       NULL,1,'principiante'),
    ('N','Lettera','Due dita chiuse sopra il pollice.',       NULL,1,'principiante'),
    ('O','Lettera','Dita curve a formare una O.',             NULL,1,'principiante'),
    ('P','Lettera','Variante della K, rivolta verso il basso.',NULL,1,'principiante'),
    ('Q','Lettera','Variante della G, rivolta verso il basso.',NULL,1,'principiante'),
    ('R','Lettera','Indice e medio incrociati.',              NULL,1,'principiante'),
    ('S','Lettera','Pugno chiuso con il pollice davanti.',    NULL,1,'principiante'),
    ('T','Lettera','Pugno con il pollice tra indice e medio.',NULL,1,'principiante'),
    ('U','Lettera','Indice e medio uniti, estesi in alto.',   NULL,1,'principiante'),
    ('V','Lettera','Indice e medio aperti a V.',              NULL,1,'principiante'),
    ('Z','Lettera','Indice che traccia una Z.',               NULL,1,'principiante');

-- Numeri (cat 2)
INSERT IGNORE INTO segni (parola, tipo, descrizione, immagine, categoria_id, livello) VALUES
    ('Uno',     'Numero', 'Mano chiusa a pugno con solo il dito indice esteso.', NULL, 2, 'principiante'),
    ('Due',     'Numero', 'Dita indice e medio estese a formare una V, le altre chiuse.', NULL, 2, 'principiante'),
    ('Tre',     'Numero', 'Pollice, indice e medio estesi, anulare e mignolo chiusi.', NULL, 2, 'principiante'),
    ('Quattro', 'Numero', 'Indice, medio, anulare e mignolo estesi, pollice piegato sul palmo.', NULL, 2, 'principiante'),
    ('Cinque',  'Numero', 'Mano aperta, tutte e cinque le dita estese e separate.', NULL, 2, 'principiante'),
    ('Sei',     'Numero', 'Pollice e indice estesi a forma di L, con movimento rotatorio del polso.', NULL, 2, 'principiante'),
    ('Sette',   'Numero', 'Pollice, indice e medio estesi, con movimento rotatorio del polso.', NULL, 2, 'principiante'),
    ('Otto',    'Numero', 'Pollice, indice, medio e anulare estesi, con movimento rotatorio del polso.', NULL, 2, 'principiante'),
    ('Nove',    'Numero', 'Mano aperta con tutte le dita estese, con movimento rotatorio del polso.', NULL, 2, 'principiante'),
    ('Dieci',   'Numero', 'Mano aperta (cinque) che esegue una rapida oscillazione laterale a scatto del polso.', NULL, 2, 'principiante');


-- -------------------------------------------------------------
-- Collegamento lezioni-segni
-- (uso INSERT IGNORE perché la coppia ha UNIQUE KEY)
-- -------------------------------------------------------------

-- Lezione 1 (Alfabeto A-M)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 1, id FROM segni WHERE parola IN ('A','B','C','D','E','F','G','H','I','L','M');

-- Lezione 2 (Alfabeto N-Z)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 2, id FROM segni WHERE parola IN ('N','O','P','Q','R','S','T','U','V','Z');

-- Lezione 3 (Numeri 1-10)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 3, id FROM segni WHERE parola IN ('Uno','Due','Tre','Quattro','Cinque','Dieci');

-- Lezione 5 (Saluti)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 5, id FROM segni WHERE parola IN ('Ciao','Grazie','Buongiorno','Buonasera','Arrivederci','Per favore','Scusa');

-- Lezione 7 (Famiglia)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 7, id FROM segni WHERE parola IN ('Mamma','Papà','Amico');

-- Lezione 8 (Emozioni)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 8, id FROM segni WHERE parola IN ('Felice','Triste','Amore');

-- Lezione 9 (Cibo)
INSERT IGNORE INTO lezioni_segni (lezione_id, segno_id)
SELECT 9, id FROM segni WHERE parola IN ('Acqua','Pane','Caffè','Arancia');

DELETE s1 FROM segni s1
INNER JOIN segni s2 
WHERE s1.id > s2.id 
AND LOWER(s1.parola) = LOWER(s2.parola);
ALTER TABLE segni ADD UNIQUE KEY unique_parola (parola);

ALTER TABLE lezioni_segni
ADD COLUMN ordine INT NOT NULL DEFAULT 0;