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
#include "Reseau.h"
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
void *avancer(void *argument);
void *creerVoiture(void *arg);
struct sock
{
   int numSocket;
   int numPort;
} sock;
struct arg
{
   int carId;
   int *nbCar;
   struct sock socket;
} arg;

int main(int argc, const char *argv[])
{
   struct sock socket;
   int *num = (int *)argv[1];
   socket.numPort = *num;
   socket.numSocket = socketClient("localhost", socket.numPort, TCP);
   printf("Je suis connecter\n");
   fflush(stdout);
   
   
   road_init(0);

   pthread_t th;

   pthread_create(&th, NULL, creerVoiture, &socket);

   // Update display and make car move until Esc is pressed
   usleep(5000); // Wait cause he do the test before a car is create
   while (!road_isEscPressed() && pthread_tryjoin_np(th, NULL))
   {
      road_refresh();
   }
   // Clean shutdown
   road_shutdown();

   return 0;
}
void *creerVoiture(void *arg)
{

   struct sock* socket = (struct sock *)arg;
   int nbCar = 0;
   for(int i=0; i<6;i++){
   sleep(1);
   nbCar += 1;
   int carId = road_addCar(0);
   if (carId == -1)
   {
      fprintf(stderr, "Something went wrong while trying to add a car on the road. Exiting.\n");
      return (void *)EXIT_FAILURE;
   }
   struct arg argument;
   argument.carId = carId;
   argument.nbCar = &nbCar;
   argument.socket = *socket;
   pthread_t th;
   pthread_create(&th, NULL, avancer, &argument);
   while(nbCar != 0){

      }   }
}

void *avancer(void *argument)
{
   struct arg *arg = (struct arg *)argument;
   int carId = arg->carId;
   int *nbCar = arg->nbCar;
   struct sock socket = arg->socket;
   while (road_stepCar(carId))
   {
      usleep(5000);
   }
   if (socket.numSocket != -1) write(socket.numSocket,"Car",3);
   road_removeCar(carId);
   *nbCar= *nbCar - 1;
}
