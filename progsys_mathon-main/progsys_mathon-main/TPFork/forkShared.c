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
void pere(FILE*);
void fils(FILE*);

/* ---------------------------------------------------------------------------
 * Création d'un processus fils et exécution d'une fonction particulière
 * par chaque processus (père et fils).
 */
int main(void)
{
  pid_t ident;
  FILE* sharedFile = fopen("./sharedFile.txt", "w");
  ident = fork();

  /* A partir de cette ligne, deux processus exécutent le code en parallèle */
  printf("Cette ligne va être affichée deux fois\n");


//Création du premier fils 
  switch (ident) {
    case -1:
        //Erreur dans le fork
        perror("fork");
        return errno;
    case 0:
        //fonction exécuté par le premier fils 
        fils(sharedFile);
        return EXIT_SUCCESS;  
        break;
    default:
        usleep(100);
        pere(sharedFile);   
  }     
  
  return EXIT_SUCCESS;
}


/* Actions du processus père, regroupées dans une procédure. */
void pere(FILE* sharedFile)
{
    for(int i=0;i<10000;i++)fprintf(sharedFile, "Je suis sataaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaan\n"),fflush(sharedFile);

}


/* Actions du processus fils */
void fils(FILE* sharedFile)
{
    for(int i=0;i<10000;i++)fprintf(sharedFile, "KOIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIN\n"),fflush(sharedFile);
}
