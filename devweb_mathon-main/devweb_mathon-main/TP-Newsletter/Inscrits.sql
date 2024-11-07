-- Création de la table Inscrits


CREATE TABLE Inscrits (
     login varchar(16) NOT NULL,
     nom varchar(64) NOT NULL,
     prénom varchar(64),
     mdp varchar(64),
     courriel varchar(128),
     validation varchar(64),
     rôle varchar(32), -- ROLE_UTILISATEUR, ROLE_REDACTEUR, ROLE_ADMIN
     PRIMARY KEY (login)
) CHARSET='utf8' COMMENT='Liste des inscrits';
-- 
-- Contenu de la table `Inscrits`
-- 
-- Laisser vide le champ `validation` et mettre des adresses valides pour les tests (adresses UGA 
-- pour ROLE_REDACTEUR et ROLE_ADMINISTRATEUR)
INSERT INTO Inscrits (login, nom, prénom, mdp, courriel, validation, rôle) 
VALUES ('admin', 'admin', 'admin', 'admin', 'admin@admin', '', 'ROLE_ADMIN'),
       ('redac', 'redac', 'redac', 'redac', 'redac@redac', '', 'ROLE_REDACTEUR'),
       ('user', 'user', 'user', 'user', 'user@user', '', 'ROLE_UTILISATEUR');

