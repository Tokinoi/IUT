<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Visites" />

    <q-page-container class="q-pa-md">
      <div>
        <div class="flex">
          <p class="text-subtitle1">Demandes</p>
          <q-btn @click="$router.replace('/visit')" class="bg-primary text-white q-px-sm q-ml-sm rounded-borders" style="transform: translateY(-8px);">
            <q-icon name="fas fa-plus" />
          </q-btn>
        </div>
        <div v-if="!loading">
          <q-list v-for="visit in responseData" :key="visit" >
            <q-item v-if="visit['status'] !== 'Annulée'" bordered round class="q-pa-md q-ma-none bg-white rounded" style="border: 2px solid #ffa628;">
              <q-item-section>
                <q-item-label>{{ visit["propertyAddress"] }}, {{ visit["propertyCity"] }}</q-item-label>
                <q-item-label caption>{{ unixToDate(visit["scheduledAt"]) }}</q-item-label>
              </q-item-section>
              <q-btn @click="setVisits(visit['id'])" class="rounded-borders" style="background: #ffa628; color: white">
                <q-icon name="fas fa-edit" />
              </q-btn>
              <q-btn @click="confirmationDialog = true; currentVisit = visit" class="q-ml-sm rounded-borders" style="background: #C10015; color: white">
                <q-icon name="fas fa-xmark" />
              </q-btn>
            </q-item>
          </q-list>
        </div>
        <div v-else class="q-pa-md">
          <div class="q-gutter-y-md">
            <q-skeleton bordered />
            <q-skeleton bordered />
            <q-skeleton bordered />
          </div>
        </div>
      </div>

    </q-page-container>

    <ToolBarComponent home />
  </q-layout>

  <q-dialog v-model="confirmationDialog">
    <q-card>
      <q-card-section class="items-center">
        <p>Êtes-vous sûr de vouloir supprimer cette demande de visite ?</p>
        <div class="text-center">
          <q-btn @click="onAnnulate(currentVisit['id'])" label="Confirmer" color="positive" text-color="white" class="q-mr-md" />
          <q-btn @click="confirmationDialog = false" label="Annuler" color="negative" text-color="white" />
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import HeaderComponent from 'src/components/HeaderComponent.vue';
import ToolBarComponent from 'src/components/ToolBarComponent.vue';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { deleteVisitRequest, myVisitsRequests, viewVisitRequest } from 'pages/visit/composables/api';
import { useVisitStore } from 'stores/targetVisit';
import { useIdStore } from 'stores/targetId';
import { notify } from 'src/composables/notify'

const router = useRouter()
const responseData = ref(null)
const loading = ref(true)
const confirmationDialog = ref(false)

const store = useVisitStore()
const idStore = useIdStore()
const currentVisit = ref(null)

const visitorList = ref([])
visitorList.value.push({name:'Jean Baptiste', address:'Lyon', img:'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU'})

visitorList.value.push({name:'Pole pogba', address:'Paris', img:'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU'})

visitorList.value.push({name:'Tom Dario', address:'Valence', img:'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU'})

async function onLoad() {
  try {
    responseData.value = await myVisitsRequests();
  } catch (error) {
    console.error(error);
    // Gérer l'erreur ici, par exemple rediriger vers une page d'erreur
  }
  confirmationDialog.value = false
  loading.value = false
}

onLoad()

watch(responseData.value, () => {
  onLoad()
}, { deep: true })

async function onAnnulate(id) {
  try {
    await deleteVisitRequest(id);
  } catch (error) {
    console.error(error);
    // Gérer l'erreur ici, par exemple rediriger vers une page d'erreur
  } finally {
    notify('Demande de visite supprimer avec succès', 'positive')
    onLoad()
  }
}

async function setVisits(id) {
  await onView(id);
  await router.replace('/visit/update');
}

async function onView(id) {
  try {
    const responseData = await viewVisitRequest(id); // Récupérer les données de la réponse Axios
    console.log('visitGet',responseData);
    store.setVisit(responseData)
    idStore.setId(id)
    return responseData; // Retourner les données de la réponse
  } catch (e) {
    console.error(e);
    throw e; // Si une erreur se produit, la relancer pour la gérer ailleurs si nécessaire
  }
}

function unixToDate(unixTime) {
   // Par exemple, un timestamp Unix correspondant au 7 avril 2021 à 12:00:00 UTC
// Créer un objet Date en utilisant le timestamp Unix
  const date = new Date(unixTime * 1000); // JavaScript utilise des millisecondes, donc nous multiplions par 1000

// Extraire les éléments de la date (année, mois, jour)
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois sont indexés de 0 à 11
  const day = String(date.getDate()).padStart(2, '0');

// Créer la date au format souhaité (YYYY-MM-DD)
  return `${year}-${month}-${day}`;
}

</script>
