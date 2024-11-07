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

    <!-- Texte de la checkbox à gauche de la checkbox -->
    <div class="q-pa-sm">
      <q-item-label>Etat des visites</q-item-label>
      <q-select
        v-model="checkRef"
        :options="options"
        emit-value
        map-options
      />
    </div>

    <q-btn @click="onReaserch" class="q-mt-sm text-black" color="primary" icon="check" label="Rechercher" />
    <q-list v-for="user in responseData" :key="user" class="bg-white">
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
import { searchvisit } from 'src/composables/api';
const responseData = ref(null)

const queryRef = ref(null)
const options = ref([
  { label: 'Toutes', value: 'option1' },
  { label: 'En attente', value: 'option2' },
  { label: 'Annulée', value: 'option3' },
  { label: 'Terminée', value: 'option4' },
]);

const checkRef = ref('option1');


async function onReaserch() {
  try {
    console.log(checkRef.value)
    console.log(queryRef.value)
    responseData.value = await searchvisit(queryRef.value, checkRef.value)
    console.log(responseData.value)
  } catch (e) {
    console.error(e);
  } finally {
    console.log(responseData.value)
  }
}
</script>
