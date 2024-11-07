--
-- Base de données: 'appro'
--

-- --------------------------------------------------------

--
-- Structure de la table 'commande'
--

CREATE TABLE commande (
  numero_cde int(11) NOT NULL AUTO_INCREMENT,
  date_cde date DEFAULT NULL,
  code_frs char(5) DEFAULT NULL,
  PRIMARY KEY (numero_cde),
  KEY i_fk_frs2 (code_frs)
) ENGINE=InnoDB ;


-- --------------------------------------------------------

--
-- Structure de la table 'fournir'
--

CREATE TABLE fournir (
  code_frs char(5) NOT NULL,
  ref_pdt char(5) NOT NULL,
  pu_achat decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (code_frs,ref_pdt),
  KEY i_fk_frs1 (code_frs),
  KEY i_fk_pdt1 (ref_pdt)
) ENGINE=InnoDB ;

--
-- Contenu de la table 'fournir'
--

INSERT INTO fournir (code_frs, ref_pdt, pu_achat) VALUES
('CSNO', 'CHOC', 2.30),
('CSNO', 'PICO', 4.30),
('CSNO', 'SAUSS', 4.50),
('CSNO', 'PATF', 2.50),
('CSNO', 'SMCH', 7.10),
('CSNO', 'PATEF', 1.60),
('CROUF', 'CHOC', 2.40),
('CROUF', 'SAUSS', 4.40),
('CROUF', 'PATE', 3.10),
('CROUF', 'PAIN', 0.80),
('CROUF', 'CDRR', 3.50),
('CROUF', 'PATEF', 1.50),
('LCLRC', 'SAUSS', 4.30),
('LCLRC', 'PICO', 4.50),
('LCLRC', 'PATF', 2.40),
('LCLRC', 'PATE', 3.20),
('LCLRC', 'PAIN', 0.75),
('AUCH', 'CHOC', 2.50),
('AUCH', 'PICO', 4.50),
('AUCH', 'PATF', 2.45),
('AUCH', 'SMCH', 6.90),
('AUCH', 'CDRR', 4.50),
('AUCH', 'PAIN', 0.75);

-- --------------------------------------------------------

--
-- Structure de la table 'fournisseur'
--

CREATE TABLE fournisseur (
  code_frs char(5) NOT NULL,
  nom char(30) DEFAULT NULL,
  PRIMARY KEY (code_frs)
) ENGINE=InnoDB ;

--
-- Contenu de la table 'fournisseur'
--

INSERT INTO fournisseur (code_frs, nom) VALUES
('CSNO', 'Casino'),
('CROUF', 'Carrefour'),
('LCLRC', 'Leclerc'),
('AUCH', 'Auchan');

-- --------------------------------------------------------

--
-- Structure de la table 'ligne_commande'
--

CREATE TABLE ligne_commande (
  numero_cde int(11) NOT NULL,
  ref_pdt char(5) NOT NULL,
  pu_cde decimal(8,2) DEFAULT '0.00',
  quantite int(11) DEFAULT '0',
  PRIMARY KEY (numero_cde,ref_pdt),
  KEY i_fk_cde (numero_cde),
  KEY i_fk_pdt2 (ref_pdt)
) ENGINE=InnoDB ;


-- --------------------------------------------------------

--
-- Structure de la table 'produit'
--

CREATE TABLE produit (
  ref_pdt char(5) NOT NULL,
  libelle char(64) DEFAULT NULL,
  PRIMARY KEY (ref_pdt)
) ENGINE=InnoDB ;

--
-- Contenu de la table 'produit'
--

INSERT INTO produit (ref_pdt, libelle) VALUES
('CHOC', 'Chocolat'),
('SAUSS', 'Saucisson'),
('PATE', 'Pâté de Campagne'),
('PATF', 'Pâté de foie'),
('PICO', 'Picodons x 3'),
('CDRR', 'Côtes-du-Rhône Rouge'),
('SMCH', 'Saumur-Champigny Rouge'),
('PAIN', 'Baguette'),
('PATEF', 'Pâte de fruits');


--
-- Contraintes pour la table `commande`
--
ALTER TABLE commande
  ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (code_frs) REFERENCES fournisseur (code_frs);

--
-- Contraintes pour la table `fournir`
--
ALTER TABLE fournir
  ADD CONSTRAINT fournir_ibfk_1 FOREIGN KEY (code_frs) REFERENCES fournisseur (code_frs),
  ADD CONSTRAINT fournir_ibfk_2 FOREIGN KEY (ref_pdt) REFERENCES produit (ref_pdt);

--
-- Contraintes pour la table `ligne_commande`
--
ALTER TABLE ligne_commande
  ADD CONSTRAINT ligne_commande_ibfk_1 FOREIGN KEY (numero_cde) REFERENCES commande (numero_cde),
  ADD CONSTRAINT ligne_commande_ibfk_2 FOREIGN KEY (ref_pdt) REFERENCES produit (ref_pdt);

-- Un jeu d'essai pour les commandes :
INSERT INTO commande (date_cde, code_frs) VALUES ('2022-10-18', 'AUCH');
INSERT INTO ligne_commande (numero_cde, ref_pdt, pu_cde, quantite)
VALUES ('1', 'CHOC', '2.50', '5'),
       ('1', 'PAIN', '0.75', '3');
INSERT INTO commande (date_cde, code_frs) VALUES ('2022-10-18', 'LCLRC');
INSERT INTO ligne_commande (numero_cde, ref_pdt, pu_cde, quantite)
VALUES ('2', 'PATE', '3.20', '2'),
       ('2', 'SAUSS', '4.30', '1');
INSERT INTO commande (date_cde, code_frs) VALUES ('2022-10-19', 'CROUF');
INSERT INTO ligne_commande (numero_cde, ref_pdt, pu_cde, quantite)
VALUES ('3', 'PAIN', '0.80', '2'),
       ('3', 'PATEF', '1.50', '3'),
       ('3', 'SAUSS', '4.40', '1');
