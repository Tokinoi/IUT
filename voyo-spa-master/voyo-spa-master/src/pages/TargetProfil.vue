<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Profil"/>

    <img :src="targetUser?.img" alt="">
    <q-page-container class="q-px-md q-pt-md q-pa-none text-center">
      <q-file v-model="imgRef" v-if="!imgRef" style="margin: 0 calc(50% - 100px) 0 calc(50% - 100px);" borderless rounded>
        <q-icon name="person" style="width: 200px; height: 200px; border-radius: 100px;" color="grey-8" size="100px" class="bg-grey-1 cursor-pointer hoverable" />

      </q-file>
      <q-avatar v-else style="width: 200px; height: 200px;">
        <img src="https://cdn.quasar.dev/img/avatar.png">
      </q-avatar>
      <div class="text-center">
        <span>{{ imgRef?.name }}</span>
        <h5 class="q-ma-none q-mt-md+">{{ targetUser?.displayName }}</h5>
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
          readonly
        />
        <q-input
          v-model="cityRef"
          type="text"
          label="Ville"
          readonly
        />
        <q-input
          v-model="radiusRef"
          type="text"
          label="Rayon"
          readonly
        />
        <q-input
          v-model="bioRef"
          type="textarea"
          label="Biographie"
          readonly
        />
        <div class="text-center">
          <q-btn :disable="!targetUser" @click="goToMessage()" icon="send" label="Envoyer un message" color="primary" class="text-black"/>
        </div>

      </q-form>
    </q-page-container>

    <ToolBarComponent profil />
  </q-layout>
</template>

<script setup>
import HeaderComponent from 'src/components/HeaderComponent.vue';
import ToolBarComponent from 'src/components/ToolBarComponent.vue';
import { getProfile, getUserProfileImg } from 'src/composables/api';
import { ref } from 'vue';
import { useRouter } from 'vue-router'
const router = useRouter()

const targetUser = ref(null)

const imgRef = ref(null)
const emailRef = ref('')
const cityRef = ref('')
const radiusRef = ref(0)
const bioRef = ref('')

function goToMessage() {
  sessionStorage.setItem('voyo-target-profile', targetUser.value.id);
  router.replace('/contacts')
}

async function loadUserImg() {
  imgRef.value = await getUserProfileImg()
}

loadUserImg()

async function loadUser() {
  targetUser.value = await getProfile()
  emailRef.value = targetUser.value?.email
  cityRef.value = targetUser.value?.city
  radiusRef.value = targetUser.value?.radius
  bioRef.value = targetUser.value?.biography
}

loadUser()

</script>