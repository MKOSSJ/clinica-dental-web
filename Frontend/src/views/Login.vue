<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const errorMessage = ref('')
const loading = ref(false)

async function onSubmit() {
  errorMessage.value = ''

  if (!form.email || !form.password) {
    errorMessage.value = 'Completa todos los campos.'
    return
  }

  loading.value = true

  try {
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form),
    })

    const result = await response.json()

    if (!response.ok) {
      errorMessage.value = result.message || 'No se pudo iniciar sesión.'
      return
    }

    authStore.setAuth(result.data)
    await router.push('/')
  } catch (error) {
    errorMessage.value = 'No se pudo conectar con el servidor.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <form class="auth-card" @submit.prevent="onSubmit">
      <h2>Iniciar sesión</h2>
      <p class="subtitle">Accede al sistema de gestión de citas</p>

      <label for="email">Correo</label>
      <input id="email" v-model="form.email" type="email" placeholder="tu@correo.com" />

      <label for="password">Contraseña</label>
      <input id="password" v-model="form.password" type="password" placeholder="********" />

      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Ingresando...' : 'Entrar' }}
      </button>

      <router-link to="/forgot-password">¿Olvidaste tu contraseña?</router-link>
    </form>
  </div>
</template>

<style scoped>
.auth-page { min-height: 100vh; display: grid; place-items: center; background: #f5f7fb; }
.auth-card { display: flex; flex-direction: column; gap: 0.75rem; width: min(100%, 400px); padding: 2rem; border-radius: 12px; background: white; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
input, button { padding: 0.75rem; border-radius: 8px; border: 1px solid #d9e2ec; }
button { background: #2563eb; color: white; cursor: pointer; }
button:disabled { opacity: 0.7; cursor: wait; }
.error { color: #b91c1c; font-size: 0.95rem; }
.subtitle { margin: 0; color: #64748b; }
</style>
