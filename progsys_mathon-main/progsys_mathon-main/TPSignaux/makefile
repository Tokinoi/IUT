path=/users/but/info/Public/Systeme/onTheRoadAgain/

componTheRoadAgain:
	gcc -c -I$(path) onTheRoadAgain.c -pthread -o onTheRoadAgain.o 

EDLonTheRoadAgain:
	gcc  onTheRoadAgain.o  -o onTheRoadAgain.exe -L$(path) -lroad -lallegro -lallegro_primitives -pthread

componTheRoadAgainTimer:
	gcc -c -I$(path) onTheRoadAgainTimer.c -pthread -o onTheRoadAgainTimer.o 

EDLonTheRoadAgainTimer:
	gcc  onTheRoadAgainTimer.o  -o onTheRoadAgainTimer.exe -L$(path) -lroad -lallegro -lallegro_primitives -pthread
