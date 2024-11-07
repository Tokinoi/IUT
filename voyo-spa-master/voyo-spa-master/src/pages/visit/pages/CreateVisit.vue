<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Demandes de visite" />
    <q-page-container class="q-pa-md">
      <q-form @submit="onSubmit" greedy class="q-gutter-md text-center">
        <div>
          <q-input
            v-model="villeRef"
            type="text"
            label="Ville"
            :rules="[rules.required]"
          />
          <q-input
            v-model="CPRef"
            type="text"
            label="Code Postal"
            :rules="[rules.required, rules.codePostal, rules.codePostalSize]"
          />
          <q-input
            v-model="complementRef"
            type="text"
            label="Complément d'adresse"
            :rules="[rules.required]"
          />
          <q-input
            v-model="priceRef"
            type="text"
            label="Coût de la visite"
            :rules="[rules.required, rules.number]"
          />
          <q-input
            v-model="dateVisitRef"
            type="text"
            label="Date de la visite"
            :rules="[rules.required]"
          />
          <q-input
            v-model="newparticularDemandRef"
            type="text"
            label="Demande particulière"
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
                @click="removeParticularDemand(index)"
                round
                flat
                icon="remove"
                class="q-mt-none"
              />
            </div>
          </div>

          <q-btn
            label="Créer une demande de visite"
            type="submit"
            color="primary"
            class="q-my-lg text-black"
          />
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
import { createVisitRequest, addVerificationPoint } from 'src/pages/visit/composables/api';
import { ref } from 'vue'
import { useRouter } from 'vue-router'
const router = useRouter()

const villeRef = ref('')
const CPRef = ref('')
const complementRef = ref('')
const dateVisitRef = ref('')
const priceRef = ref('')
const particularDemandsRef = ref([])
const newparticularDemandRef = ref([])

function addParticularDemand() {
  particularDemandsRef.value.push(newparticularDemandRef.value);
  newparticularDemandRef.value = ''
}

function removeParticularDemand(value) {
  particularDemandsRef.value.splice(value, 1)
}

async function onSubmit() {
  let response;
  try {
    response = await createVisitRequest(complementRef.value, villeRef.value, CPRef.value, priceRef.value, dateVisitRef.value);
    return response
  } catch (e) {
    console.error(e);
  } finally {
    console.log('onSubmitResponse', response.data['visit-request-id'])
    try {
      await addVerificationPoint(response.data['visit-request-id'], particularDemandsRef.value)
    } catch (e) {
      console.error(e);
    } finally {
      await router.replace('/home');
    }
  }
}
</script>
