CC := gcc
CFLAGS := -Wall -Werror -pedantic -g
EXECUTABLES := db1 list.test list2.test

all: $(EXECUTABLES)

clean:
	rm -f *.o $(EXECUTABLES)

list.test: list.test.o list.o
	$(CC) $(LFLAGS) -o $@ $^

list2.test: list2.test.o list2.o
	$(CC) $(LFLAGS) -o $@ $^

db1: db1.o list.o
	$(CC) $(LFLAGS) -o $@ $^  -L./readline/shlib  -lreadline -lncurses

%.o : %.c
	$(CC) $(CFLAGS) -I. -c -o $@ $^

archive:
	tar -cvzf list.tar.gz Makefile list.h list.c list.test.c db1.c commandes.txt readline .vscode/launch.json

skiplist.o: skiplist.c skiplist.h
	$(CC) $(CFLAGS) -c skiplist.c

skiplist.test.o: skiplist.test.c skiplist.h
	$(CC) $(CFLAGS) -c skiplist.test.c

skiplist.test: skiplist.o skiplist.test.o
	$(CC) $(CFLAGS) -o skiplist.test skiplist.o skiplist.test.o
