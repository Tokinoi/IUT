<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Créer une demande" />
    <q-input class="q-pa-sm" rounded standout bottom-slots v-model="queryRef" label="Rechercher...">
      <template v-slot:prepend>
        <q-icon name="place" />
      </template>
      <template v-slot:append>
        <q-icon name="close" @click="queryRef = null" class="cursor-pointer" />
      </template>
    </q-input>

    <div class="q-pa-sm">
      <q-item-label>Afficher uniquement les visiteurs</q-item-label>
      <q-checkbox v-model="checkRef" @click="console.log(checkRef)" :value="false" />
    </div>

    <q-btn @click="onReaserch" class="q-mt-sm text-black" color="primary" icon="check" label="Rechercher" />
    <q-list v-for="user in responseData" :key="user"  class="bg-white">
      <q-item-section>
        <q-item-label>{{ user["displayname"] }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ user["city"] }}</q-item-label>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ user["address"] }}</q-item-label>
      </q-item-section>
    </q-list>
  </q-layout>
</template>

<script setup>
import HeaderComponent from 'components/HeaderComponent.vue';
import { ref } from 'vue';
import { searchUsers } from 'src/composables/api';
const responseData = ref(null)

const queryRef = ref(null)
const checkRef = ref(false) // Définir la valeur initiale à false

async function onReaserch() {
  try {
    console.log(checkRef.value)
    console.log(queryRef.value)
    responseData.value = await searchUsers(queryRef.value, checkRef.value)
  } catch (e) {
    console.error(e);
  } finally {
    console.log(responseData.value)
  }
}
</script>
