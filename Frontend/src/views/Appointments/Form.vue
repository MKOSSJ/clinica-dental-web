<!-- src/views/Appointments/Form.vue -->
<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">Nueva cita</h2>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block text-sm mb-1">Paciente *</label>
          <select v-model="form.patient_id" class="border rounded w-full px-3 py-2">
            <option value="">Selecciona un paciente</option>
            <option v-for="p in patients" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <p v-if="errors.patient_id" class="text-red-600 text-sm">{{ errors.patient_id[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Doctor *</label>
          <select v-model="form.doctor_id" @change="onDoctorChange" class="border rounded w-full px-3 py-2">
            <option value="">Selecciona un doctor</option>
            <option v-for="d in doctors" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
          <p v-if="errors.doctor_id" class="text-red-600 text-sm">{{ errors.doctor_id[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Fecha *</label>
          <input
            v-model="fecha"
            @change="onDateChange"
            type="date"
            class="border rounded w-full px-3 py-2"
          />
        </div>

        <div class="mb-3" v-if="form.doctor_id && fecha">
          <label class="block text-sm mb-1">Hora disponible *</label>
          <select v-model="horaSeleccionada" class="border rounded w-full px-3 py-2">
            <option value="">Selecciona una hora</option>
            <option v-for="h in horariosDisponibles" :key="h" :value="h">{{ h }}</option>
          </select>
          <p v-if="horariosDisponibles.length === 0" class="text-sm text-gray-500">
            No hay horarios disponibles ese día.
          </p>
          <p v-if="errors.appointment_date" class="text-red-600 text-sm">
            {{ errors.appointment_date[0] }}
          </p>
        </div>

        <div class="mb-4">
          <label class="block text-sm mb-1">Notas</label>
          <textarea v-model="form.notes" class="border rounded w-full px-3 py-2" rows="2"></textarea>
        </div>

        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 border rounded">
            Cancelar
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import patientService from '@/services/patientService'
import doctorService from '@/services/doctorService'
import appointmentService from '@/services/appointmentService'

const emit = defineEmits(['close', 'saved'])

const patients = ref([])
const doctors = ref([])
const fecha = ref('')
const horariosDisponibles = ref([])
const horaSeleccionada = ref('')
const errors = ref({})

const form = reactive({
  patient_id: '',
  doctor_id: '',
  notes: '',
})

async function loadOptions() {
  const [{ data: patientsData }, { data: doctorsData }] = await Promise.all([
    patientService.list({}),
    doctorService.list({}),
  ])
  patients.value = patientsData.data.data
  doctors.value = doctorsData.data.data
}

async function onDoctorChange() {
  horariosDisponibles.value = []
  horaSeleccionada.value = ''
  if (form.doctor_id && fecha.value) fetchHorarios()
}

async function onDateChange() {
  horariosDisponibles.value = []
  horaSeleccionada.value = ''
  if (form.doctor_id && fecha.value) fetchHorarios()
}

async function fetchHorarios() {
  const { data } = await doctorService.horarios(form.doctor_id, fecha.value)
  horariosDisponibles.value = data.data.disponibles
}

async function submit() {
  errors.value = {}

  if (!fecha.value || !horaSeleccionada.value) {
    errors.value.appointment_date = ['Selecciona una fecha y hora válidas.']
    return
  }

  const appointment_date = `${fecha.value} ${horaSeleccionada.value}:00`

  try {
    await appointmentService.create({
      ...form,
      appointment_date,
    })
    emit('saved')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      alert(err.response?.data?.message || 'Error al registrar la cita.')
    }
  }
}

onMounted(loadOptions)
</script>