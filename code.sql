-- Fichier généré par SQLiteStudio v3.4.13 sur ven. mars 28 10:17:40 2025
-- Encodage texte utilisé : UTF-8

BEGIN TRANSACTION;

-- Tableau : commentaires
CREATE TABLE IF NOT EXISTS commentaires (
    commentaire_id INTEGER PRIMARY KEY AUTOINCREMENT, 
    histoire_id INTEGER, 
    pseudo TEXT NOT NULL, 
    contenu TEXT NOT NULL, 
    date_creation DATE,
    FOREIGN KEY (histoire_id) REFERENCES VDE (histoire_id)
);

-- Tableau : VDE
CREATE TABLE IF NOT EXISTS VDE (
    histoire_id INTEGER PRIMARY KEY AUTOINCREMENT, 
    date_creation DATE, 
    pseudo TEXT NOT NULL, 
    contenu TEXT NOT NULL
);

COMMIT TRANSACTION;
