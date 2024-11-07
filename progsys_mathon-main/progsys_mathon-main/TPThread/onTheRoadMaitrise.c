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
#include <allegro5/allegro.h>

void *avancer(void *argument);
void *creerVoiture(void *arg);
struct arg
{
   int carId;
   int *nbCar;
   bool *stoped;
   ALLEGRO_KEYBOARD_STATE *keys;
} arg;

struct argu
{
   bool *stoped;
   ALLEGRO_KEYBOARD_STATE *keys;
} argu;

int main(int argc, const char *argv[])
{
   ALLEGRO_KEYBOARD_STATE keys;
   bool stoped = false;
   srand(time(NULL));
   road_init(0);
   struct argu argument;
   argument.stoped = &stoped;
   argument.keys = &keys;

   pthread_t th;
   pthread_create(&th, NULL, creerVoiture, &argument);

   // Update display and make car move until Esc is pressed
   while (!road_isEscPressed() && pthread_tryjoin_np(th, NULL))
   {
      if (al_key_down(&keys, ALLEGRO_KEY_P))
      {
         stoped = !stoped;
         while (al_key_down(&keys, ALLEGRO_KEY_P))
         {
            al_get_keyboard_state(&keys);
         }
      }
      road_refresh();
   }
   // Clean shutdown
   road_shutdown();

   return 0;
}
void *creerVoiture(void *arg)
{
   struct argu *input = (struct argu *)arg;
   ALLEGRO_KEYBOARD_STATE *keys;
   keys = (ALLEGRO_KEYBOARD_STATE *)input->keys;
   bool *stoped = input->stoped;
   int nbCar = 0;
   while (true)
   {
      while (*stoped)
      {
         al_get_keyboard_state(keys); //Je ne sais pas pouquoi je dois le mettre ici. Normalement le main devrais gérer le changement de touche. 

      }

      al_get_keyboard_state(keys);
      if (al_key_down(keys, ALLEGRO_KEY_V))
      {
         int i = 0;
         usleep(100);
         while (al_key_down(keys, ALLEGRO_KEY_V))
         {
            al_get_keyboard_state(keys);
         }

         int carId = road_addCar(rand() % 2);
         if (carId == -1)
         {
            fprintf(stderr, "Something went wrong while trying to add a car on the road. Exiting.\n");
            return (void *)EXIT_FAILURE;
         }
         struct arg argument;
         argument.carId = carId;
         argument.nbCar = &nbCar;
         argument.keys = keys;
         argument.stoped = stoped;
         pthread_t th;
         pthread_create(&th, NULL, avancer, &argument);
         nbCar += 1;
      }
   }
}

void *avancer(void *argument)
{

   ALLEGRO_KEYBOARD_STATE keys;
   struct arg *arg = (struct arg *)argument;
   int carId = arg->carId;
   int *nbCar = arg->nbCar;
   bool *stoped = arg->stoped;

   while (road_stepCar(carId))
   {
      while (*stoped)
      {
         // Ici j'ai pas besoin de le mettre.ça fonctionne quand même car les threads parents gère la modification pour lui. 
      }
      usleep(5000);
   }
   road_removeCar(carId);
   *nbCar = *nbCar - 1;
}
