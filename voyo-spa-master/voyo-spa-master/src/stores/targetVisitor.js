import { defineStore } from 'pinia';

export const useTargetVisitorStore = defineStore('targetVisitor', {
  state: () => {
    return { targetVisitor: null}
  },
  getters: {
    getVisitor: (state) => state.targetVisitor,
  },
  actions: {
    setVisitor(newVisitor) {
      this.targetVisitor = newVisitor
    },
  },
})
