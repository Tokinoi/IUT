#ifndef LIST_H_
#define LIST_H_
#define SKIPLIST_MAXLEVEL 32
#define SKIPLIST_P 0.25


typedef struct skiplist {
    struct skiplistNode *header; // le nœud sentinelle
    unsigned long length; // le nombre d'éléments dans la liste
    int level; // le nombre de niveaux présents, initialiser à 1
} skiplist;
typedef struct skiplistNode {
    char *ele;
    double score;
    struct skiplistLevel {
    struct skiplistNode *forward; // pointe vers le nœud suivant
    } level[];
} skiplistNode;

skiplistNode *createNode(int level,double score, char *str);
void freeNode(skiplistNode *n);
char *printToString(skiplistNode *pList);
void print(skiplistNode *pList);
char *printRangeByScore(skiplistNode  *pList, int scoreMin, int scoreMax);
skiplistNode *createSkiplist(void);
unsigned long size(skiplistNode *pList);
void freeList(skiplistNode *pList);
skiplistNode *insertNode(skiplistNode *pList, double score, char *str);
void removeValues(skiplistNode *pList, char *str);
int cmpNodes(listNode *a, double score, char *str);

#endif
