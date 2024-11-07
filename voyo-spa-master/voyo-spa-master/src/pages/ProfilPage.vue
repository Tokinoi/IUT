<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Profil"/>
    <img :src="profile?.img" alt="">
    <q-page-container class="q-px-md q-pt-md q-pa-none text-center">
      <q-spinner
          v-if="loading"
          color="primary"
          size="100px"
          class="q-mt-xl"
        />
      <q-file v-else-if="!imgRef" v-model="imgRef" style="margin: 0 calc(50% - 100px) 0 calc(50% - 100px);" borderless rounded>
        <q-icon name="person" style="width: 200px; height: 200px; border-radius: 100px;" color="grey-8" size="100px" class="bg-grey-1 cursor-pointer hoverable" />

      </q-file>
      <q-avatar v-else style="width: 200px; height: 200px;">
        <img src="https://cdn.quasar.dev/img/avatar.png">
      </q-avatar>
      <div class="q-mt-md text-center">
        <span>{{ imgRef?.name }}</span>
        <h5 class="q-ma-none q-mt-md+">{{ profile.displayName }}</h5>
      </div>
    </q-page-container>

    <q-page-container class="q-px-md">
      <q-form
        @submit="onSubmit"
        class="q-mb-md q-gutter-md"
      >
        <q-input
          v-model="emailRef"
          type="text"
          label="Email"
        />
        <q-input
          v-model="cityRef"
          type="text"
          label="Ville"
        />
        <q-input
          v-model="radiusRef"
          type="text"
          label="Rayon"
        />
        <q-input
          v-model="bioRef"
          type="textarea"
          label="Biographie"
        />
        <div class="text-center">
          <q-btn label="Enregistrer" type="submit" color="primary" class="text-black"/>
        </div>
        <div v-if="!isVisitor()" class="text-center">
          <q-btn @click="visitorRequestDialog = true" label="Devenir Visiteur" color="primary" class="text-black"/>
        </div>
        <div class="text-center">
          <q-btn @click="newPWDialogRef = true" label="Réinitialiser le mot de passe" type="submit" color="primary" class="text-black"/>
        </div>

      </q-form>
    </q-page-container>

    <ToolBarComponent profil />

    <q-dialog v-model="newPWDialogRef">
      <q-card>
        <q-card-section class="row items-center">
          <q-form
            @submit="onNewPassword"
            class="q-gutter-md"
          >
            <q-input
              v-model="newPWRef"
              type="text"
              label="Nouveau mot de passe"
              :rules="[rules.password]"
            />
            <div>
              <q-btn label="Enregistrer" type="submit" color="primary" text-color="black"/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="visitorRequestDialog">
      <q-card>
        <q-card-section v-if="!showKeyVisitor" class="row items-center">
          <q-form
            @submit="becomeVisitor" greedy
            class="q-gutter-md q-pa-md text-center"
          >
            <q-input
              v-model="hourlyRate"
              type="text"
              label="Taux horaire"
              :rules="[rules.required, rules.price]"
            >
              <template v-slot:append>
                €
              </template>
            </q-input>
            <q-input
              v-model="address"
              type="text"
              label="Adresse"
              :rules="[rules.required]"
            />
            <q-input
              v-model="phoneRef"
              type="text"
              label="Numéro de Téléphone"
              :rules="[rules.required, rules.phone]"
            >
              <template v-slot:prepend>
                +33
              </template>
            </q-input>
            <q-btn label="Soumettre" type="submit" color="primary" text-color="black"/>
          </q-form>
        </q-card-section>

        <q-card-section v-else>
          <q-form
            @submit="becomeVisitorWithCode" greedy
            class="q-gutter-md q-pa-md text-center"
          >
            <q-input
              v-model="code"
              type="text"
              label="Code"
              :rules="[rules.required]"
            />
            <q-btn label="Soumettre le code" type="submit" color="primary" text-color="black"/>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-layout>
</template>

<script setup>
import HeaderComponent from 'src/components/HeaderComponent.vue';
import ToolBarComponent from 'src/components/ToolBarComponent.vue';
import {
  visitorRequest,
  visitorRequestWithCode,
  editProfile,
  newPassword,
  getUserProfileImg,
  getOwnProfile,
  profile
 } from '../composables/api';
import { ref, watch, onMounted } from 'vue';
import rules from 'src/composables/rules'

const newPWDialogRef = ref(false)
const visitorRequestDialog = ref(false)

const newPWRef = ref('')
const imgRef = ref(null)
const hourlyRate = ref('')
const emailRef = ref('' || profile.email)
const cityRef = ref('' || profile.city)
const radiusRef = ref(0 || profile.radius)
const bioRef = ref('' || profile.biography)
const address = ref('')
const phoneRef = ref(0)
const responseVisitorRequest = ref(null)
const showKeyVisitor = ref(true)
const code = ref(0)
const Responsecode = ref(0)
const loading = ref(true)

async function becomeVisitor() {
  responseVisitorRequest.value = await visitorRequest(hourlyRate.value, address.value, '+33' + phoneRef.value)
  console.log('responseVisitorRequest', responseVisitorRequest.value)
  showKeyVisitor.value = true
  //visitorRequestDialog.value = false
}

function isVisitor() {
  let roles = sessionStorage.getItem('voyo-roles');
  return roles.includes('ROLE_VISITOR')
}

async function becomeVisitorWithCode() {
  Responsecode.value = await visitorRequestWithCode(code.value)

  let roles
  try {
    roles = await getRoles();
  } catch (e) {
    console.error(e);
  } finally {
    sessionStorage.setItem('voyo-roles', roles.data.roles);
  }

  showKeyVisitor.value = false
  visitorRequestDialog.value = false
}

async function onSubmit() {
  var bodyFormData = new FormData();
  bodyFormData.append('email', emailRef.value);
  bodyFormData.append('city', cityRef.value);
  bodyFormData.append('radius', radiusRef.value);
  bodyFormData.append('biography', bioRef.value);
  //bodyFormData.append('availabilityStartingHour', content.availabilityStartingHour);
  //bodyFormData.append('availabilityDays', content.availabilityDays);
  //bodyFormData.append('availabilityEndingHour', content.availabilityEndingHour);
  bodyFormData.append('photo',  imgRef.value);
  editProfile(bodyFormData)
}

async function onNewPassword() {
  await newPassword(newPWRef.value)
  newPWDialogRef.value = false
}

async function loadUserImg() {
  imgRef.value = await getUserProfileImg()
}

loadUserImg()

onMounted(() => {
  getOwnProfile()
})

watch(profile, () => {
  console.log('PROFILE:', profile)
  emailRef.value = profile.value.email || ''
  cityRef.value = profile.value.city || ''
  radiusRef.value = profile.value.radius || 0
  bioRef.value = profile.value.biography || ''

  loading.value = false
})
</script>

<style scoped>
.hoverable:hover {
  background-color: #ddd !important;
}
</style>
