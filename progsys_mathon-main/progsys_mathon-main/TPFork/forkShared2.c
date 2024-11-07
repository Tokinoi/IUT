/* --------------------------------------------------------------------------- 
 * Schéma de création de processus : création d'un fils, les deux processus
 * affichent simplement leur PID et PPID.
 * Auteur(s)    : Mathon Alonzo
 * Groupe de TP : 1G
 * --------------------------------------------------------------------------*/

#include <stdio.h>    /* Pour utiliser printf, perror, NULL... */
#include <stdlib.h>    /* Pour exit */
#include <unistd.h>    /* Pour fork, getpid, getppid */
#include <errno.h>
#include <sys/types.h>    /* Pour pid_t */
#include <sys/wait.h>    /* Pour wait */

int global=0;    /* La variable est globale POUR LE PROGRAMME, si je fork, il y a plusieurs variables globales  */


/* Fonctions exécutant le code propre au père ou au fils */
void pere(void);
void fils(void);

/* ---------------------------------------------------------------------------
 * Création d'un processus fils et exécution d'une fonction particulière
 * par chaque processus (père et fils).
 */
int main(void)
{
  pid_t ident;

  ident = fork();

  /* A partir de cette ligne, deux processus exécutent le code en parallèle */
  printf("Cette ligne va être affichée deux fois\n");


/*----------------------------------------------


Bonjour futur moi, comme je sais que tu auras de grande chance d'oublier je t'expliquer mes conclusions. 
    Il faut ouvrir le fichier mais pas dans la fonction car il y as deux accès. 
        Donc je doit refaire la question juste avant ^' 

Ce fichier parcontre est bon pour la fonction deux comme le fopen est après le fork. Donc tu dois juste refaire la question 4 et déplacer la réponse de la 4 dans la 5 

:)


-----------------------------------------------*/





//Création du premier fils 
  switch (ident) {
    case -1:
        //Erreur dans le fork
        perror("fork");
        return errno;
    case 0:
        //fonction exécuté par le premier fils 
        fils();
        return EXIT_SUCCESS;  
        break;
    default:

        pere();   
  }     
  
  return EXIT_SUCCESS;
}


/* Actions du processus père, regroupées dans une procédure. */
void pere(void)
{
    FILE* sharedFile = fopen("./sharedFile.txt", "w");
    for(int i=0;i<10000;i++)fprintf(sharedFile, "%d Je suis sataaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaan\n",i),fflush(sharedFile);

}


/* Actions du processus fils */
void fils(void)
{
    FILE* sharedFile = fopen("./sharedFile.txt", "w");
    for(int i=0;i<10000;i++)fprintf(sharedFile, "%d KOIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIN\n",i),fflush(sharedFile);
}
