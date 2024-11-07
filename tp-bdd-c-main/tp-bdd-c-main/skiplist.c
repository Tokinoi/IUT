#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include "list.h"

// Fonction de création d'un noeud
skiplistNode *createNode(int level, double score, char *ele) {
    skiplistNode *n = malloc(sizeof(skiplistNode) + level * sizeof(struct skiplistLevel));
    n->score = score;
    n->ele = ele == NULL ? NULL : strdup(ele);
    for (int i = 0; i < level; i++) {
        n->level[i].forward = NULL;
    }
    return n;
}

// Fonction de libération de la mémoire d'un noeud
void freeNode(skiplistNode *n) {
    if (n == NULL) return;
    free(n->ele);
    free(n);
}

// Fonction pour obtenir la taille de la liste
unsigned long size(skiplist *pList) {
    unsigned long s = 0;
    skiplistNode *cur = pList->header->level[0].forward;
    while (cur) {
        cur = cur->level[0].forward;
        s++;
    }
    return s;
}

// Fonction de libération de la mémoire de la liste
void freeList(skiplist *pList) {
    skiplistNode *cur = pList->header->level[0].forward;
    while (cur) {
        skiplistNode *next = cur->level[0].forward;
        freeNode(cur);
        cur = next;
    }
    free(pList->header);
    free(pList);
}

// Fonction d'insertion d'un noeud dans la liste
skiplist *insertNode(skiplist *pList, double score, char *ele) {
    skiplistNode *update[SKIPLIST_MAXLEVEL];
    skiplistNode *cur = pList->header;
    for (int i = pList->level - 1; i >= 0; i--) {
        while (cur->level[i].forward && cmpNodes(cur->level[i].forward, score, ele) < 0) {
            cur = cur->level[i].forward;
        }
        update[i] = cur;
    }

    int level = rand() % SKIPLIST_MAXLEVEL;
    if (level > pList->level) {
        for (int i = pList->level; i < level; i++) {
            update[i] = pList->header;
        }
        pList->level = level;
    }

    skiplistNode *newNode = createNode(level, score, ele);
    for (int i = 0; i < level; i++) {
        newNode->level[i].forward = update[i]->level[i].forward;
        update[i]->level[i].forward = newNode;
    }
    pList->length++;
    return pList;
}

// Fonction de suppression des valeurs
void removeValues(skiplist *pList, char *ele) {
    skiplistNode *update[SKIPLIST_MAXLEVEL];
    skiplistNode *cur = pList->header;
    for (int i = pList->level - 1; i >= 0; i--) {
        while (cur->level[i].forward && strcmp(cur->level[i].forward->ele, ele) < 0) {
            cur = cur->level[i].forward;
        }
        update[i] = cur;
    }

    cur = cur->level[0].forward;
    if (cur && strcmp(cur->ele, ele) == 0) {
        for (int i = 0; i < pList->level; i++) {
            if (update[i]->level[i].forward != cur) break;
            update[i]->level[i].forward = cur->level[i].forward;
        }
        freeNode(cur);
        while (pList->level > 1 && pList->header->level[pList->level - 1].forward == NULL) {
            pList->level--;
        }
        pList->length--;
    }
}

// Fonction pour convertir la liste en chaîne de caractères
char *printToString(skiplist *pList) {
    int total_size = 0;
    for (skiplistNode *cur = pList->header->level[0].forward; cur != NULL; cur = cur->level[0].forward) {
        total_size += snprintf(NULL, 0, "%.0f %s", cur->score, cur->ele);
        if (cur->level[0].forward != NULL) {
            total_size += 1; // for comma
        }
    }

    char *result = malloc(total_size + 1); // +1 for null terminator
    if (result == NULL) {
        perror("malloc failed");
        return NULL;
    }

    char *ptr = result;
    for (skiplistNode *cur = pList->header->level[0].forward; cur != NULL; cur = cur->level[0].forward) {
        ptr += sprintf(ptr, "%.0f %s", cur->score, cur->ele);
        if (cur->level[0].forward != NULL) {
            ptr += sprintf(ptr, ",");
        }
    }

    return result;
}

// Fonction pour imprimer la liste
void print(skiplist *pList) {
    if (pList->header->level[0].forward == NULL) {
        printf("The list is empty.\n");
        return;
    }

    for (skiplistNode *cur = pList->header->level[0].forward; cur != NULL; cur = cur->level[0].forward) {
        printf("%.0f %s", cur->score, cur->ele);
        if (cur->level[0].forward != NULL) {
            printf(",");
        }
    }
    printf("\n");
}

// Fonction pour imprimer les éléments dans une plage de scores
char *printRangeByScore(skiplist *pList, int scoreMin, int scoreMax) {
    int total_size = 0;
    skiplistNode *cur;
    for (cur = pList->header->level[0].forward; cur != NULL; cur = cur->level[0].forward) {
        if (cur->score > scoreMax) {
            break; // We can stop here because the list is sorted.
        }
        if (cur->score < scoreMin) {
            continue;
        }
        total_size += snprintf(NULL, 0, "%.0f %s", cur->score, cur->ele);
        if (cur->level[0].forward != NULL && cur->level[0].forward->score <= scoreMax) {
            total_size += 1; // for comma
        }
    }

    char *result = malloc(total_size + 1); // +1 for null terminator
    if (result == NULL) {
        perror("malloc failed");
        return NULL;
    }

    char *ptr = result;
    for (cur = pList->header->level[0].forward; cur != NULL; cur = cur->level[0].forward) {
        if (cur->score > scoreMax) {
            break; // We can stop here because the list is sorted.
        }
        if (cur->score < scoreMin) {
            continue;
        }
        ptr += sprintf(ptr, "%.0f %s", cur->score, cur->ele);
        if (cur->level[0].forward != NULL && cur->level[0].forward->score <= scoreMax) {
            ptr += sprintf(ptr, ",");
        }
    }

    return result;
}

// Fonction de création d'une skiplist
skiplist *createSkiplist(void) {
    skiplist *pList = malloc(sizeof(skiplist));
    pList->header = createNode(SKIPLIST_MAXLEVEL, 0, NULL);
    pList->length = 0;
    pList->level = 1;
    return pList;
}

// Fonction de comparaison des noeuds
int cmpNodes(skiplistNode *a, double score, char *str) {
    if (a->score < score) return -1;
    if (a->score > score) return 1;
    return strcmp(a->ele, str);
}
