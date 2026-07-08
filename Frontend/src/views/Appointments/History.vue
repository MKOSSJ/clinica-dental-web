<!-- src/views/Appointments/History.vue -->
<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Historial de citas</h1>

    <p v-if="loading" class="text-gray-500">Cargando...</p>
    <p v-if="errorMessage" class="text-red-600">{{ errorMessage }}</p>

    <table v-if="!loading" class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">Doctor</th>
          <th class="p-2">Fecha</th>
          <th class="p-2">Estado</th>
          <th class="p-2">Notas</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cita in appointments" :key="cita.id" class="border-b">
          <td class="p-2">{{ cita.doctor?.name }}</td>
          <td class="p-2">{{ formatDate(cita.appointment_date) }}</td>
          <td class="p-2">{{ cita.status }}</td>
          <td class="p-2">{{ cita.notes || '-' }}</td>
        </tr>
        <tr v-if="appointments.length === 0">
          <td colspan="4" class="p-4 text-center text-gray-500">Sin historial</td>
        </tr>
      </tbody>
    </table>

    <router-link to="/citas" class="text-blue-600 mt-4 inline-block">
      Volver a citas
    </router-link>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import appointmentService from '@/services/appointmentService'

const route = useRoute()
const appointments = ref([])
const loading = ref(true)
const errorMessage = ref('')

async function fetchHistorial() {
  try {
    const { data } = await appointmentService.historial(route.params.patientId)
    appointments.value = data.data
  } catch (err) {
    errorMessage.value = 'Error al cargar el historial.'
  } finally {
    loading.value = false
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('es-MX', { dateStyle: 'medium', timeStyle: 'short' })
}

onMounted(fetchHistorial)
</script>