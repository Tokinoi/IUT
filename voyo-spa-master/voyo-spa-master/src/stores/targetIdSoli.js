import { defineStore } from 'pinia';

export const useIdSoliStore = defineStore('targetId', {
  state: () => {
    return { targetId: null}
  },
  getters: {
    getId: (state) => state.targetId,
  },
  actions: {
    setId(newId) {
      this.targetId = newId
    },
  },
})
