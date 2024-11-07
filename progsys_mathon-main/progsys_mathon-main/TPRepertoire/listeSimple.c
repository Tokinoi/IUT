#include <dirent.h>
#include <sys/types.h>
#include <stdlib.h> 
#include <stdio.h>

int main(int nbArgs, char *args[])
{
    
    DIR *rep;
    rep = opendir(".");
    if (rep != NULL)
    {
        struct dirent *dir;
        dir = readdir(rep);
        while(dir != NULL){
            struct dirent *courant;
            printf("%ld %s \n",dir->d_ino,dir->d_name);
            dir=readdir(rep);
        }
        return 0;
    }
    printf("Impossible d'ouvrir le fichier");
    return -1;
}