<script setup>
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()
</script>

<template>
  <div class="dashboard">
    <h1>Panel</h1>
    <p>Bienvenido, {{ authStore.user?.name || 'usuario' }}</p>
    <button @click="authStore.logout">Cerrar sesión</button>
  </div>
</template>

<style scoped>
.dashboard { padding: 2rem; }
button { margin-top: 1rem; padding: 0.75rem 1rem; border-radius: 8px; border: none; background: #dc2626; color: white; cursor: pointer; }
</style>
