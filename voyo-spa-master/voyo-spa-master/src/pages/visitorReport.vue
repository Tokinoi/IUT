<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Compte rendu" />

    <q-page-container class="q-pa-md">
      <q-form @submit="onSubmit" greedy class="q-gutter-md text-center">
        <div>
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
          <q-input
            v-model="priceRef"
            type="text"
            label="Coût de la visite"
            :rules="[rules.required, rules.number]"
            readonly
          />
          <q-input
            v-model="dateVisiteRef"
            type="text"
            label="Date de la visite"
            :rules="[rules.required]"
            readonly
          />

          <h6 class="q-mb-xs">Points particulier</h6>
          <div
            v-for="(condition, index) in particularDemandsRef"
            :key="condition"
            class="q-mt-sm text-left"
          >
            <div class="no-wrap justify-between">
              <div class="flex">
                <span style="align-items: center; display: grid"
                  >Demande n°{{ index + 1 }}: {{ condition.title }}</span
                >
                <q-checkbox
                  v-model="condition.status"
                  label="Status"
                  :rules="[rules.required]"
                />
              </div>
              <q-input
                v-model="condition.comment"
                type="textarea"
                label="Commentaire"
                :rules="[rules.required]"
              />
            </div>
          </div>

          <q-input
            v-model="reportRef"
            type="textarea"
            label="Compte-rendu"
          />
          <div style="display: block;">
            <q-btn
              label="Enregistrer le rapport"
              color="primary"
              class="q-mt-lg q-mx-xl text-black"
              @click="onSend(reportRef)"
            />

            <q-btn
              label="Clôturer la demande"
              type="submit"
              color="primary"
              class="q-my-lg text-black"
              @click="onSubmit()"
            />
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
import { editReport, terminateVisit, validateParticularDemand } from 'src/pages/visit/composables/api';
import { ref, toRaw } from 'vue';
import { useRouter } from 'vue-router'
import { useVisitStore } from 'stores/targetVisit';
import { useIdStore } from 'stores/targetId';
const router = useRouter()

const store = useVisitStore()
let visit = toRaw(store.targetVisit);

const idStore = useIdStore()
let id = sessionStorage.getItem('voyo-visitId')

console.log(id)
console.log(visit)
console.log(idStore.targetId)

const nomClientRef = ref(visit['client']['displayName'])
const emailClientRef = ref(visit['client']['email'])
const villeClientRef = ref(visit['client']['city'])
const adresseClientRef = ref(visit['client']['address'])
const adresseRef = ref(visit['propertyAddress'])
const villeRef = ref(visit['propertyCity'])
const CPref = ref(visit['propertyPostalCode'])
const dateVisiteRef = ref(visit['scheduledAt'])
const priceRef = ref(visit['price'])
const particularDemandsRef = ref(visit['verificationPoints'])
const reportRef = ref(visit['report'])

async function onParticularDemand(id, particularDemand) {
  await validateParticularDemand(id, particularDemand);
}


async function onSend(decision) {
  particularDemandsRef.value.forEach(particularDemand => {
    try {
      console.log(id, particularDemand.comment)
      onParticularDemand(particularDemand.id, particularDemand.comment)
    } catch (e) {
      console.error(e);
    }
  });

  try {
    console.log(id, decision)
    await editReport(id, decision);
  } catch (e) {
    console.error(e);
  } finally {
    await router.replace('/home/visitor')
  }
}

async function onSubmit() {
  try {
    await terminateVisit(id)
  } catch (e) {
    console.error(e);
  } finally {
    await router.replace('/home/visitor')
  }
}
</script>
