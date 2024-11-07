# Retour 

## les plus : 
- beaucoup de boulot, supplémentaire non demandé
- jeu joli, soigné, original
- code bien sur certains aspects

## les moins :
- quasiment aucun des points demandés par les TPs (pas de gameplay asteroid, pas de fichier des description des planets ...)
- encore des efforts de qualité de dev à faire (ex : menu principal -> showLines() : pourquoi ne pas utiliser un groupe ? (point faible identifié), répétition de la scène planete (point faible non identifié)...) 
- traduction des boutons mais pas des descriptions ? 
- aucune trace du keybind (ni dans le jeu, ni dans le code), vous voulez dire l'utilisation des actions ?

## notation: 
probablement un léger bonus pour jordan. la quantité de travail suplémentaire de mathieu est appréciée, mais c'est aussi la ou j'ai vu le plus de problèmes.


# Rapport Projet Godot

## Contributions individuelles

- Jordan (35 %) : Conception et développement de la logique générale du jeu (mort du joueur et des ennemies, spawn des ennemies, les mouvements, le lvl up, l’HP…). Intégration de certains sound effects et de la musique du menu. Ajout progressive de différents éléments au gameplay (différents types d’attaques, différents types d'ennemis...). Ajout de shaders (trou noir, couleur inversé pour grenade). Ajout de particules (explosion, atmosphere). RigidBody2D pour le joueur et les asteroids.

- Alonzo (15%) : Ajout de nouveaux éléments au gameplay (mines, etc..). Ajout de la personnalisation des touches (keybind).

- Aymane (15%) : Ajout de la traduction de l’ensemble des boutons (anglais/français). Ajout du boost rechargeable avec le temps. Intégration de certains sound effects.

- Mathieu (35%): 
Forces : Intégration des différents menus (menu de départ, menu choix de planètes, menu de mort...). Intégration du système solaire en 3D et des éléments d'interface associés. Gestion d'effet de transition entre différents menus, les différentes vues des planètes et le jeu. Amélioration de l’ambiance générale du menu (luminosité et orbitation des planètes etc..).

## Organisation projet : 
Le nommage des répertoires est plutôt clair : les différentes scènes en lien avec le menu sont dans le dossier Menu. Les différentes entités du jeu (y compris le joueur lui-même) sont présentes dans le répertoire Entities. Les différents scripts utilisés pour coder la logique du jeu sont dans le dossier Scripts…

## Évaluation des forces et faiblesses

### Forces
- Nommage et organisation : Les variables et fonctions sont clairement nommées, la structure du projet est bien organisée.
- Compétences de programmation de base : Pas de duplications de code, fonctions généralement simples et pas trop chargées, conditions plutôt compréhensibles…
- Utilisation pertinente des concepts de Godot : variables globales (autoloads), événements, actions des boutons, gestion des scènes, signaux/observateur, instanciation, destruction etc...
- Qualité graphique et sonore : Présence d’un thème logique (science-fiction), utilisation de ressources libres de droits (sound effects…), shaders, particules GPU, etc...

### Faiblesses
- Originalité : On a peut être été limité niveau originalité. On sent qu’on propose un jeu, certes plutôt complet, mais assez classique.
- Manque de documentation : On a pas eu le temps (ni l’envie, faut être honnêtes !…) de faire une documentation du jeu pour les futurs devs du jeu.
- Certaines parties du code sont dupliquée (cf. hideLines/showLines). Dans le menu "planet_menu", quand on clique sur un des boutons de planète une caméra fait le focus sur la planète choisie, mais en réalité, il y a plusieurs caméras au lieu d'une seule que l'on déplace.

### Informations supplémentaires
Pour jouer au mode de jeu "TP", il faut cliquer sur le bouton "PlayRoid".
Pour jouer à notre mode de jeu, il faut cliquer sur le bouton "Play".
Le jeu deviens de plus en plus dur avec le temps, mais chaque niveaux supplémentaires donnent des pouvoirs...

Bonne correction !

