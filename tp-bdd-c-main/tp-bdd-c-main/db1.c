#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <readline/readline.h>
#include <readline/history.h>
#include "list.h"

#define MAX_COMMAND_LENGTH 128
#define MAX_DATA_LENGTH 50

listNode *gListHead = NULL;

void cmd_zadd(char *command)
{
    int score;
    char data[MAX_DATA_LENGTH];
    sscanf(command, "zadd %d %s", &score, data);
    printf("Adding: Score: %d, data: %s\n", score, data);
    gListHead = insertNode(gListHead, score, data);
}

void cmd_zrem(char *command)
{
    char data[MAX_DATA_LENGTH];
    sscanf(command, "zrem %s", data);
    printf("Removing: data: %s\n", data);
    gListHead = removeValues(gListHead, data);
}

void cmd_zrangebyscore(char *command)
{
    int min, max;
    sscanf(command, "zrangebyscore %d %d", &min, &max);
    printf("Range by score: Min: %d, Max: %d\n", min, max);
    char *res = printRangeByScore(gListHead, min, max);
    printf("%s\n", res);
    free(res);
}

void listenToInputs()
{
    char *input;
    while ((input = readline("\ndb1: ")) != NULL)
    {
        if (strcmp(input, "bye") == 0)
        {
            free(input);
            break;
        }
        else
        {
            add_history(input);
            if (strncmp(input, "zadd", 4) == 0)
            {
                cmd_zadd(input);
            }
            else if (strncmp(input, "zrem", 4) == 0)
            {
                cmd_zrem(input);
            }
            else if (strncmp(input, "zrangebyscore", 13) == 0)
            {
                cmd_zrangebyscore(input);
            }
            else
            {
                printf("Unknown command: %s\n", input);
            }
            free(input);
        }
    }
    printf("Exiting the program...\n");
}

void listenToInputsNoReadline()
{
    int isLooping = 1;

    while (isLooping)
    {
        char buffer[MAX_COMMAND_LENGTH];

        printf("\ndb1: ");

        if (fgets(buffer, sizeof(buffer), stdin) != NULL)
        {
            buffer[strcspn(buffer, "\n")] = '\0'; // Remove trailing newline

            if (strcmp(buffer, "bye") == 0)
            {
                printf("Exiting the program...\n");
                isLooping = 0;
            }
            else if (strncmp(buffer, "zadd", 4) == 0)
            {
                cmd_zadd(buffer);
            }
            else if (strncmp(buffer, "zrem", 4) == 0)
            {
                cmd_zrem(buffer);
            }
            else if (strncmp(buffer, "zrangebyscore", 13) == 0)
            {
                cmd_zrangebyscore(buffer);
            }
            else
            {
                printf("Unknown command: %s\n", buffer);
            }
        }
        else
        {
            if (feof(stdin))
            {
                printf("End of input reached. Exiting the program...\n");
                isLooping = 0;
            }
            else
            {
                perror("Error reading input");
                isLooping = 0;
            }
        }
    }
}

int main()
{
    // listenToInputsNoReadline();
    listenToInputs();
    return 0;
}
