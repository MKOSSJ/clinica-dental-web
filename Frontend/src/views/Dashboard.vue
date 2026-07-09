<!-- src/views/Dashboard.vue -->
<template>
  <div class="dashboard p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-xl font-bold">Panel</h1>
        <p class="text-gray-600">Bienvenido, {{ authStore.user?.name || 'usuario' }}</p>
      </div>
      <button @click="authStore.logout" class="logout-btn">Cerrar sesión</button>
    </div>

    <p v-if="errorMessage" class="text-red-600 mb-4">{{ errorMessage }}</p>

    <div v-if="resumen" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white border rounded p-4">
        <p class="text-sm text-gray-500">Pacientes</p>
        <p class="text-2xl font-bold">{{ resumen.totalPacientes }}</p>
      </div>
      <div class="bg-white border rounded p-4">
        <p class="text-sm text-gray-500">Doctores</p>
        <p class="text-2xl font-bold">{{ resumen.totalDoctores }}</p>
      </div>
      <div class="bg-white border rounded p-4">
        <p class="text-sm text-gray-500">Citas hoy</p>
        <p class="text-2xl font-bold">{{ resumen.citasHoy }}</p>
      </div>
      <div class="bg-white border rounded p-4">
        <p class="text-sm text-gray-500">Citas pendientes</p>
        <p class="text-2xl font-bold">{{ resumen.citasPendientes }}</p>
      </div>
    </div>

    <div v-if="resumen" class="bg-white border rounded p-4">
      <h2 class="font-semibold mb-3">Citas de los últimos 7 días</h2>
      <canvas ref="chartCanvas" height="100"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import dashboardService from '@/services/dashboardService'
import Chart from 'chart.js/auto'

const authStore = useAuthStore()
const resumen = ref(null)
const errorMessage = ref('')
const chartCanvas = ref(null)
let chartInstance = null

async function fetchResumen() {
  try {
    const { data } = await dashboardService.resumen()
    resumen.value = data.data
    await nextTick()
    renderChart()
  } catch (err) {
    errorMessage.value = 'Error al cargar el resumen del dashboard.'
  }
}

function renderChart() {
  if (!chartCanvas.value || !resumen.value) return

  if (chartInstance) {
    chartInstance.destroy()
  }

  const labels = resumen.value.citasUltimos7Dias.map((d) => d.fecha)
  const data = resumen.value.citasUltimos7Dias.map((d) => d.total)

  chartInstance = new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Citas',
          data,
          backgroundColor: '#2563eb',
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } },
      },
      plugins: {
        legend: { display: false },
      },
    },
  })
}

onMounted(fetchResumen)
</script>

<style scoped>
.dashboard {
  padding: 2rem;
}
.logout-btn {
  margin-top: 0;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  border: none;
  background: #dc2626;
  color: white;
  cursor: pointer;
}
</style>