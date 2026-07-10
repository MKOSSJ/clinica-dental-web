<!-- src/views/Appointments/Index.vue -->
<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-xl font-bold">Citas</h1>
      <button @click="openCreate" class="bg-blue-600 text-white px-4 py-2 rounded">
        + Nueva cita
      </button>
    </div>

    <div class="flex flex-wrap gap-3 mb-4">
      <select v-model="filters.status" @change="fetchAppointments" class="border rounded px-3 py-2">
        <option value="">Todos los estados</option>
        <option value="pending">Pendiente</option>
        <option value="confirmed">Confirmada</option>
        <option value="cancelled">Cancelada</option>
        <option value="completed">Completada</option>
      </select>

      <input
        v-model="filters.desde"
        @change="fetchAppointments"
        type="date"
        class="border rounded px-3 py-2"
      />
      <input
        v-model="filters.hasta"
        @change="fetchAppointments"
        type="date"
        class="border rounded px-3 py-2"
      />
    </div>

    <p v-if="errorMessage" class="text-red-600 mb-2">{{ errorMessage }}</p>

    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">Paciente</th>
          <th class="p-2">Doctor</th>
          <th class="p-2">Fecha</th>
          <th class="p-2">Estado</th>
          <th class="p-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cita in appointments" :key="cita.id" class="border-b">
          <td class="p-2">{{ cita.patient?.name }}</td>
          <td class="p-2">{{ cita.doctor?.name }}</td>
          <td class="p-2">{{ formatDate(cita.appointment_date) }}</td>
          <td class="p-2">
            <select
              v-if="cita.status !== 'cancelled'"
              :value="cita.status"
              @change="cambiarEstado(cita, $event.target.value)"
              :class="statusClass(cita.status)"
              class="px-2 py-1 rounded text-xs border-0 cursor-pointer"
            >
              <option value="pending">Pendiente</option>
              <option value="confirmed">Confirmada</option>
              <option value="completed">Completada</option>
            </select>
            <span v-else :class="statusClass(cita.status)" class="px-2 py-1 rounded text-xs">
              Cancelada
            </span>
          </td>
          <td class="p-2 space-x-2">
            <button
              v-if="cita.status !== 'cancelled'"
              @click="openReschedule(cita)"
              class="text-blue-600"
            >
              Reagendar
            </button>
            <button
              v-if="cita.status !== 'cancelled'"
              @click="confirmCancel(cita)"
              class="text-red-600"
            >
              Cancelar
            </button>
            <button @click="viewHistory(cita.patient_id)" class="text-gray-600">
              Historial
            </button>
          </td>
        </tr>
        <tr v-if="appointments.length === 0">
          <td colspan="5" class="p-4 text-center text-gray-500">Sin resultados</td>
        </tr>
      </tbody>
    </table>

    <div class="flex justify-center mt-4 space-x-2" v-if="lastPage > 1">
      <button
        v-for="page in lastPage"
        :key="page"
        @click="goToPage(page)"
        :class="['px-3 py-1 border rounded', page === currentPage ? 'bg-blue-600 text-white' : '']"
      >
        {{ page }}
      </button>
    </div>

    <AppointmentForm
      v-if="showForm"
      @close="showForm = false"
      @saved="handleSaved"
    />

    <RescheduleModal
      v-if="showReschedule"
      :appointment="selectedAppointment"
      @close="showReschedule = false"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import appointmentService from '@/services/appointmentService'
import AppointmentForm from './Form.vue'
import RescheduleModal from './RescheduleModal.vue'

const router = useRouter()

const appointments = ref([])
const currentPage = ref(1)
const lastPage = ref(1)
const errorMessage = ref('')
const showForm = ref(false)
const showReschedule = ref(false)
const selectedAppointment = ref(null)

const filters = ref({
  status: '',
  desde: '',
  hasta: '',
})

async function fetchAppointments() {
  try {
    const { data } = await appointmentService.list({
      ...filters.value,
      page: currentPage.value,
    })
    appointments.value = data.data.data
    lastPage.value = data.data.last_page
  } catch (err) {
    errorMessage.value = 'Error al cargar las citas.'
  }
}

function goToPage(page) {
  currentPage.value = page
  fetchAppointments()
}

function openCreate() {
  showForm.value = true
}

function openReschedule(cita) {
  selectedAppointment.value = cita
  showReschedule.value = true
}

async function confirmCancel(cita) {
  if (!confirm(`¿Cancelar la cita de ${cita.patient?.name}?`)) return
  try {
    await appointmentService.cancelar(cita.id)
    fetchAppointments()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'No se pudo cancelar la cita.'
  }
}

async function cambiarEstado(cita, nuevoStatus) {
  try {
    await appointmentService.actualizarEstado(cita.id, nuevoStatus)
    fetchAppointments()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'No se pudo actualizar el estado.'
  }
}

function viewHistory(patientId) {
  router.push({ name: 'appointments-history', params: { patientId } })
}

function handleSaved() {
  showForm.value = false
  showReschedule.value = false
  fetchAppointments()
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

function statusLabel(status) {
  const labels = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    cancelled: 'Cancelada',
    completed: 'Completada',
  }
  return labels[status] || status
}

function statusClass(status) {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-red-100 text-red-700',
    completed: 'bg-green-100 text-green-700',
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

onMounted(fetchAppointments)
</script>