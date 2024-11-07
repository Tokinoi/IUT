
#define _GNU_SOURCE
#include <unistd.h> // For usleep
#include <pthread.h>
#include <errno.h>
#include <stdlib.h> // For rand
#include <semaphore.h> 
#include <signal.h>
#include <stdio.h>
#include "road.h"

typedef struct {
   int carId;
   int roadId; // 0: left -> right; 1: right -> left
  sem_t* sem; //"le carrefour"
} CarInfo;

typedef struct{
   int count;
   sem_t* sem1;
   sem_t* sem2;
} Switch;

bool stepCar(int id){
      while(road_distNextCar(id) <= road_minDist){
         
      }
      return road_stepCar(id);
}

void* switchLane(void* para){
   Switch* param = (Switch*) para;
   int count = param->count;
   sem_t* sem1 = param->sem1;
   sem_t* sem2 = param->sem2;
   for (int token = 0; token < count; token++)sem_wait(sem2);
   while(!road_isEscPressed()){
      for (int token = 0; token < count; token++)sem_wait(sem1);
      for (int token = 0; token < count; token++)sem_post(sem2);
      sleep(3);
      for (int token = 0; token < count; token++)sem_wait(sem2);
      for (int token = 0; token < count; token++)sem_post(sem1);
      sleep(3);
   }
}


void* avancer(void* ci)
{
   CarInfo* carInfo = (CarInfo*) ci;
   const int carId = carInfo->carId;
   const int roadId = carInfo->roadId;
   sem_t* mut = carInfo->sem;
   bool outOfRoad = false;
   
            while (road_distToCross(carId) > road_minDist){
               stepCar(carId);
               usleep(3000);
            }


sem_wait(mut);

            while (road_distToCross(carId) != __INT_MAX__)
            {
               stepCar(carId);
               usleep(3000);
            }

sem_post(mut);  

            while (!outOfRoad)
            {
               outOfRoad = !stepCar(carId);
               usleep(3000);
            }

   road_removeCar(carId);
   return NULL;
}

void* createCars(void* unused)
{
   sem_t sem0; //Route dans un sens
   sem_t sem1; //Route dans l'autre sens
   int count= 500;
   sem_init(&sem0,0,count);
   sem_init(&sem1,0,count);
   pthread_t carThread;
   const int nbCars = 300;
   CarInfo carsInfo[nbCars];
   const int minDelay = 300000;
   int delay;

   //creer struc switch 
   Switch parameter;
   parameter.count=count;
   parameter.sem1=&sem0;
   parameter.sem2=&sem1;


   pthread_t alternateLine;
   pthread_create(&alternateLine, NULL, switchLane,&parameter);

   // Create cars
   for (int i = 0; i < nbCars; i++) {
      delay = minDelay + rand()%(minDelay);
      usleep(delay);
      const int roadId = rand()>RAND_MAX/3 ? 0 : 1; // A bit more cars from left than from right
      carsInfo[i].carId = road_addCar(roadId);
      carsInfo[i].roadId = roadId;
      if (roadId == 1)carsInfo[i].sem = &sem1;
      else carsInfo[i].sem = &sem0;
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

