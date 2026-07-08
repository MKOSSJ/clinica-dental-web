<!-- src/views/Doctors/DoctorSchedule.vue -->
<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded p-6 w-full max-w-lg">
      <h2 class="text-lg font-bold mb-4">
        Horarios de {{ doctor.name }}
      </h2>

      <input
        v-model="fecha"
        @change="fetchHorarios"
        type="date"
        class="border rounded px-3 py-2 w-full mb-4"
      />

      <p v-if="loading" class="text-gray-500">Cargando horarios...</p>
      <p v-if="errorMessage" class="text-red-600">{{ errorMessage }}</p>

      <div v-if="!loading && fecha" class="grid grid-cols-3 gap-2">
        <div
          v-for="slot in allSlots"
          :key="slot"
          :class="[
            'text-center py-2 rounded text-sm',
            disponibles.includes(slot)
              ? 'bg-green-100 text-green-700'
              : 'bg-red-100 text-red-700 line-through',
          ]"
        >
          {{ slot }}
        </div>
      </div>

      <div class="flex justify-end mt-4">
        <button @click="$emit('close')" class="px-4 py-2 border rounded">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import doctorService from '@/services/doctorService'

const props = defineProps({ doctor: Object })
defineEmits(['close'])

const fecha = ref(new Date().toISOString().slice(0, 10))
const disponibles = ref([])
const ocupados = ref([])
const loading = ref(false)
const errorMessage = ref('')

const allSlots = ref([])

async function fetchHorarios() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await doctorService.horarios(props.doctor.id, fecha.value)
    disponibles.value = data.data.disponibles
    ocupados.value = data.data.ocupados
    allSlots.value = [...disponibles.value, ...ocupados.value].sort()
  } catch (err) {
    errorMessage.value = 'Error al cargar los horarios.'
  } finally {
    loading.value = false
  }
}

fetchHorarios()
</script>