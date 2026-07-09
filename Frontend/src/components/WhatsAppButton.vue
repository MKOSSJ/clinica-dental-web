<!-- src/components/WhatsAppButton.vue -->
<template>
  <button
    @click="enviarConfirmacion"
    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
  >
    <MessageCircle class="w-4 h-4" />
    Confirmar por WhatsApp
  </button>
</template>

<script setup>
import { MessageCircle } from 'lucide-vue-next'
import appointmentService from '@/services/appointmentService'

const props = defineProps({
  patientName: { type: String, required: true },
  appointmentDate: { type: String, required: true },
  phone: { type: String, default: '' }, // número del paciente, formato con código de país, ej. 521XXXXXXXXXX
  appointmentId: { type: Number, default: null },
})

const CLINIC_PHONE_FALLBACK = '2481037204' // número de la clínica, usado si no se pasa `phone` del paciente

function buildMessage() {
  const fecha = new Date(props.appointmentDate).toLocaleString('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
  return `Hola ${props.patientName}, te confirmamos tu cita en Clínica Dental para el ${fecha}. ¡Te esperamos!`
}

async function enviarConfirmacion() {
  const numero = props.phone || CLINIC_PHONE_FALLBACK
  const mensaje = encodeURIComponent(buildMessage())
  const url = `https://wa.me/${numero}?text=${mensaje}`

  window.open(url, '_blank')

  if (props.appointmentId) {
    try {
      await appointmentService.notificar(props.appointmentId)
    } catch (err) {
      console.error('No se pudo registrar la notificación en el backend.', err)
    }
  }
}
</script>