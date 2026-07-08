<template>
  <div class="unauthorized">
    <h1>No autorizado</h1>
    <p>No tienes permisos para ver esta página.</p>
    <router-link to="/">Volver al inicio</router-link>
  </div>
</template>

<style scoped>
.unauthorized { min-height: 100vh; display: grid; place-items: center; text-align: center; }
</style>
