/* ---------------------------------------------------------------------------
 * Programme affichant une voiture qui parcours une route
 * Auteur(s)  : 
 * Groupe TP  : 
 * Entrées    : Aucune
 * Sorties    : Affichage d'une fenêtre graphique
 * Avancement : <Où en êtes vous ? Qu'est-ce qui fonctionne ?>
 */

#include "road.h"

#define _GNU_SOURCE  // For pthread_tryjoin_np
#include <unistd.h>  // For usleep
#include <pthread.h> // For threading in general
#include <errno.h>   // For EBUSY
#include <stdlib.h>  // For EXIT_FAILURE
#include <stdio.h>   // Just in case

void* avancer();
int carId;
int main(int argc, const char *argv[])
{
   road_init(0);

   carId = road_addCar(0);

   if (carId == -1) {
      fprintf(stderr, "Something went wrong while trying to add a car on the road. Exiting.\n");
      return EXIT_FAILURE;
   }
   pthread_t th;
   pthread_create(&th, NULL, avancer, NULL);
   

   // Update display and make car move until Esc is pressed
   while (!road_isEscPressed() && pthread_tryjoin_np(th,NULL))
   {
      road_refresh();
   }

   // Clean shutdown
   road_shutdown();

   return 0;
}

void* avancer(){
   while (road_stepCar(carId))
   {
      usleep(500);
      
   }
   road_removeCar(carId);
}


