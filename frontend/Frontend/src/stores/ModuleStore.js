import { defineStore } from 'pinia'
import api from '@/services/api'

export const useModuleStore = defineStore('modules', {
  state: () => ({
    modules: [],
    loading: false,
    error: null,
  }),

  persist: {
    paths: ['modules'],
  },

  getters: {
    activeModules: (state) => state.modules.filter((m) => m.is_active),
    getModuleByName: (state) => (name) => state.modules.find((m) => m.name === name),
  },

  actions: {
    async fetchModules() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/modules')
        const apiModules = response.data || []

        // Conserver l'état is_active des modules existants
        const existingModulesMap = new Map(this.modules.map((m) => [m.id, m.is_active]))

        this.modules = apiModules.map((module) => ({
          ...module,
          is_active: existingModulesMap.get(module.id) || false,
        }))
      } catch (error) {
        console.error('Erreur lors de la récupération des modules:', error)
        this.error = 'Impossible de charger les modules'
      } finally {
        this.loading = false
      }
    },

    async toggleModule(moduleId, isActive) {
      try {
        const endpoint = isActive
          ? `/modules/${moduleId}/deactivate`
          : `/modules/${moduleId}/activate`
        const response = await api.post(endpoint)

        const moduleIndex = this.modules.findIndex((m) => m.id === moduleId)
        if (moduleIndex !== -1) {
          this.modules[moduleIndex].is_active = !isActive
        }

        return response.data
      } catch (error) {
        console.error('Erreur lors du toggle du module:', error)
        throw error
      }
    },
  },
})
