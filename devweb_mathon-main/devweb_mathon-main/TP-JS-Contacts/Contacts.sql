-- Création de la table Contacts

CREATE TABLE Contacts (
     id INTEGER NOT NULL AUTO_INCREMENT,
     nom VARCHAR(64) NOT NULL,
     prénom VARCHAR(64),
     tél VARCHAR(16),
     PRIMARY KEY (id)
) CHARSET='utf8' COMMENT='Liste des contacts';

-- 
-- Contenu de la table `Contacts`
-- 

INSERT INTO Contacts (nom, prénom, tél) 
VALUES ('RITCHIE', 'Dennis', '04 75 41 04 21'),
       ('THOMSON', 'Ken', '04 75 41 04 31'),
       ('LOVELACE', 'Ada', '04 75 41 04 23"'),
       ('COLMERAUER', 'Alain', '04 75 58 09 22'),
       ('LAMPORT', 'Leslie', '04 75 41 04 22'),
       ('KNUTH', 'Donald', '04 90 64 21 77');

