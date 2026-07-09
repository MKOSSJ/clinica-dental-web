<!-- src/views/Integrations/Index.vue -->
<template>
  <div class="p-6 space-y-6">
    <h1 class="text-xl font-bold">Clínica e integraciones</h1>

    <ClinicMap />

    <div class="bg-white border rounded p-4">
      <h2 class="font-semibold mb-3">Enviar confirmación de cita</h2>
      <p class="text-sm text-gray-500 mb-4">
        Selecciona una cita reciente para enviar la confirmación por WhatsApp.
      </p>

      <p v-if="errorMessage" class="text-red-600 mb-2">{{ errorMessage }}</p>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="p-2">Paciente</th>
            <th class="p-2">Fecha</th>
            <th class="p-2">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cita in appointments" :key="cita.id" class="border-b">
            <td class="p-2">{{ cita.patient?.name }}</td>
            <td class="p-2">{{ formatDate(cita.appointment_date) }}</td>
            <td class="p-2">
              <WhatsAppButton
                :patient-name="cita.patient?.name"
                :appointment-date="cita.appointment_date"
                :phone="cita.patient?.phone"
                :appointment-id="cita.id"
              />
            </td>
          </tr>
          <tr v-if="appointments.length === 0">
            <td colspan="3" class="p-4 text-center text-gray-500">
              No hay citas próximas registradas
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ClinicMap from '@/components/ClinicMap.vue'
import WhatsAppButton from '@/components/WhatsAppButton.vue'
import appointmentService from '@/services/appointmentService'

const appointments = ref([])
const errorMessage = ref('')

async function fetchProximasCitas() {
  try {
    const { data } = await appointmentService.list({ status: 'pending' })
    appointments.value = data.data.data.slice(0, 10)
  } catch (err) {
    errorMessage.value = 'Error al cargar las citas próximas.'
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('es-MX', { dateStyle: 'medium', timeStyle: 'short' })
}

onMounted(fetchProximasCitas)
</script>