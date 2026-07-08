<!-- src/views/ForgotPassword.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <form @submit.prevent="submit" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
      <h1 class="text-xl font-bold mb-6 text-center">Recuperar contraseña</h1>

      <div class="mb-4">
        <label class="block text-sm mb-1">Email</label>
        <input v-model="email" type="email" required class="border rounded w-full px-3 py-2" />
      </div>

      <p v-if="message" :class="success ? 'text-green-600' : 'text-red-600'" class="text-sm mb-3">
        {{ message }}
      </p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded disabled:opacity-50"
      >
        {{ loading ? 'Enviando...' : 'Enviar enlace' }}
      </button>

      <router-link to="/login" class="block text-center text-sm text-blue-600 mt-4">
        Volver a iniciar sesión
      </router-link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import authService from '@/services/authService'

const email = ref('')
const message = ref('')
const success = ref(false)
const loading = ref(false)

async function submit() {
  loading.value = true
  message.value = ''
  try {
    const { data } = await authService.forgotPassword(email.value)
    success.value = true
    message.value = data.message
  } catch (err) {
    success.value = false
    message.value = err.response?.data?.message || 'Error al enviar el enlace.'
  } finally {
    loading.value = false
  }
}
</script>