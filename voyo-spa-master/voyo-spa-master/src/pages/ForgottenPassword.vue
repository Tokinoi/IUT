<template>
  <main>
    <q-card class="rounded-border-lg q-ma-lg full-height q-py-md" height="100%">
      <q-btn @click="$router.replace('/login')" class="q-ma-md bg-primary"
        >Retour</q-btn
      >
      <q-card-section class="q-py-none">
        <div class="text-h6 text-h1">Mot de passe oublié</div>
      </q-card-section>
      <div v-if="!isKeySent">
        <q-form @submit="onReset" greedy class="q-gutter-md">
          <q-card-section class="text-center">
            <q-input
              v-model="emailRef"
              type="email"
              label="Email"
              :rules="[rules.email]"
            />
            <q-btn v-if="!isKeySent" type="submit" class="q-mx-md bg-primary">
              Envoyer une clé de réinitialisation
            </q-btn>
          </q-card-section>
        </q-form>
      </div>
      <div v-else>
        <q-form @submit="onReset" greedy class="q-gutter-md">
          <q-card-section class="text-center">
            <q-input
              v-model="keyRef"
              type="text"
              label="Clé de réinitialisation"
              :rules="[rules.required]"
            />
            <q-input
              v-model="passwordRef"
              :type="isPwd ? 'password' : 'text'"
              label="Nouveau mot de passe"
              :rules="[rules.required]"
            >
              <template v-slot:append>
                <q-icon
                  :name="isPwd ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
              </template>
            </q-input>
            <q-btn type="submit" class="q-mx-md bg-primary">
              Réinitialiser le mot de passe
            </q-btn>
          </q-card-section>
        </q-form>
      </div>
      <section class="text-center"></section>
    </q-card>
  </main>
</template>

<script setup>
import { getRKey } from 'src/composables/api';
import { ref } from 'vue';
import rules from 'src/composables/rules';
const emailRef = ref('');
const keyRef = ref('');
const passwordRef = ref('');
const isPwd = ref(true);
const isKeySent = ref(false);

function onReset() {
  const response = getRKey(emailRef);
  console.log('response', response);
  isKeySent.value = true;
}
</script>

<style lang="scss"></style>
