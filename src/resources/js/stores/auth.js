import { defineStore } from 'pinia'
import authApi from '../api/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    ready: false,
    _fetchPromise: null,
  }),

  getters: {
    isAuthenticated: (state) => state.user !== null,
  },

  actions: {
    fetchUser() {
      if (this._fetchPromise) {
        return this._fetchPromise
      }

      this._fetchPromise = (async () => {
        try {
          this.user = await authApi.me()
        } catch {
          this.user = null
        } finally {
          this.ready = true
          this._fetchPromise = null
        }
      })()

      return this._fetchPromise
    },

    async login(payload) {
      this.user = await authApi.login(payload)
    },

    async register(payload) {
      this.user = await authApi.register(payload)
    },

    async logout() {
      await authApi.logout()
      this.user = null
    },
  },
})
