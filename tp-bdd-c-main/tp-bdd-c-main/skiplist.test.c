#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <assert.h>
#include <stdbool.h>
#include "skiplist.h"

bool isEqual(skiplist *var_skiplist, char *expected) {
    char *res = printToString(var_skiplist);
    int cmp = strcmp(res, expected);
    free(res);
    return cmp == 0;
}

void test1() {
    skiplist *var_skiplist = createSkiplist();
    insertNode(var_skiplist, 100, "lorem");
    assert(isEqual(var_skiplist, "100 lorem"));
    insertNode(var_skiplist, 100, "ipsum");
    assert(isEqual(var_skiplist, "100 ipsum,100 lorem"));
    insertNode(var_skiplist, 200, "lorem");
    assert(isEqual(var_skiplist, "100 ipsum,100 lorem,200 lorem"));
    removeValues(var_skiplist, "lorem");
    assert(isEqual(var_skiplist, "100 ipsum"));
    removeValues(var_skiplist, "ipsum");
    assert(isEqual(var_skiplist, ""));
    printf("ALL TESTS PASSED!\n");
}

int main() {
    test1();
    return 0;
}
