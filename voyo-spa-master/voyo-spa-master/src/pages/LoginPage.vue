<template>
  <main>
    <q-card class="rounded-border-lg q-ma-lg full-height q-py-md">
      <q-card-section class="text-center">
        <q-img
          src="src/assets/voyo-logo-grey.png"
          style="width: 200px"
          spinner-color="primary"
          spinner-size="82px"
        />
      </q-card-section>
      <q-card-section class="q-py-none">
        <div class="text-h6 text-h1">Se connecter</div>
      </q-card-section>
      <q-form @submit="onLogin" greedy class="q-gutter-md">
        <q-card-section class="text-center">
          <q-input
            v-model="emailRef"
            type="email"
            label="Email"
            :rules="[rules.email]"
          />
          <q-input
            v-model="passwordRef"
            :type="isPwd ? 'password' : 'text'"
            label="Mot de passe"
            :rules="[rules.password]"
          >
            <template v-slot:append>
              <q-icon
                :name="isPwd ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="isPwd = !isPwd"
              />
            </template>
          </q-input>
          <q-btn type="submit" class="q-mt-lg bg-primary">Se connecter</q-btn>
        </q-card-section>
      </q-form>
      <section class="text-center">
        <div class="flex justify-around text-center q-my-md">
          <a @click="$router.replace('/forgottenPassword')" class="text-grey-8">
            Mot de passe oublié ?
          </a>
          <a @click="$router.replace('/register')" class="text-grey-8">
            Créer un compte
          </a>
        </div>
      </section>
    </q-card>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { loginUser, getRoles } from '../composables/api';
import rules from 'src/composables/rules';
const router = useRouter();
const emailRef = ref('');
const passwordRef = ref('');
const isPwd = ref(true);

async function onLogin() {
  let token;
  let roles;
  try {
    token = await loginUser(emailRef.value, passwordRef.value);
  } catch (e) {
    console.error(e);
  } finally {
    sessionStorage.setItem('voyo-token', token.data['token']);

    try {
      roles = await getRoles(token.data['token']);
    } catch (e) {
      console.error(e);
    } finally {
      sessionStorage.setItem('voyo-roles', roles.data.roles);
      console.log(roles.data)
      if(roles.data.roles.includes('ROLE_VISITOR')){
        router.replace('/home/visitor')
      }
      else{router.replace('/home');}
    }
  }
}
</script>

<style lang="scss">
.rounded-border-lg {
  border-radius: 20px;
}

main {
  margin-top: 10%;
  margin-bottom: 10%;
}
</style>
