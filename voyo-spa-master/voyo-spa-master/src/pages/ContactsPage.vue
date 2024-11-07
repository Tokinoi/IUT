<template>
  <q-layout view="lHh lpr lFf">
    <HeaderComponent title="Messagerie" />
    <div v-if="!loading" style="margin-bottom: 200px;">
      <q-page-container v-if="!currentVisitor" class="q-pa-md">
        <p class="text-subtitle2">Contacts</p>
        <q-list v-for="userId in formattedMessages" :key="userId" bordered round class="q-ma-none bg-white">
          <q-item @click="openVisitorMessages(userId)" class="q-my-sm" clickable v-ripple>
            <q-item-section>
              <q-item-label>{{ contactsNames[userId] || 'Aucune information' }}</q-item-label>
            </q-item-section>

            <q-item-section side>
              <q-icon name="chat_bubble" color="orange" />
            </q-item-section>
          </q-item>
        </q-list>
      </q-page-container>

      <q-page-container v-else class="q-pa-md">
        <p class="flex text-subtitle2">
          Messages avec {{ currentVisitor.displayName }}
          <span class="q-ml-xs text-grey-9">, {{ currentVisitor.city }}</span>
        </p>

        <div class="q-pa-md row justify-center">
          <q-chat-message
            label="Vendredi 12/04"
          />
          <div v-for="message in messageHistory" :key="message.timestamp" style="width: 100%; max-width: 400px">
            <q-chat-message
              v-if="message.sender.id === profile.id && message.recipient.id === currentVisitor.id"
              name="moi"
              :avatar="currentVisitor.img"
              :text="[message.content]"
              sent
              :stamp="message.timestamp.split(' ')[1]"
            />
            <q-chat-message
              v-else-if="message.sender.id === currentVisitor.id"
              :name="currentVisitor.displayName"
              :avatar="currentVisitor.img"
              :text="[message.content]"
              bg-color="orange"
              :stamp="message.timestamp.split(' ')[1]"
            />
          </div>
        </div>

        <q-input class="footer" rounded outlined v-model="messageRef">
          <template v-slot:append>
            <q-btn @click="onSubmitMessage" round dense flat text-color="primary" icon="send" />
          </template>
        </q-input>
      </q-page-container>
    </div>
    <div v-else>
      <q-item style="max-width: 100%">
        <q-item-section avatar>
          <q-skeleton type="QAvatar" />
        </q-item-section>

        <q-item-section>
          <q-item-label>
            <q-skeleton type="text" />
          </q-item-label>
          <q-item-label caption>
            <q-skeleton type="text" width="65%" />
          </q-item-label>
        </q-item-section>
      </q-item>

      <q-item style="max-width: 90%">
        <q-item-section avatar>
          <q-skeleton type="QAvatar" />
        </q-item-section>

        <q-item-section>
          <q-item-label>
            <q-skeleton type="text" />
          </q-item-label>
          <q-item-label caption>
            <q-skeleton type="text" width="90%" />
          </q-item-label>
        </q-item-section>
      </q-item>

      <q-item style="max-width: 96%">
        <q-item-section avatar>
          <q-skeleton type="QAvatar" />
        </q-item-section>

        <q-item-section>
          <q-item-label>
            <q-skeleton type="text" width="35%" />
          </q-item-label>
          <q-item-label caption>
            <q-skeleton type="text" />
          </q-item-label>
        </q-item-section>
      </q-item>
    </div>

    <ToolBarComponent class="q-mt-xl" contacts />
  </q-layout>
</template>

<script setup>
import HeaderComponent from 'src/components/HeaderComponent.vue'
import ToolBarComponent from 'src/components/ToolBarComponent.vue'
import { sendMessage, retrieveMessages, getProfile, profile } from '../composables/api';
import { ref } from 'vue'

const currentVisitor = ref(null)
const messageHistory = ref(null)
const formattedMessages = ref([])
const contactsNames = ref([])
const messageRef = ref('')
const loading = ref(true)

function openVisitorMessages(visitorId) {
  currentVisitor.value = visitorId
  sessionStorage.setItem('voyo-target-profile', visitorId);
  loadUser()
}

async function onSubmitMessage() {
  await sendMessage(profile.value.id, currentVisitor.value.id, messageRef.value)
  messageRef.value = ''
}

function formatMessageHistory() {
  messageHistory.value?.forEach(message => {
    if (message.sender.id !== profile.value.id) {
      if (formattedMessages.value.indexOf(message.sender.id) === -1) {
        formattedMessages.value.push(message.sender.id)
        contactsNames.value[message.sender.id] = message.sender.displayName
      }
    }
  });
}

async function loadMessages() {
  messageHistory.value = await retrieveMessages()
  loading.value = false
  formatMessageHistory()
}

loadMessages()

// Is it dirty? Yes. Is it stupid? Yes. Does it works? Yes! Welcome to Javascript (Hell)
if (!(window.setInterval > 0)) window.setInterval = setInterval(loadMessages, 1000)

async function loadUser() {
  currentVisitor.value = await getProfile()
}

loadUser()
</script>

<style scoped>
.footer {
  position: fixed;
  bottom: 70px;
  width: calc(100% - 30px);
  background-color: white;
  border-radius: 30px;
}
</style>
