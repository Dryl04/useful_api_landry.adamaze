<template>
  <main>
    <div class="bg-gray-100 flex h-screen items-center justify-center px-4 sm:px-6 lg:px-8">
      <div class="w-full max-w-md space-y-8">
        <div class="bg-white shadow-md rounded-md p-6">
          <img
            class="mx-auto h-12 w-auto"
            src="https://www.svgrepo.com/show/499664/user-happy.svg"
            alt=""
          />

          <h2 class="my-3 text-center text-3xl font-bold tracking-tight text-gray-900">
            Connexion
          </h2>

          <form @submit.prevent="userLogin" class="space-y-6" method="POST">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <div class="mt-1">
                <input
                  v-model="userEmail"
                  name="email"
                  type="email"
                  autocomplete="email"
                  required
                  class="px-2 py-3 mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-sky-500 sm:text-sm"
                />
              </div>
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-gray-700"
                >Mot de passe</label
              >
              <div class="mt-1">
                <input
                  v-model="userPassword"
                  name="password"
                  type="password"
                  autocomplete="current-password"
                  required
                  class="px-2 py-3 mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-sky-500 sm:text-sm"
                />
              </div>
            </div>

            <div>
              <button
                type="submit"
                class="flex w-full justify-center rounded-md border border-transparent bg-sky-400 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 hover:cursor-pointer hover:bg-sky-600"
              >
                Se connecter
              </button>
            </div>
          </form>
          <div
            v-if="error"
            class="flex items-center justify-center text-red-600 border-1 rounded-lg mt-5 h-10 font-semibold"
          >
            {{ error }}
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { useUserStore } from '../stores/UserStore.js'
import { ref } from 'vue'

const UserStore = useUserStore()

const userEmail = ref('')
const userPassword = ref('')
const error = ref(null)
let errorTimeout = null

function setError(message) {
  error.value = message

  if (errorTimeout) {
    clearTimeout(errorTimeout)
  }

  errorTimeout = setTimeout(() => {
    error.value = null
  }, 5000)
}

function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return regex.test(email)
}

async function userLogin() {
  try {
    if (!validateEmail(userEmail.value)) {
      setError('Email incorrect')
      return
    }

    if (userPassword.value.length < 1) {
      setError('Veuillez insérer un mot de passe')
      return
    }

    await UserStore.login(userEmail.value, userPassword.value)
  } catch (err) {
    setError('Erreur lors de la connexion.')
    console.log(err)
  }
}
</script>
