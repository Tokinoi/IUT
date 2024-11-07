
#define _GNU_SOURCE
#include <unistd.h> // For usleep
#include <pthread.h>
#include <errno.h>
#include <stdlib.h> // For rand
#include <semaphore.h> 
#include <signal.h>

#include "road.h"

typedef struct {
   int carId;
   int roadId; // 0: left -> right; 1: right -> left
   pthread_mutex_t* mut; //"le carrefour"
} CarInfo;


void* avancer(void* ci)
{
   CarInfo* carInfo = (CarInfo*) ci;
   const int carId = carInfo->carId;
   const int roadId = carInfo->roadId;
   pthread_mutex_t* mut = carInfo->mut;
   bool outOfRoad = false;
   
            while (road_distToCross(carId) > road_minDist){
               road_stepCar(carId);
               usleep(3000);
            }

   pthread_mutex_lock(mut);
   
            while (road_distToCross(carId) != __INT_MAX__)
            {
               road_stepCar(carId);
               usleep(3000);
            }

   
   pthread_mutex_unlock(mut);

            while (!outOfRoad)
            {
               outOfRoad = !road_stepCar(carId);
               usleep(3000);
            }

   road_removeCar(carId);

   return NULL;
}

void* createCars(void* unused)
{
   pthread_mutex_t mut = PTHREAD_MUTEX_INITIALIZER;
   pthread_t carThread;
   const int nbCars = 300;
   CarInfo carsInfo[nbCars];

   const int minDelay = 300000;
   int delay;

   // Create cars
   for (int i = 0; i < nbCars; i++) {
      delay = minDelay + rand()%(minDelay);
      usleep(delay);
      const int roadId = rand()>RAND_MAX/3 ? 0 : 1; // A bit more cars from left than from right
      carsInfo[i].carId = road_addCar(roadId);
      carsInfo[i].roadId = roadId;
      carsInfo[i].mut = &mut;
      // Start car-stepping thread
      pthread_create(&carThread, NULL, avancer, &carsInfo[i]);
   }

   // Vu le délai entre le démarrage des threads et le fait qu'ils durent
   // a priori le même temps, je n'ai besoin de faire un "join" que sur le 
   // dernier thread
   pthread_join(carThread, NULL);

   return NULL;
}


int main(int argc, const char *argv[])
{
   road_init(1);
   pthread_t createCarsThread;
   pthread_create(&createCarsThread, NULL, createCars, NULL);

   while (!road_isEscPressed() && pthread_tryjoin_np(createCarsThread, NULL) == EBUSY)
   {
      usleep(1000);
      road_refresh();
   }

   road_shutdown();

   return 0;
}

