<template>
  <q-page-container style="padding-bottom: 0;" class="bg-yellow-1" >
    <div class="q-px-sm">
      <div class="flex justify-between">
        <q-btn flat round @click="notifRef = true" class="text-primary bg-none">
          <q-icon name="far fa-bell" />
        </q-btn>
        <span class="q-mt-sm text-subtitle1">{{ props.title }}</span>
        <q-btn
          flat
          round
          @click="logoutRef = true"
          class="text-primary bg-none"
        >
          <q-icon name="fas fa-arrow-right-from-bracket" />
        </q-btn>
      </div>
    </div>
  </q-page-container>

  <q-dialog v-model="logoutRef">
    <q-card class="q-pa-sm">
      <span class="text-subtitle1">Déconnexion</span>
      <q-card-section class="row items-center">
        <span class="q-ma-xs">Voulez-vous vous déconnecter ?</span>
      </q-card-section>
      <q-btn flat label="Annuler" color="primary" v-close-popup />
      <q-btn
        flat
        @click="onLogout"
        label="Se déconnecter"
        color="primary"
        v-close-popup
      />
    </q-card>
  </q-dialog>

  <q-dialog v-model="notifRef">
    <q-card class="q-pa-sm">
      <span class="text-subtitle1">Notifications</span>
      <q-card-section class="row items-center">
        <span class="q-ma-xs">Vous n'avez aucune notification</span>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { ref } from 'vue';

const props = defineProps({
  title: String,
});
const router = useRouter();
const logoutRef = ref(false);
const notifRef = ref(false);

function onLogout() {
  sessionStorage.removeItem('voyo-token');
  sessionStorage.removeItem('voyo-roles');
  sessionStorage.removeItem('voyo-target-profile');
  sessionStorage.removeItem('voyo-visitId');
  router.replace('/');
}
</script>
