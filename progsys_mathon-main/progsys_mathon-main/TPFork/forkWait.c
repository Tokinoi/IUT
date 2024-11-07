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
        //fin du code éxécuté par le premier fils 
        break;
     default:
     printf("PID du fils mort --> %d\n",wait(0)); 
//Création du second fils 
            ident=fork();
            switch (ident) {
              case -1:
                //Erreur dans le fork
                perror("fork");
                return errno;           
              case 0:
                //Code éxécuté par le second fils 
                fils();
                return EXIT_SUCCESS;  
                //fin du code éxécuté par le second fils                     
              break;
              default:
              break;
            }
        //Le père attend que l'un de ses fils meurt
        printf("PID du fils mort --> %d\n",wait(0)); 
        pere();   
  }     
  
  return EXIT_SUCCESS;
}


/* Actions du processus père, regroupées dans une procédure. */
void pere(void)
{
  
  printf("Père --> PID = %d - PPID = %d\n", getpid(), getppid());
}

/* Actions du processus fils */
void fils(void)
{
  printf("Fils --> PID = %d - PPID = %d\n", getpid(), getppid());
}
