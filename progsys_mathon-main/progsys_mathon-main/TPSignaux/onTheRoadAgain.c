
#define _GNU_SOURCE
#include <unistd.h> // For usleep
#include <pthread.h>
#include <stdlib.h> // For rand
#include <signal.h> // For signals
#include <sys/time.h> // For setitimer
#include <stdio.h>

#include "road.h"

void* avancer(void* argument);
void creerVoiture();
void* gestionVoiture(void* argument);
struct arg{
   int carId; 
}arg;

int main(int argc, const char *argv[])
{
   road_init(0);

   pthread_t th;
   pthread_create(&th, NULL, gestionVoiture, NULL);

   while (pthread_tryjoin_np(th,NULL))
   {
      road_refresh();
   }

   road_shutdown();


   return 0;
}
void* gestionVoiture(void* argument){
   while(!road_isEscPressed()){
      signal(SIGINT,creerVoiture);
   }

}

void creerVoiture(){
   int carId = road_addCar(1);
   if (carId == -1) {
      fprintf(stderr, "Something went wrong when trying to add a car on the road. Exiting.\n");
   }
   struct arg argument; 
   argument.carId = carId; 
   pthread_t th;
   pthread_create(&th, NULL, avancer, &argument);
}

   void* avancer(void* argument){
      struct arg *arg= (struct arg*) argument;
      int carId = arg->carId; 
      while (road_stepCar(carId))
      {
         usleep(5000);
         
      }
      road_removeCar(carId);
      
   }