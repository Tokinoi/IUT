# Évaluation de la qualité de la preuve de concept

Vous répondrez aux questions suivantes à propos de la preuve de concept fournie en vous appuyant sur des éléments concrets :

* Comment évaluez vous la maintenabilité du code fourni ? (Est-il facile de prendre en main le fonctionnement des différentes parties du code ?)
* Comment évaluez vous la testabilité du code fourni ? (Est-il possible de tester la partie simulation sans la partie graphique ?)


## Maintenabilité
### Alonzo 
Très compliqué...
Aucune constante ne sont utilisé. On donne la responsabilité du déplacement dans la gestion de l'écran. Les variables
n'ont pas des noms parlant 
### Mathieu
Code pas "uniforme", c'est à dire que pour parties "identique", c'est fait de plusieurs manières différente
#### Exemple
`200 as usize` et `3f32` : deux écriture différente qui font la même chose (selon valeur et type) utilisées
## Testabilité
### Alonzo
Impossible à tester. Tout est un amat de fonctionnalité qui sont emmeler les une des autres.

### Mathieu

aucun test n'est implémenté, on ne peut pas savoir ce qui fonctionne correctement.
