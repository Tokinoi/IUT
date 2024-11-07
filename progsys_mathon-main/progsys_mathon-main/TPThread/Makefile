path=/users/but/info/Public/Systeme/onTheRoad/

componTheRoad:
	gcc -c -I$(path) onTheRoad.c -o onTheRoad.o 

EDLonTheRoad:
	gcc  onTheRoad.o  -o onTheRoad -L$(path) -lroad -lallegro -lallegro_primitives 

componTheRoadV2:
	gcc -c -I$(path) onTheRoadV2.c -pthread -o onTheRoadV2.o 

EDLonTheRoadV2:
	gcc  onTheRoadV2.o  -o onTheRoadV2 -L$(path) -lroad -lallegro -lallegro_primitives -pthread

componTheRoad2:
	gcc -c -I$(path) onTheRoad2.c -pthread -o onTheRoad2.o 

EDLonTheRoad2:
	gcc  onTheRoad2.o  -o onTheRoad2 -L$(path) -lroad -lallegro -lallegro_primitives -pthread

componTheRoad3:
	gcc -c -I$(path) onTheRoad3.c -pthread -o onTheRoad3.o 

EDLonTheRoad3:
	gcc  onTheRoad3.o  -o onTheRoad3 -L$(path) -lroad -lallegro -lallegro_primitives -pthread

componTheRoadMaitrise:
	gcc -c -I$(path) onTheRoadMaitrise.c -pthread -o onTheRoadMaitrise.o 

EDLonTheRoadMaitrise:
	gcc  onTheRoadMaitrise.o  -o onTheRoadMaitrise -L$(path) -lroad -lallegro -lallegro_primitives -pthread