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

int main(int argc, const char *argv[])
{
   road_init(0);

   int carId = road_addCar(0);

   if (carId == -1)
   {
      fprintf(stderr, "Something went wrong while trying to add a car on the road. Exiting.\n");
      return EXIT_FAILURE;
   }

   // Update display and make car move until Esc is pressed
   while (!road_isEscPressed())
   {
      usleep(1000);
      road_refresh();
      road_stepCar(carId);
   }

   road_removeCar(carId);

   // Clean shutdown
   road_shutdown();

   return 0;
}
