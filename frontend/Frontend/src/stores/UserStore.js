import { defineStore } from 'pinia'
import api from '@/services/api'
import router from '@/router'

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [],
    currentUser: null,
    isAuthenticated: false,

    loading: false,
    error: null,
  }),

  persist: {
    paths: ['currentUser', 'isAuthenticated'],
  },

  getters: {
    getAllUsers: (state) => {
      return state.users
    },

    getCurrentUserName: (state) => {
      return state.currentUser ? `${state.currentUser.name}` : 'utilisateur non authentifié'
    },

    getCurrentUserId: (state) => {
      return state.currentUser ? `${state.currentUser.id}` : null
    },

    getCurrentUserEmail: (state) => {
      return state.currentUser ? `${state.currentUser.email}` : null
    },
  },

  actions: {
    initializeAuth() {
      const token = localStorage.getItem('authToken')

      if (token && this.isAuthenticated) {
        return true
      }
      return false
    },

    saveUserData(user) {
      this.currentUser = {
        id: user.id || user.user_id,
        name: user.name || '',
        email: user.email || '',
      }
      this.isAuthenticated = true
    },

    logout() {
      localStorage.removeItem('authToken')
      this.currentUser = null
      this.isAuthenticated = false
      this.users = []
      router.push('/login')
    },

    async login(email, password) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.post('/login', {
          email: email,
          password: password,
        })
        if (response.data?.token) {
          localStorage.setItem('authToken', response.data.token)

          this.saveUserData({
            id: response.data.user_id,
            email: email,
            name: '',
          })

          router.push('/')
        } else {
          throw new Error('Une erreur est survenue')
        }
      } catch (error) {
        console.error('Erreur de connexion :', error)

        if (error.response) {
          const status = error.response.status
          if (status === 401) {
            this.error = 'Email ou mot de passe incorrect.'
          } else if (status === 404) {
            this.error = 'Compte introuvable. Veuillez vous inscrire.'
          } else {
            this.error = error.response.data?.message || 'Erreur serveur.'
          }
        } else {
          this.error = 'Impossible de se connecter au serveur.'
        }
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(name, email, password) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.post('/register', {
          name: name,
          email: email,
          password: password,
        })
        if (response.status === 201 && response.data?.id) {
          console.log('Inscription réussie')
          router.push('/login')
        } else {
          throw new Error('Réponse du serveur invalide.')
        }
      } catch (error) {
        console.error("Erreur d'inscription :", error)
        this.error = "Impossible de s'inscrire. Veuillez réessayer "
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
