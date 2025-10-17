<template>
  <aside class="w-64 bg-white shadow-md h-screen p-4">
    <h2 class="text-xl font-bold mb-6 text-gray-800">Modules</h2>

    <div v-if="moduleStore.loading" class="text-gray-500 text-center py-4">Chargement...</div>

    <div v-else class="space-y-3">
      <div
        v-for="module in moduleStore.modules"
        :key="module.id"
        class="p-4 border rounded-lg hover:bg-gray-50 transition"
      >
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold text-gray-700">{{ module.name }}</h3>
          <button
            @click="toggleModule(module)"
            :disabled="togglingId === module.id"
            :class="[
              'px-3 py-1 rounded-full text-xs font-medium transition disabled:opacity-50',
              module.is_active
                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
            ]"
          >
            {{ togglingId === module.id ? '...' : module.is_active ? 'Actif' : 'Inactif' }}
          </button>
        </div>
        <p class="text-sm text-gray-500">{{ module.description }}</p>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useModuleStore } from '@/stores/ModuleStore'

const moduleStore = useModuleStore()
const togglingId = ref(null)

onMounted(() => {
  moduleStore.fetchModules()
})

const toggleModule = async (module) => {
  togglingId.value = module.id
  try {
    await moduleStore.toggleModule(module.id, module.is_active)
  } catch (error) {
    console.error('Erreur lors du toggle:', error)
  } finally {
    togglingId.value = null
  }
}
</script>
