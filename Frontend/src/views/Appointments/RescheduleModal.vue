<!-- src/views/Appointments/RescheduleModal.vue -->
<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded p-6 w-full max-w-sm">
      <h2 class="text-lg font-bold mb-4">Reagendar cita</h2>
      <p class="text-sm text-gray-600 mb-4">
        Paciente: {{ appointment.patient?.name }} — Doctor: {{ appointment.doctor?.name }}
      </p>

      <div class="mb-3">
        <label class="block text-sm mb-1">Nueva fecha *</label>
        <input
          v-model="fecha"
          @change="fetchHorarios"
          type="date"
          class="border rounded w-full px-3 py-2"
        />
      </div>

      <div class="mb-4" v-if="fecha">
        <label class="block text-sm mb-1">Nueva hora *</label>
        <select v-model="hora" class="border rounded w-full px-3 py-2">
          <option value="">Selecciona una hora</option>
          <option v-for="h in horariosDisponibles" :key="h" :value="h">{{ h }}</option>
        </select>
        <p v-if="horariosDisponibles.length === 0" class="text-sm text-gray-500">
          No hay horarios disponibles ese día.
        </p>
      </div>

      <p v-if="errorMessage" class="text-red-600 text-sm mb-3">{{ errorMessage }}</p>

      <div class="flex justify-end space-x-2">
        <button @click="$emit('close')" class="px-4 py-2 border rounded">Cancelar</button>
        <button @click="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
          Confirmar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import doctorService from '@/services/doctorService'
import appointmentService from '@/services/appointmentService'

const props = defineProps({ appointment: Object })
const emit = defineEmits(['close', 'saved'])

const fecha = ref('')
const hora = ref('')
const horariosDisponibles = ref([])
const errorMessage = ref('')

async function fetchHorarios() {
  hora.value = ''
  const { data } = await doctorService.horarios(props.appointment.doctor_id, fecha.value)
  horariosDisponibles.value = data.data.disponibles
}

async function submit() {
  if (!fecha.value || !hora.value) {
    errorMessage.value = 'Selecciona fecha y hora.'
    return
  }

  try {
    const appointment_date = `${fecha.value} ${hora.value}:00`
    await appointmentService.reagendar(props.appointment.id, appointment_date)
    emit('saved')
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'No se pudo reagendar la cita.'
  }
}
</script>