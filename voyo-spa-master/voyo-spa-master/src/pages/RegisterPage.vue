<template>
  <main>
    <q-card class="rounded-border-lg q-ma-lg full-height q-py-md">
      <q-card-section>
        <div class="text-h6 text-h1">Créer un compte</div>
      </q-card-section>
      <q-form @submit="onNewUser" greedy class="q-gutter-md">
        <q-card-section>
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
          <q-input
            v-model="lastNameRef"
            type="text"
            label="Nom"
            :rules="[rules.required]"
          />
          <q-input
            v-model="firstNameRef"
            type="text"
            label="Prénom"
            :rules="[rules.required]"
          />
        </q-card-section>
        <section class="text-center">
          <q-btn type="submit" class="bg-primary"> S'enregistrer </q-btn>
          <div class="text-right q-my-md">
            <a @click="$router.replace('/login')" class="q-ma-lg text-grey-8">
              Déjà un compte ?
            </a>
          </div>
        </section>
      </q-form>
    </q-card>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { createUser } from '../composables/api'
import rules from 'src/composables/rules'
const router = useRouter()
const emailRef = ref('')
const passwordRef = ref('')
const isPwd = ref(true)
const lastNameRef = ref('')
const firstNameRef = ref('')

async function onNewUser() {
  let response
  try {
    response = await createUser(
      emailRef.value,
      passwordRef.value,
      firstNameRef.value,
      lastNameRef.value
    )
    if (response) router.replace(response.data['redirect_to'])
  } catch (e) {
    console.error(e)
  }
}
</script>

<style lang="scss"></style>
