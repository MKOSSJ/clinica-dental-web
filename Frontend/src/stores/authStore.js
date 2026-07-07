import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
  const token = ref(localStorage.getItem('auth_token') || '')
  const role = ref(localStorage.getItem('auth_role') || '')

  const isAuthenticated = computed(() => Boolean(token.value))

  function setAuth(payload) {
    user.value = payload.user
    token.value = payload.token
    role.value = payload.user?.role || ''

    localStorage.setItem('auth_user', JSON.stringify(payload.user))
    localStorage.setItem('auth_token', payload.token)
    localStorage.setItem('auth_role', payload.user?.role || '')
  }

  function clearAuth() {
    user.value = null
    token.value = ''
    role.value = ''

    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_role')
  }

  async function logout() {
    try {
      if (token.value) {
        await fetch('http://localhost:8000/api/logout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token.value}`,
          },
        })
      }
    } catch (error) {
      console.error(error)
    } finally {
      clearAuth()
      await router.push('/login')
    }
  }

  return {
    user,
    token,
    role,
    isAuthenticated,
    setAuth,
    clearAuth,
    logout,
  }
})
