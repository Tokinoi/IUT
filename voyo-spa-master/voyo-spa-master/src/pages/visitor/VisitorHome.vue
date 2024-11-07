<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Visites" />

    <q-page-container class="q-pa-md">
      <p class="text-subtitle1">Solicitations</p>
      <div v-if="!loading">
        <q-list v-for="soli in responseDataVisitors" :key="soli">
          <q-item bordered round class="q-pa-md q-ma-none bg-white rounded" style="border: 2px solid #ffa628;">
            <q-item-section>
              <q-item-label>{{ soli["propertyAddress"] }}, {{ soli["propertyCity"] }}</q-item-label>
              <q-item-label caption>{{ soli["scheduledAt"].split('T')[0] }}</q-item-label>
            </q-item-section>
            <q-btn @click="setVisits( soli['id'], soli['visitRequestId'])"
            class="rounded-borders text-white"
            :class="soli.isAccepted ? 'bg-positive' : 'bg-primary'">
              <q-icon name="fas fa-edit" />
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

      <div class="q-mt-lg flex">
        <p class="text-subtitle1">Demandes</p>
        <q-btn @click="$router.replace('/visit')" class="bg-primary text-white q-px-sm q-ml-sm rounded-borders" style="transform: translateY(-8px);">
          <q-icon name="fas fa-plus" />
        </q-btn>
      </div>
      <div v-if="!loading">
        <q-list v-for="visit in responseData" :key="visit">
          <q-item v-if="visit['status'] !== 'Annulée'" bordered round class="q-pa-md q-ma-none bg-white rounded" style="border: 2px solid #ffa628;">
            <q-item-section>
              <q-item-label>{{ visit["propertyAddress"] }}, {{ visit["propertyCity"] }}</q-item-label>
              <q-item-label caption>{{ unixToDate(visit["scheduledAt"]) }}</q-item-label>
            </q-item-section>
            <div v-if="visit.status === 'Terminée'">
              <q-btn @click="setVisits('', visit['id'])" class="rounded-borders bg-green text-white">
                <q-icon name="fas fa-check" />
              </q-btn>
            </div>
            <div v-else>
              <q-btn @click="setVisits('', visit['id'])" class="rounded-borders bg-primary text-white">
                <q-icon name="fas fa-edit" />
              </q-btn>
              <q-btn @click="confirmationDialog = true; currentVisit = visit" class="q-ml-sm rounded-borders bg-red text-white">
                <q-icon name="fas fa-xmark" />
              </q-btn>
            </div>
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
import { myVisitSoli, myVisitsRequests, viewVisitRequest, deleteVisitRequest } from 'pages/visit/composables/api';
import { useVisitStore } from 'stores/targetVisit';
import { useIdSoliStore } from 'stores/targetIdSoli';

const router = useRouter()
const responseData = ref(null)
const responseDataVisitors = ref(null)
const loading = ref(true)
const confirmationDialog = ref(false)

const store = useVisitStore()
const idSoli = useIdSoliStore()

async function onLoad() {
  try {
    responseData.value = await myVisitsRequests();
    responseDataVisitors.value = await myVisitSoli();
    console.log(responseDataVisitors.value)
  } catch (error) {
    console.error(error);
    // Gérer l'erreur ici, par exemple rediriger vers une page d'erreur
  } finally {
    loading.value = false
  }
}

onLoad()

watch(responseData.value, () => {
  onLoad()
}, { deep: true })

async function setVisits(idSoli, id) {
  let response = await onView(idSoli, id);
  console.log(25, response)
  if (response['isTheConnectedUserTheVisitor'] === false && response['isTheConnectedUserTheClient'] === false) {
      await router.replace('/viewVisit');
    }
  if (response['isTheConnectedUserTheVisitor'] === true && response['isTheConnectedUserTheClient'] === false) {
    if (response['status'] === 'En cours') {
      await router.replace('/visitorReport');
    } else if (response['status'] === 'Terminée') {
      await router.replace('/terminatedVisit');
    } else {
      await router.replace('/viewVisit');
    }
  }
  if (response['isTheConnectedUserTheVisitor'] === false && response['isTheConnectedUserTheClient'] === true) {
    await router.replace('/visit/update');
  }
}

async function onAnnulate(id) {
  try {
    await deleteVisitRequest(id);
  } catch (error) {
    console.error(error);
  } finally {
    onLoad()
    confirmationDialog.value = false
  }
}

async function onView(idSolis, id) {
  try {
    const responseData = await viewVisitRequest(id); // Récupérer les données de la réponse Axios
    console.log(responseData);
    store.setVisit(responseData)
    console.log(id)
    sessionStorage.setItem('voyo-visitId', id)
    idSoli.setId(idSolis)
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

function isFinished(soliId) {
  responseData.value.forEach(visit => {
    if (soliId === visit.id && visit.status === 'Terminée') return false
  });
  return true
}
</script>
