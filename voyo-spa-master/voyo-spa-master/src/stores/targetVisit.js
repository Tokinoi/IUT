import { defineStore } from 'pinia';

export const useVisitStore = defineStore('targetVisit', {
  state: () => {
    return { targetVisit: null}
  },
  getters: {
    getVisit: (state) => state.targetVisit,
  },
  actions: {
    setVisit(newVisit) {
      this.targetVisit = newVisit
    },
  },
})
