<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Modifier une demande" />

    <q-page-container class="q-pa-md">
      <q-form @submit="onSubmit" class="q-gutter-md text-center">
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

          <div class="flex text-center">
            <q-btn
              label="Accepter"
              color="primary"
              class="q-mr-md text-black"
              type="submit"
            />
            <q-btn
              @click="onSubmit('reject')"
              class="text-black"
              color="primary"
              label="Refuser"
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
import { acceptOrDecline } from 'src/pages/visit/composables/api';
import { ref, toRaw } from 'vue';
import { useRouter } from 'vue-router'
import { useVisitStore } from 'stores/targetVisit';
import { useIdSoliStore } from 'stores/targetIdSoli';
const router = useRouter()

const store = useVisitStore()
let visit = toRaw(store.targetVisit);

const idSoliStore = useIdSoliStore()
console.log(idSoliStore.targetId)

const nomClientRef = ref(visit['client']['displayName'])
const emailClientRef = ref(visit['client']['email'])
const villeClientRef = ref(visit['client']['city'])
const adresseClientRef = ref(visit['client']['address'])
const adresseRef = ref(visit['propertyAddress'])
const villeRef = ref(visit['propertyCity'])
const CPref = ref(String(visit['propertyPostalCode']).trim())
const dateVisiteRef = ref(visit['scheduledAt'])
const priceRef = ref(visit['price'])
const userRef = ref(visit['isTheConnectedUserTheVisitor'])
console.log(userRef.value)

async function onSubmit() {
  try {
    console.log(idSoliStore.targetId, 'accept')
    await acceptOrDecline(idSoliStore.targetId, 'accept');
  } catch (e) {
    console.error(e);
  } finally {
    console.log(idSoliStore.targetId, 'accept')
    await router.replace('/home/visitor')
  }
}
</script>
