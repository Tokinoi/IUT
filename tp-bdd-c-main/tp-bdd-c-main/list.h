#ifndef LIST_H_
#define LIST_H_

typedef struct listNode {
    char *ele;
    double score;
    struct listNode *forward;
} listNode;
typedef struct list {
    struct listNode *head;
    unsigned long length;
} list;

listNode *createNode(double score, char *str);
void freeNode(listNode *n);
char *printToString(list *pList);
void print(list *pList);
char *printRangeByScore(list *pList, int scoreMin, int scoreMax);
list *createList(void);
unsigned long size(list *pList);
void freeList(list *pList);
list *insertNode(list *pList, double score, char *str);
void removeValues(list *pList, char *str);
int cmpNodes(listNode *a, double score, char *str);

#endif
