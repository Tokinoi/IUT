<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Profil" />

    <img :src="profile?.img" alt="">
    <q-page-container class="q-px-md q-pt-md q-pa-none text-center">
      <q-spinner
        v-if="loading"
        color="primary"
        size="100px"
        class="q-mt-xl"
      />
      <q-file v-model="imgRef" v-else-if="!imgRef" style="margin: 0 calc(50% - 100px) 0 calc(50% - 100px);" borderless rounded>
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
      <q-form @submit="onSubmit" greedy class="q-mb-md q-gutter-md">
        <q-input v-model="emailRef" type="text" label="Email" :rules="[rules.required]" />
        <q-input v-model="cityRef" type="text" label="Ville" />
        <q-input v-model="radiusRef" type="text" label="Radius" />
        <q-input v-model="bioRef" type="textarea" label="Biographie" :rules="[rules.required]" />
        <q-input v-model="availabilityStartingHour" type="time" label="Heure de début" :rules="[rules.required]" />
        <q-input v-model="availabilityEndingHour" type="time" label="Heure de fin" :rules="[rules.required]" />
        <q-checkbox v-model="monday" label="Lundi" />
    <q-checkbox v-model="tuesday" label="Mardi" />
    <q-checkbox v-model="wednesday" label="Mercredi" />
    <q-checkbox v-model="thursday" label="Jeudi" />
    <q-checkbox v-model="friday" label="Vendredi" />
    <q-checkbox v-model="saturday" label="Samedi" />
    <q-checkbox v-model="sunday" label="Dimanche" />

        <div class="text-center">
          <q-btn label="Enregistrer" type="submit" color="primary" class="text-black" />
        </div>

        <div class="text-center">
          <q-btn @click="newPWDialogRef = true" label="Réinitialiser le mot de passe" type="submit" color="primary"
            class="text-black" />
        </div>

      </q-form>
    </q-page-container>

    <ToolBarComponent profil />

    <q-dialog v-model="newPWDialogRef">
      <q-card>
        <q-card-section class="row items-center">
          <q-form @submit="onNewPassword" class="q-gutter-md">
            <q-input v-model="newPWRef" type="text" label="Nouveau mot de passe" :rules="[rules.password]" />
            <div>
              <q-btn label="Enregistrer" type="submit" color="primary" text-color="black" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

  </q-layout>
</template>

<script setup>
import HeaderComponent from 'src/components/HeaderComponent.vue';
import ToolBarComponent from 'src/components/ToolBarComponent.vue';
import { editProfile, newPassword, getUserProfileImg, getOwnProfile, profile } from '../../composables/api';
import { ref, watch } from 'vue';
import rules from 'src/composables/rules'

const newPWDialogRef = ref(false)

const loading = ref(true)
const newPWRef = ref('')
const imgRef = ref(null)
const emailRef = ref('')
const cityRef = ref('')
const radiusRef = ref(0)
const bioRef = ref('')
const availabilityEndingHour = ref('')
const availabilityStartingHour = ref('')
const monday = ref(false)
const tuesday = ref(false)
const wednesday = ref(false)
const thursday = ref(false)
const friday = ref(false)
const saturday = ref(false)
const sunday  = ref(false)


async function onSubmit() {
  var bodyFormData = new FormData();
  let temp = []
  if (monday.value) {
  temp.push('Mon');
}

if (tuesday.value) {
  temp.push('Tue');
}

if (wednesday.value) {
  temp.push('Wed');
}

if (thursday.value) {
  temp.push('Thu');
}

if (friday.value) {
  temp.push('Fri');
}

if (saturday.value) {
  temp.push('Sat');
}

if (sunday.value) {
  temp.push('Sun');
}
temp = JSON.stringify(temp)
  bodyFormData.append('email', emailRef.value);
  bodyFormData.append('city', cityRef.value);
  bodyFormData.append('radius', radiusRef.value);
  bodyFormData.append('biography', bioRef.value);
  bodyFormData.append('availabilityStartingHour', availabilityStartingHour.value);
  bodyFormData.append('availabilityDays', temp);
  bodyFormData.append('availabilityEndingHour', availabilityEndingHour.value);
  bodyFormData.append('photo', imgRef.value);
  editProfile(bodyFormData)
}


async function onNewPassword() {
  await newPassword(newPWRef.value)
  newPWDialogRef.value = false
}

watch(profile, () => {
  console.log('PROFILE:', profile)
  emailRef.value = profile.value.email || ''
  cityRef.value = profile.value.city || ''
  radiusRef.value = profile.value.radius || 0
  bioRef.value = profile.value.biography || ''
  availabilityEndingHour.value = profile.value.availabilityEndingHour || ''
  availabilityStartingHour.value = profile.value.availabilityStartingHour || ''
  monday.value = profile.value['availabilityDays']?.includes('Mon') || false
  tuesday.value = profile.value['availabilityDays']?.includes('Tue') || false
  wednesday.value = profile.value['availabilityDays']?.includes('Wed') || false
  thursday.value = profile.value['availabilityDays']?.includes('Thu') || false
  friday.value = profile.value['availabilityDays']?.includes('Fri') || false
  saturday.value = profile.value['availabilityDays']?.includes('Sat') || false
  sunday.value = profile.value['availabilityDays']?.includes('Sun') || false

  getUserProfileImg()
  loading.value = false
})

getOwnProfile()
</script>

<style scoped>
.hoverable:hover {
  background-color: #ddd !important;
}
</style>
