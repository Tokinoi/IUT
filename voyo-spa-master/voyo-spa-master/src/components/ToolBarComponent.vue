<template>
  <q-footer>
    <q-toolbar class="bg-secondary text-black justify-around">
      <div>
        <a
          @click="goTo('/home')"
          class="cursor-pointer"
          :class="props.home ? 'text-primary' : ''"
        >
          <q-icon name="fas fa-list-ul" size="sm" />
          <span class="q-ml-sm text-weight-bold">Visites</span>
        </a>
      </div>
      <div>
        <a
          @click="goTo('/contacts')"
          class="cursor-pointer"
          :class="props.contacts ? 'text-primary' : ''"
        >
          <q-icon name="far fa-comments" size="sm" />
          <span class="q-ml-sm text-weight-bold">Messages</span>
        </a>
      </div>
      <div>
        <a
          @click="goTo('/profil')"
          class="cursor-pointer"
          :class="props.profil ? 'text-primary' : ''"
        >
          <q-icon name="far fa-user" size="sm" />
          <span class="q-ml-sm text-weight-bold">Profil</span>
        </a>
      </div>
    </q-toolbar>
  </q-footer>
</template>

<script setup>
import { useRouter } from 'vue-router';

const props = defineProps({
  home: Boolean,
  contacts: Boolean,
  profil: Boolean,
});
const router = useRouter();

function goTo(url) {
  let roles = sessionStorage.getItem('voyo-roles');
  let visitorUrls = ['/home', '/profil']
  console.log('mesroles', roles)
  if(roles.includes('ROLE_VISITOR') && visitorUrls.includes(url)) {
    router.replace(url + '/visitor')
  } else {
    sessionStorage.removeItem('voyo-target-profile');
    router.replace(url)
  }
}
</script>
