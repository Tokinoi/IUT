<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Modification d'une demande de visite" />

    <q-page-container class="q-pa-md q-mb-lg">
      <q-form @submit="onSubmit" greedy class="q-gutter-md">
        <h6 class="q-mb-xs">Informations du Client</h6>
        <div class="text-center">
          <q-input
            v-model="nomClientRef"
            type="text"
            label="Nom du client"
            readonly
          />
          <q-input
            v-model="emailClientRef"
            type="text"
            label="Email du client"
            readonly
          />
          <q-input
            v-model="villeClientRef"
            type="text"
            label="Ville du client"
            readonly
          />
          <q-input
            v-model="adresseClientRef"
            type="text"
            label="Adresse du client"
            readonly
          />
        </div>
        <h6 class="q-mb-xs">Informations du Bien</h6>
        <div>
          <q-input
            v-model="adresseRef"
            type="text"
            label="Adresse du bien"
            :rules="[rules.required]"
            readonly
          />
          <q-input
            v-model="villeRef"
            type="text"
            label="Ville du bien"
            :rules="[rules.required]"
            readonly
          />
          <q-input
            v-model="CPref"
            type="text"
            label="Code postal du bien"
            :rules="[rules.required, rules.codePostal, rules.codePostalSize]"
            readonly
          />
        </div>
        <h6 class="q-mb-xs">Informations de la Visite</h6>
        <div>
          <q-input
            v-model="priceRef"
            type="text"
            label="Coût de la visite"
            :rules="[rules.required, rules.price]"
            :readonly="visit['visitor'] !== undefined"
          >
            <template v-slot:append>
              €
            </template>
          </q-input>
          <q-input
            v-model="dateVisiteRef"
            type="date"
            label="Date de la visite"
            :rules="[rules.required]"
            :readonly="visit['visitor'] !== undefined"
          />
        </div>
        <h6 class="q-mb-xs">Informations du Visiteur</h6>
        <div>
          <q-input
            v-model="nomVisiteurRef"
            type="text"
            label="Nom du visiteur"
            readonly
          />
          <q-input
            v-model="emailVisiteurRef"
            type="text"
            label="Email du visiteur"
            readonly
          />
          <q-input
            v-model="villeVisiteurRef"
            type="text"
            label="Ville du visiteur"
            readonly
          />
          <q-input
            v-model="adresseVisiteurRef"
            type="text"
            label="Adresse du visiteur"
            readonly
          />
          <q-input
            v-model="telVisiteurRef"
            type="text"
            label="Telephone du visiteur"
            readonly
          />
          <h6 class="q-mb-xs">Points particulier</h6>
          <q-input
            v-if='visit["visitor"] === undefined'
            v-model="newparticularDemandRef"
            type="text"
            label="Point particulier"
          >
            <template v-slot:append>
              <q-btn
                @click="addParticularDemand"
                round
                color="primary"
                icon="add"
                class="q-my-md text-black"
              />
            </template>
          </q-input>

          <div
            v-for="(condition, index) in particularDemandsRef"
            :key="condition"
            class="q-mt-sm text-left"
          >
            <div class="flex no-wrap justify-between">
              <span style="align-items: center; display: grid"
                >Demande n°{{ index + 1 }}: {{ condition }}</span
              >
              <q-btn
                v-if="visit['visitor'] === undefined"
                @click="removeParticularDemand(index)"
                round
                flat
                icon="remove"
                class="q-mt-none"
              />
            </div>
          </div>

          <div class="text-center">
            <q-btn
              label="Modifier les informations"
              icon="edit"
              type="submit"
              color="primary"
              class="q-my-lg text-black"
            />
            <div v-if="visit['visitor'] !== undefined">
              <q-btn
                label="Payer la visite"
                type="submit"
                color="primary"
                class="q-my-lg text-black"
                @click="onPay"
              />
            </div>
          </div>
          <div v-if='visit["visitor"] === undefined'>
            <p class="text-subtitle2">Recherche</p>
            <div class="text-center">
              <q-input class="q-pa-sm" rounded standout bottom-slots v-model="lieuRef" label="Lieu">
                <template v-slot:prepend>
                  <q-icon name="place" />
                </template>
                <template v-slot:append>
                  <q-icon name="close" @click="lieuRef = null" class="cursor-pointer" />
                </template>
              </q-input>

                <q-input class="q-pa-sm" rounded standout bottom-slots v-model="dateRef" label="Date">
                  <template v-slot:prepend>
                    <q-icon name="event" />
                  </template>
                  <template v-slot:append>
                    <q-icon name="close" @click="dateRef = null" class="cursor-pointer" />
                  </template>
                </q-input>

              <q-input class="q-pa-sm q-mb-md" rounded standout bottom-slots v-model="hourRef" label="Heure">
                <template v-slot:prepend>
                  <q-icon name="alarm" />
                </template>
                <template v-slot:append>
                  <q-icon name="close" @click="hourRef = null" class="cursor-pointer" />
                </template>
              </q-input>

              <q-list v-for="(user, index) in responseData" :key="user">
                <q-item @click="goToUserProfile(user['id'])" clickable v-ripple style="border: 2px orange solid;">
                  <q-item-section>
                    <q-item-label lines="1">{{ user["displayname"] }}</q-item-label>
                    <q-item-label caption lines="2">
                      <span>{{ user["email"] }}</span><br>
                      <span class="text-weight-bold">{{ user["city"] || "Aucune ville définie" }}</span>
                    </q-item-label>
                  </q-item-section>

                  <q-item-section side>
                    <q-btn v-if="!responseDisabled[index]" @click.stop="linkVisitor(visit.id, user['id'], index)" class="q-ma-xs text-black" color="primary" icon="send" label="Demande de visite" />
                    <q-btn v-else class="q-ma-xs text-black" color="positive" icon="check" label="Envoyé" disable />
                  </q-item-section>
                </q-item>
              </q-list>
              <div class="text-center">
                <q-btn @click="onReaserch" class="q-mt-sm text-black" color="primary" icon="search" label="Rechercher" />
              </div>
            </div>
          </div>
        </div>
      </q-form>
    </q-page-container>

    <ToolBarComponent home />
  </q-layout>
</template>

<script setup>
import HeaderComponent from 'src/components/HeaderComponent.vue'
import ToolBarComponent from 'src/components/ToolBarComponent.vue'
import rules from 'src/composables/rules'
import {
  editVisitRequest, payVisitRequest,
  searchVisitors, sendVisitRequest
} from 'src/pages/visit/composables/api';
import { ref, onMounted, toRaw } from 'vue';
import { useRouter } from 'vue-router'
import { useVisitStore } from 'stores/targetVisit';
import { useIdStore } from 'stores/targetId';
const router = useRouter()

const store = useVisitStore()
let visit = toRaw(store.targetVisit);

const idStore = useIdStore()

const nomClientRef = ref(visit['client']['displayName'])
const emailClientRef = ref(visit['client']['email'])
const villeClientRef = ref(visit['client']['city'])
const adresseClientRef = ref(visit['client']['address'])
let nomVisiteurRef = ref('')
let emailVisiteurRef = ref('')
let villeVisiteurRef = ref('')
let adresseVisiteurRef = ref('')
let telVisiteurRef = ref('')
if (visit['visitor'] !== undefined) {
  nomVisiteurRef = ref(visit['visitor']['displayName'])
  emailVisiteurRef = ref(visit['visitor']['email'])
  villeVisiteurRef = ref(visit['visitor']['city'])
  adresseVisiteurRef = ref(visit['visitor']['address'])
  telVisiteurRef = ref(visit['visitor']['phone'])
}
const adresseRef = ref(visit['propertyAddress'])
const villeRef = ref(visit['propertyCity'])
const CPref = ref(String(visit['propertyPostalCode']).trim())
const dateVisiteRef = ref(visit['scheduledAt'])
const priceRef = ref(visit['price'])
const lieuRef = ref('')
const dateRef = ref('')
const hourRef = ref('')

const particularDemandsRef = ref([])
const newparticularDemandRef = ref([])

const responseData = ref(null)
const responseDisabled = ref([])

function addParticularDemand() {
  particularDemandsRef.value.push(newparticularDemandRef.value);
  newparticularDemandRef.value = ''
}

function removeParticularDemand(value) {
  particularDemandsRef.value.splice(value, 1)
}

async function onSubmit() {
  try {
    await editVisitRequest(idStore.targetId, adresseRef.value, villeRef.value, CPref.value, priceRef.value, dateVisiteRef.value, particularDemandsRef.value);
  } catch (e) {
    console.error(e);
  } finally {
    console.log('yes')
    await router.replace('/home')
  }
}

async function onPay() {
  try {
    console.log(visit)
    await payVisitRequest(visit.id);
  } catch (e) {
    console.error(e);
  } finally {
    console.log('yes')
    await router.replace('/home')
  }
}

async function linkVisitor(visitId, visiteurId, index) {
  responseDisabled.value[index] = true
  try {
    await sendVisitRequest(visitId, visiteurId);
  } catch (e) {
    console.error(e);
  } finally {
    console.log('yes')
    //await router.replace('/home')
  }
}

async function onReaserch() {
  try {
    responseData.value = await searchVisitors(lieuRef.value, dateRef.value, hourRef.value)
    console.log(toRaw(responseData.value))
    return toRaw(responseData.value)
  } catch (e) {
    console.error(e);
  } finally {
  }
}

function goToUserProfile(id) {
  sessionStorage.setItem('voyo-target-profile', id);
  router.replace('/profil/view')
}

onMounted(() => {
  console.log(visit['verificationPoints'])
  visit['verificationPoints'].forEach(verificationPoint => {
    particularDemandsRef.value.push(verificationPoint.title)
  });
})
</script>
