<!-- src/views/Reports/Index.vue -->
<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Reportes</h1>

    <div class="flex gap-3 mb-6">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value"
        :class="[
          'px-4 py-2 rounded border',
          activeTab === tab.value ? 'bg-blue-600 text-white' : 'bg-white',
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Citas por día -->
    <div v-if="activeTab === 'dia'">
      <div class="flex gap-3 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">Fecha</label>
          <input v-model="filtrosDia.fecha" type="date" class="border rounded px-3 py-2" />
        </div>
        <button @click="fetchCitasPorDia" class="bg-blue-600 text-white px-4 py-2 rounded">
          Generar
        </button>
      </div>

      <div v-if="resultadoDia">
        <p class="mb-2 font-semibold">Total de citas: {{ resultadoDia.total }}</p>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="p-2">Estado</th>
              <th class="p-2">Cantidad</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(cantidad, estado) in resultadoDia.porEstado" :key="estado" class="border-b">
              <td class="p-2">{{ estado }}</td>
              <td class="p-2">{{ cantidad }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Citas por doctor -->
    <div v-if="activeTab === 'doctor'">
      <div class="flex gap-3 mb-4 items-end flex-wrap">
        <div>
          <label class="block text-sm mb-1">Doctor</label>
          <select v-model="filtrosDoctor.doctor_id" class="border rounded px-3 py-2">
            <option value="">Selecciona un doctor</option>
            <option v-for="d in doctors" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Desde</label>
          <input v-model="filtrosDoctor.desde" type="date" class="border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm mb-1">Hasta</label>
          <input v-model="filtrosDoctor.hasta" type="date" class="border rounded px-3 py-2" />
        </div>
        <button @click="fetchCitasPorDoctor" class="bg-blue-600 text-white px-4 py-2 rounded">
          Generar
        </button>
      </div>

      <div v-if="resultadoDoctor">
        <div class="flex justify-between items-center mb-2">
          <p class="font-semibold">Total de citas: {{ resultadoDoctor.total }}</p>
          <button @click="exportarCSV(resultadoDoctor.citas, 'citas_por_doctor')" class="text-blue-600 text-sm">
            Exportar CSV
          </button>
        </div>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="p-2">Paciente</th>
              <th class="p-2">Fecha</th>
              <th class="p-2">Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cita in resultadoDoctor.citas" :key="cita.id" class="border-b">
              <td class="p-2">{{ cita.patient?.name }}</td>
              <td class="p-2">{{ formatDate(cita.appointment_date) }}</td>
              <td class="p-2">{{ cita.status }}</td>
            </tr>
            <tr v-if="resultadoDoctor.citas.length === 0">
              <td colspan="3" class="p-4 text-center text-gray-500">Sin resultados</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pacientes registrados -->
    <div v-if="activeTab === 'pacientes'">
      <div class="flex gap-3 mb-4 items-end">
        <div>
          <label class="block text-sm mb-1">Desde</label>
          <input v-model="filtrosPacientes.desde" type="date" class="border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm mb-1">Hasta</label>
          <input v-model="filtrosPacientes.hasta" type="date" class="border rounded px-3 py-2" />
        </div>
        <button @click="fetchPacientesRegistrados" class="bg-blue-600 text-white px-4 py-2 rounded">
          Generar
        </button>
      </div>

      <div v-if="resultadoPacientes">
        <div class="flex justify-between items-center mb-2">
          <p class="font-semibold">Total registrados: {{ resultadoPacientes.total }}</p>
          <button
            @click="exportarCSV(resultadoPacientes.pacientes, 'pacientes_registrados')"
            class="text-blue-600 text-sm"
          >
            Exportar CSV
          </button>
        </div>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="p-2">Nombre</th>
              <th class="p-2">Email</th>
              <th class="p-2">Registrado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in resultadoPacientes.pacientes" :key="p.id" class="border-b">
              <td class="p-2">{{ p.name }}</td>
              <td class="p-2">{{ p.email || '-' }}</td>
              <td class="p-2">{{ formatDate(p.created_at) }}</td>
            </tr>
            <tr v-if="resultadoPacientes.pacientes.length === 0">
              <td colspan="3" class="p-4 text-center text-gray-500">Sin resultados</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <p v-if="errorMessage" class="text-red-600 mt-4">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import reportService from '@/services/reportService'
import doctorService from '@/services/doctorService'

const tabs = [
  { value: 'dia', label: 'Citas por día' },
  { value: 'doctor', label: 'Citas por doctor' },
  { value: 'pacientes', label: 'Pacientes registrados' },
]

const activeTab = ref('dia')
const errorMessage = ref('')
const doctors = ref([])

const filtrosDia = ref({ fecha: new Date().toISOString().slice(0, 10) })
const resultadoDia = ref(null)

const filtrosDoctor = ref({ doctor_id: '', desde: '', hasta: '' })
const resultadoDoctor = ref(null)

const filtrosPacientes = ref({ desde: '', hasta: '' })
const resultadoPacientes = ref(null)

async function loadDoctors() {
  const { data } = await doctorService.list({})
  doctors.value = data.data.data
}

async function fetchCitasPorDia() {
  errorMessage.value = ''
  try {
    const { data } = await reportService.citasPorDia(filtrosDia.value.fecha)
    resultadoDia.value = data.data
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Error al generar el reporte.'
  }
}

async function fetchCitasPorDoctor() {
  errorMessage.value = ''
  if (!filtrosDoctor.value.doctor_id || !filtrosDoctor.value.desde || !filtrosDoctor.value.hasta) {
    errorMessage.value = 'Completa doctor, fecha desde y fecha hasta.'
    return
  }
  try {
    const { data } = await reportService.citasPorDoctor(filtrosDoctor.value)
    resultadoDoctor.value = data.data
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Error al generar el reporte.'
  }
}

async function fetchPacientesRegistrados() {
  errorMessage.value = ''
  if (!filtrosPacientes.value.desde || !filtrosPacientes.value.hasta) {
    errorMessage.value = 'Completa fecha desde y fecha hasta.'
    return
  }
  try {
    const { data } = await reportService.pacientesRegistrados(filtrosPacientes.value)
    resultadoPacientes.value = data.data
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Error al generar el reporte.'
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('es-MX', { dateStyle: 'medium', timeStyle: 'short' })
}

function exportarCSV(rows, filename) {
  if (!rows || rows.length === 0) return

  const headers = Object.keys(rows[0]).filter(
    (key) => typeof rows[0][key] !== 'object'
  )
  const csvRows = [
    headers.join(','),
    ...rows.map((row) => headers.map((h) => `"${row[h] ?? ''}"`).join(',')),
  ]

  const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `${filename}.csv`
  link.click()
  URL.revokeObjectURL(url)
}

onMounted(loadDoctors)
</script>