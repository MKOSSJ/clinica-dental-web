<!-- src/views/Login.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <form @submit.prevent="submit" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
      <h1 class="text-xl font-bold mb-6 text-center">Iniciar sesión</h1>

      <div class="mb-3">
        <label class="block text-sm mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          class="border rounded w-full px-3 py-2"
        />
        <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-sm mb-1">Contraseña</label>
        <input
          v-model="form.password"
          type="password"
          required
          class="border rounded w-full px-3 py-2"
        />
        <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password[0] }}</p>
      </div>

      <p v-if="generalError" class="text-red-600 text-sm mb-3">{{ generalError }}</p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded disabled:opacity-50"
      >
        {{ loading ? 'Ingresando...' : 'Ingresar' }}
      </button>

      <router-link to="/forgot-password" class="block text-center text-sm text-blue-600 mt-4">
        ¿Olvidaste tu contraseña?
      </router-link>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import authService from '@/services/authService'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({ email: '', password: '' })
const errors = ref({})
const generalError = ref('')
const loading = ref(false)

async function submit() {
  errors.value = {}
  generalError.value = ''
  loading.value = true

  try {
    const { data } = await authService.login(form)
    authStore.setAuth(data.data) // { user, token }
    router.push('/dashboard')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      generalError.value = err.response?.data?.message || 'Error al iniciar sesión.'
    }
  } finally {
    loading.value = false
  }
}
</script>