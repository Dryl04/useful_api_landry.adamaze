<template>
  <div class="flex h-screen bg-gray-100">
    <SideBar />

    <main class="flex-1 p-8 overflow-y-auto">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-700 mb-4">Modules Actifs</h2>
          <div v-if="moduleStore.activeModules.length === 0" class="text-gray-500">
            Aucun module activé
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="module in moduleStore.activeModules"
              :key="module.id"
              class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition"
            >
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-800">{{ module.name }}</h3>
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
              </div>
              <p class="text-gray-600 text-sm">{{ module.description }}</p>
            </div>
          </div>
        </div>

        <div v-if="urlShortenerModule && urlShortenerModule.is_active" class="mt-8">
          <UrlShortener />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import SideBar from '@/components/SideBar.vue'
import UrlShortener from '@/components/UrlShortener.vue'
import { useModuleStore } from '@/stores/ModuleStore'

const moduleStore = useModuleStore()

const urlShortenerModule = computed(() => moduleStore.getModuleByName('URL Shortener'))

onMounted(() => {
  if (moduleStore.modules.length === 0) {
    moduleStore.fetchModules()
  }
})
</script>
