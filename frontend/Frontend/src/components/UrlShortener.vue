<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">URL Shortener</h2>

    <form @submit.prevent="shortenUrl" class="mb-8">
      <div class="flex gap-3">
        <input
          v-model="longUrl"
          type="url"
          placeholder="Entrez l'URL à raccourcir"
          required
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
        />
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Chargement...' : 'Raccourcir' }}
        </button>
      </div>
      <div v-if="error" class="mt-3 text-red-600 text-sm">{{ error }}</div>
      <div v-if="success" class="mt-3 text-green-600 text-sm">{{ success }}</div>
    </form>

    <div>
      <h3 class="text-xl font-semibold text-gray-700 mb-4">Mes liens</h3>

      <div v-if="loadingLinks" class="text-gray-500 text-center py-8">Chargement des liens...</div>

      <div v-else-if="links.length === 0" class="text-gray-500 text-center py-8">
        Aucun lien raccourci
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="link in links"
          :key="link.id"
          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
        >
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-sm font-medium text-sky-600">{{ link.short_url }}</span>
                <button
                  @click="copyToClipboard(link.short_url)"
                  class="text-xs text-gray-500 hover:text-sky-600"
                >
                  📋 Copier
                </button>
              </div>
              <div class="text-sm text-gray-600 truncate">{{ link.original_url }}</div>
            </div>
            <button
              @click="deleteLink(link.id)"
              :disabled="deletingId === link.id"
              class="ml-4 px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded disabled:opacity-50"
            >
              {{ deletingId === link.id ? '...' : 'Supprimer' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const longUrl = ref('')
const links = ref([])
const loading = ref(false)
const loadingLinks = ref(false)
const deletingId = ref(null)
const error = ref(null)
const success = ref(null)

const fetchLinks = async () => {
  loadingLinks.value = true
  try {
    const response = await api.get('/links')
    links.value = response.data || []
  } catch (err) {
    console.error('Erreur lors de la récupération des liens:', err)
  } finally {
    loadingLinks.value = false
  }
}

const shortenUrl = async () => {
  loading.value = true
  error.value = null
  success.value = null

  try {
    const response = await api.post('/shorten', {
      url: longUrl.value,
    })

    success.value = 'URL raccourcie avec succès !'
    longUrl.value = ''
    await fetchLinks()

    setTimeout(() => {
      success.value = null
    }, 3000)
  } catch (err) {
    error.value = err.response?.data?.message || "Erreur lors du raccourcissement de l'URL"
    setTimeout(() => {
      error.value = null
    }, 5000)
  } finally {
    loading.value = false
  }
}

const deleteLink = async (id) => {
  deletingId.value = id
  try {
    await api.delete(`/links/${id}`)
    await fetchLinks()
  } catch (err) {
    console.error('Erreur lors de la suppression:', err)
  } finally {
    deletingId.value = null
  }
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
  success.value = 'Lien copié !'
  setTimeout(() => {
    success.value = null
  }, 2000)
}

onMounted(() => {
  fetchLinks()
})
</script>
