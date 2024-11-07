#include <dirent.h>
#include <sys/types.h>
#include <stdlib.h> 
#include <stdio.h>
#include <stdbool.h>
#include <string.h>
#include <sys/stat.h>

void affRepertoire(char* dirName);
void createTab(char* dirName);
int cmpstringp(const void *p1, const void *p2);





int main(int argc, char* argv[]){
    createTab(argv[1]);
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
            element = (char*) malloc( (taille) * sizeof(char));
            table[place] = strcpy(element, dir->d_name);
            place = place+1;
            dir = readdir(rep);
        }

        qsort(table,nbE,sizeof(char *),cmpstringp);
        for(int i=0;i<nbE;i++){
            struct stat st;
            stat(table[i], &st);
            printf("%s : %li bytes\n",table[i],st.st_size);
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