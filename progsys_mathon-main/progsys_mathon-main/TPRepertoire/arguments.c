#include <dirent.h>
#include <sys/types.h>
#include <stdlib.h> 
#include <stdio.h>
#include <stdbool.h>
#include <string.h>

void affRepertoire(char* dirName);
void createTab(char* dirName);
int cmpstringp(const void *p1, const void *p2);





int main(int argc, char* argv[]){
    createTab(argv[1]);
}









void affRepertoire(char* dirName){
    DIR *rep;
    rep = opendir(dirName);
    if (rep != NULL)
    {
        struct dirent *dir;
        dir = readdir(rep);
        while(dir != NULL){
            printf("%ld %s \n",dir->d_ino,dir->d_name);
            struct dirent *courant;
            dir=readdir(rep);
        }
    }
}






void createTab(char* dirName){
    DIR *rep;
    rep = opendir(dirName);
    if (rep != NULL)
    {
        int nbE = 0;
        struct dirent *dir;
        dir = readdir(rep);

        while(dir != NULL)
        {
            nbE = nbE+1;
            dir = readdir(rep);
        }

        char** table = (char**) malloc(nbE * sizeof(char*));
        rewinddir(rep);
        dir = readdir(rep);
        int place = 0;

        while(dir != NULL) 
        {                          
            char* element;
            int taille = strlen(dir->d_name);
            element = (char*) malloc( (taille+1) * sizeof(char));
            table[place] = strcpy(element, dir->d_name);
            place = place+1;
            dir = readdir(rep);
        }

        qsort(table,nbE,sizeof(char *),cmpstringp);
        for(int i=0;i<nbE;i++){
            printf("%s\n",table[i]);
        }
        
        for(int i=0;i<nbE;i++){
            free(table[i]);
        }
        free(table);
    }



}



int cmpstringp(const void *p1, const void *p2){
    return strcmp(* (char * const *) p1, * (char * const *) p2);
}