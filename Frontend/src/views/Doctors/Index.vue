<!-- src/views/Doctors/Index.vue -->
<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-xl font-bold">Doctores</h1>
      <button
        v-if="isAdmin"
        @click="openCreate"
        class="bg-blue-600 text-white px-4 py-2 rounded"
      >
        + Nuevo doctor
      </button>
    </div>

    <input
      v-model="search"
      @input="debouncedSearch"
      type="text"
      placeholder="Buscar por nombre o especialidad..."
      class="border rounded px-3 py-2 w-full mb-4"
    />

    <p v-if="errorMessage" class="text-red-600 mb-2">{{ errorMessage }}</p>

    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">Nombre</th>
          <th class="p-2">Especialidad</th>
          <th class="p-2">Teléfono</th>
          <th class="p-2">Email</th>
          <th class="p-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="doctor in doctors" :key="doctor.id" class="border-b">
          <td class="p-2">{{ doctor.name }}</td>
          <td class="p-2">{{ doctor.specialty || '-' }}</td>
          <td class="p-2">{{ doctor.phone || '-' }}</td>
          <td class="p-2">{{ doctor.email || '-' }}</td>
          <td class="p-2 space-x-2">
            <button @click="openSchedule(doctor)" class="text-green-600">Horarios</button>
            <template v-if="isAdmin">
              <button @click="openEdit(doctor)" class="text-blue-600">Editar</button>
              <button @click="confirmDelete(doctor)" class="text-red-600">Eliminar</button>
            </template>
          </td>
        </tr>
        <tr v-if="doctors.length === 0">
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

    <DoctorForm
      v-if="showForm"
      :doctor="selectedDoctor"
      @close="showForm = false"
      @saved="handleSaved"
    />

    <DoctorSchedule
      v-if="showSchedule"
      :doctor="selectedDoctor"
      @close="showSchedule = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import doctorService from '@/services/doctorService'
import DoctorForm from './Form.vue'
import DoctorSchedule from './DoctorSchedule.vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()
const isAdmin = computed(() => authStore.user?.role === 'admin')

const doctors = ref([])
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const errorMessage = ref('')
const showForm = ref(false)
const showSchedule = ref(false)
const selectedDoctor = ref(null)

let debounceTimer = null
function debouncedSearch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchDoctors()
  }, 400)
}

async function fetchDoctors() {
  try {
    const { data } = await doctorService.list({
      search: search.value,
      page: currentPage.value,
    })
    doctors.value = data.data.data
    lastPage.value = data.data.last_page
  } catch (err) {
    errorMessage.value = 'Error al cargar doctores.'
  }
}

function goToPage(page) {
  currentPage.value = page
  fetchDoctors()
}

function openCreate() {
  selectedDoctor.value = null
  showForm.value = true
}

function openEdit(doctor) {
  selectedDoctor.value = doctor
  showForm.value = true
}

function openSchedule(doctor) {
  selectedDoctor.value = doctor
  showSchedule.value = true
}

async function confirmDelete(doctor) {
  if (!confirm(`¿Eliminar al Dr. ${doctor.name}?`)) return
  try {
    await doctorService.remove(doctor.id)
    fetchDoctors()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'No se pudo eliminar el doctor.'
  }
}

function handleSaved() {
  showForm.value = false
  fetchDoctors()
}

onMounted(fetchDoctors)
</script>