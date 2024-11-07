#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include "list.h"

listNode *createNode(double score, char *str)
{
    listNode *n = malloc(sizeof(listNode));
    n->score = score;
    n->forward = NULL;
    n->ele = strdup(str);
    return n;
}

void freeNode(listNode *n)
{
    if (n == NULL)
        return;

    free(n->ele);

    free(n);
}

unsigned long size(list *pList)
{
    listNode *cur = pList->head;
    size_t s = 0;
    while (cur)
    {
        cur = cur->forward;
        s++;
    }
    return s;
}

void freeList(list *pList)
{
    listNode *cur = pList->head;

    while(cur)
    {
        listNode *c = cur;
        cur = cur->forward;
        freeNode(c);
    }
}

list *insertNode(list *pList, double score, char *str)
{
    listNode *newNode = createNode(score, str);
    listNode *cur = pList->head, *prev = NULL;

    while (cur != NULL && cmpNodes(cur, score, str) < 0)
    {
        prev = cur;
        cur = cur->forward;
    }

    newNode->forward = cur;

    pList->length++;


    if (prev == NULL) {
        pList->head = newNode;
    }else {
        prev->forward = newNode;
    }
    return pList;
}


void removeValues(list *pList, char *str)
{
    listNode *prev = NULL,
             *cur = pList->head;

    while (cur != NULL)
    {
        if (strcmp(cur->ele, str) == 0)
        {
            listNode *tmp = cur;
            if (prev == NULL)
            {
                pList->head = cur->forward;
            }
            else
            {
                prev->forward = cur->forward;
            }
            cur = cur->forward;
            freeNode(tmp);
            pList->length--;
        }
        else
        {
            prev = cur;
            cur = cur->forward;
        }


    }
}

char *printToString(list *pList)
{
    int total_size = 0;

    for (listNode *cur = pList->head; cur != NULL; cur = cur->forward)
    {
        total_size += snprintf(NULL, 0, "%.0f %s", cur->score, cur->ele);
        if (cur->ele != NULL)
        {
            total_size += 1; // for comma
        }
    }

    char *result = malloc(total_size + 1); // +1 for null terminator

    if (result == NULL)
    {
        perror("malloc failed");
        return NULL;
    }

    result[total_size] = '\0';

    char *ptr = result;

    for (listNode *cur = pList->head; cur != NULL; cur = cur->forward)
    {
        ptr += sprintf(ptr, "%.0f %s", cur->score, cur->ele);
        if (cur->forward != NULL)
        {
            ptr += sprintf(ptr, ",");
        }
    }

    return result;
}

void print(list *pList)
{
    if (pList->head == NULL)
    {
        printf("The list is empty.\n");
        return;
    }

    for (listNode *cur = pList->head; cur != NULL; cur = cur->forward)
    {
        printf("%.0f %s", cur->score, cur->ele);
        if (cur->forward != NULL)
        {
            printf(",");
        }
    }
    printf("\n");

    return;
}

char *printRangeByScore(list *pList, int scoreMin, int scoreMax)
{
    int total_size = 0;
    listNode *cur;
    for (cur = pList->head; cur != NULL; cur = cur->forward)
    {
        if (cur->score > scoreMax)
        {
            break; // We can stop here because the list is sorted.
        }
        if (cur->score < scoreMin)
        {
            continue;
        }
        total_size += snprintf(NULL, 0, "%.0f %s", cur->score, cur->ele);
        if (cur->forward != NULL && cur->forward->score <= scoreMax)
        {
            total_size += 1; // for comma
        }
    }

    char *result = malloc(total_size + 1); // +1 for null terminator

    if (result == NULL)
    {
        perror("malloc failed");
        return NULL;
    }

    result[total_size] = '\0';

    char *ptr = result;
    for (cur = pList->head; cur != NULL; cur = cur->forward)
    {
        if (cur->score > scoreMax)
        {
            break; // We can stop here because the list is sorted.
        }
        if (cur->score < scoreMin)
        {
            continue;
        }
        ptr += sprintf(ptr, "%.0f %s", cur->score, cur->ele);
        if (cur->forward != NULL && cur->forward->score <= scoreMax)
        {
            ptr += sprintf(ptr, ",");
        }
    }

    return result;
}

list *createList(void)
{
    list *pList = malloc(sizeof(list));
    pList->head = NULL;
    pList->length = 0;
    return pList;
}


int cmpNodes(listNode *a, double score, char *str)
{
    if (a->score < score)
        return -1;
    if (a->score > score)
        return 1;
    return strcmp(a->ele, str);
}
