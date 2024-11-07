path=/users/but/info/Public/Systeme/onTheRoad/

compLocal:
	gcc -c -I$(path) ClientLocal.c -o ClientLocal.o 
	gcc -c -I$(path) ServeurLocal.c -o ServeurLocal.o 
	gcc -c Reseau.c -o Reseau.o

EDLLocal:
	gcc  ServeurLocal.o Reseau.o -o ServeurLocal.exe -L$(path)  -lroad -lallegro -lallegro_primitives 
	gcc  ClientLocal.o Reseau.o -o ClientLocal.exe -L$(path)  -lroad -lallegro -lallegro_primitives 
