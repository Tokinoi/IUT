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
VALUES ('HADDOCK', 'Capitaine', '04 75 41 04 21'),
       ('SANZOT', 'Boucherie', '04 75 41 04 31'),
       ('TOURNESOL', 'Tryfon', '04 75 41 04 23"'),
       ('CASTAFIORE', 'Bianca', '04 75 58 09 22'),
       ('TINTTIN', 'et Milou', '04 75 41 04 22'),
       ('DUPONT', 'et DUPOND', '04 90 64 21 77');

-- Création de la table Administrateurs

CREATE TABLE Administrateurs (
     id INTEGER NOT NULL AUTO_INCREMENT,
     login VARCHAR(10) NOT NULL,
     mdp VARCHAR(64),
     PRIMARY KEY (id)
) CHARSET='utf8' COMMENT='Administrateurs des contacts';

-- 
-- Contenu de la table `Administrateurs`
-- 

INSERT INTO Administrateurs (login, mdp) VALUES ('admin', 'admin');
