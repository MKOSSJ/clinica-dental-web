<script setup>
import { reactive, ref } from 'vue'

const form = reactive({ email: '' })
const message = ref('')
const loading = ref(false)

async function onSubmit() {
  if (!form.email) {
    message.value = 'Ingresa tu correo para continuar.'
    return
  }

  loading.value = true
  message.value = ''

  try {
    const response = await fetch('http://localhost:8000/api/forgot-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form),
    })

    const result = await response.json()
    message.value = result.message || 'Revisa tu correo.'
  } catch (error) {
    message.value = 'No se pudo enviar la solicitud.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <form class="auth-card" @submit.prevent="onSubmit">
      <h2>Recuperar contraseña</h2>
      <p class="subtitle">Te enviaremos un enlace si tu correo está registrado.</p>

      <label for="email">Correo</label>
      <input id="email" v-model="form.email" type="email" placeholder="tu@correo.com" />

      <p v-if="message" class="message">{{ message }}</p>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Enviando...' : 'Enviar enlace' }}
      </button>

      <router-link to="/login">Volver al login</router-link>
    </form>
  </div>
</template>

<style scoped>
.auth-page { min-height: 100vh; display: grid; place-items: center; background: #f5f7fb; }
.auth-card { display: flex; flex-direction: column; gap: 0.75rem; width: min(100%, 400px); padding: 2rem; border-radius: 12px; background: white; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
input, button { padding: 0.75rem; border-radius: 8px; border: 1px solid #d9e2ec; }
button { background: #2563eb; color: white; cursor: pointer; }
button:disabled { opacity: 0.7; cursor: wait; }
.message { color: #0f766e; }
.subtitle { margin: 0; color: #64748b; }
</style>
