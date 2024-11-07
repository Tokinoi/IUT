# Réponses au question du TP Fork 

Autheur : 
    - Alonzo Mathon 
Groupe : 
    - 1G

# Exercice 2

## Question 1:

    le PPID du fils seras toujours le même que le PID du père. Le PID sert à identifier les processus et le PPID sert à identifier le créateur du processus. Comme le père créer le fils, il prend donc le comme PPID le PID du père.     

## Question 2:

    Le PPID du fils quand le père meurt change. Il __semble__ toujours prendre la valeur 1547. (même si l'on change de terminal).

## Question 3:

    Le PPID du père correspond à celui qui le lance. Dans mon cas le terminal.
    Lorsque le père meurt, le PPID du fils correspond à *lib/systemd/systemd --user*
    Si le père ne meurt pas, le PPID du fils correspond au PIf du père. 

# Exercice 3

## Question 1:

    La variable globale n'est pas la même pour le fichier père que le fichier fils. On l'as dupliqué en même temps pendant le fork. Quand on fait les itérations, le fils semble toujours commencer en avance. On pourrait l'expliquer par sa place dans le code. Comme il est plus haut, il commence ses itérations avant le père. 

# Exercice 4 

## Question 1:
    Le résultat de l'écriture alterne assez régulièrement entre le père et le fils sans jamais tronquer les phrase de l'un où de l'autre. Les 20.000 messages sont bien présents. Le usleep changeras le nombre de ligne du fils qui se trouve en début de fichier. (Le fils à plus de temps avant de devoir partager l'accès)


## Question 2: 
    Non, deux chaîne affiché par un processus ne sont pas toujours contigue mais il ne semble jamais se couper.

    1 accès -> Il alterne pour écrire leur phrase sans se tirer dans les pattes.

# Exercice 5

## Question 1: 
    Non les processus ne réalisent pas tous leurs affichage (entre 10.000 et 11.000 au total à la place de 20.000)

## Question 2:
    Oui, il y as un mélange des affichages et même plus important car maintenant il se tronque l'un l'autre. 

## Question 3:
    Comme il y as deux accès, les fichiers ne prennent plus compte l'un de l'autre et "écrasse" ce que l'autre à fait 
## Question 4
    Le père semble écrire plus souvent que le fils. 


    2 accès -> les processus s'empèche l'un l'autre d'écrire et/ou écrasse ce que l'autre à écrit. 