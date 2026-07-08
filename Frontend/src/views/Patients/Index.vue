<!-- src/views/Patients/Index.vue -->
<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-xl font-bold">Pacientes</h1>
      <button @click="openCreate" class="bg-blue-600 text-white px-4 py-2 rounded">
        + Nuevo paciente
      </button>
    </div>

    <input
      v-model="search"
      @input="debouncedSearch"
      type="text"
      placeholder="Buscar por nombre o email..."
      class="border rounded px-3 py-2 w-full mb-4"
    />

    <p v-if="errorMessage" class="text-red-600 mb-2">{{ errorMessage }}</p>

    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">Nombre</th>
          <th class="p-2">Teléfono</th>
          <th class="p-2">Email</th>
          <th class="p-2">Nacimiento</th>
          <th class="p-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="patient in patients" :key="patient.id" class="border-b">
          <td class="p-2">{{ patient.name }}</td>
          <td class="p-2">{{ patient.phone || '-' }}</td>
          <td class="p-2">{{ patient.email || '-' }}</td>
          <td class="p-2">{{ patient.birth_date || '-' }}</td>
          <td class="p-2 space-x-2">
            <button @click="openEdit(patient)" class="text-blue-600">Editar</button>
            <button @click="confirmDelete(patient)" class="text-red-600">Eliminar</button>
          </td>
        </tr>
        <tr v-if="patients.length === 0">
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

    <PatientForm
      v-if="showForm"
      :patient="selectedPatient"
      @close="showForm = false"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import patientService from '@/services/patientService'
import PatientForm from './Form.vue'

const patients = ref([])
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const errorMessage = ref('')
const showForm = ref(false)
const selectedPatient = ref(null)

let debounceTimer = null
function debouncedSearch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchPatients()
  }, 400)
}

async function fetchPatients() {
  try {
    const { data } = await patientService.list({
      search: search.value,
      page: currentPage.value,
    })
    patients.value = data.data.data // paginate() anida en "data"
    lastPage.value = data.data.last_page
  } catch (err) {
    errorMessage.value = 'Error al cargar pacientes.'
  }
}

function goToPage(page) {
  currentPage.value = page
  fetchPatients()
}

function openCreate() {
  selectedPatient.value = null
  showForm.value = true
}

function openEdit(patient) {
  selectedPatient.value = patient
  showForm.value = true
}

async function confirmDelete(patient) {
  if (!confirm(`¿Eliminar a ${patient.name}?`)) return
  try {
    await patientService.remove(patient.id)
    fetchPatients()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'No se pudo eliminar el paciente.'
  }
}

function handleSaved() {
  showForm.value = false
  fetchPatients()
}

onMounted(fetchPatients)
</script>