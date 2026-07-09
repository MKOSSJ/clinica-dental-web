<!-- src/components/ClinicMap.vue -->
<template>
  <div class="bg-white border rounded p-4">
    <h2 class="font-semibold mb-3">Ubicación de la clínica</h2>
    <div class="rounded overflow-hidden">
      <iframe
        :src="mapEmbedUrl"
        width="100%"
        height="300"
        style="border:0"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>
    </div>
    <p class="text-sm text-gray-500 mt-2">{{ address }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const address = 'Av. Reforma 123, Tlaxcala de Xicohténcatl, Tlaxcala, México'

const mapEmbedUrl = computed(() => {
  const query = encodeURIComponent(address)
  return `https://maps.google.com/maps?q=${query}&output=embed`
})
</script>