<!-- src/views/Doctors/Form.vue -->
<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">
        {{ doctor ? 'Editar doctor' : 'Nuevo doctor' }}
      </h2>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block text-sm mb-1">Nombre *</label>
          <input v-model="form.name" type="text" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.name" class="text-red-600 text-sm">{{ errors.name[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Especialidad</label>
          <input v-model="form.specialty" type="text" class="border rounded w-full px-3 py-2" />
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Teléfono</label>
          <input v-model="form.phone" type="text" class="border rounded w-full px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block text-sm mb-1">Email</label>
          <input v-model="form.email" type="email" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
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
import { reactive, ref } from 'vue'
import doctorService from '@/services/doctorService'

const props = defineProps({ doctor: Object })
const emit = defineEmits(['close', 'saved'])

const form = reactive({
  name: props.doctor?.name || '',
  specialty: props.doctor?.specialty || '',
  phone: props.doctor?.phone || '',
  email: props.doctor?.email || '',
})

const errors = ref({})

async function submit() {
  errors.value = {}
  try {
    if (props.doctor) {
      await doctorService.update(props.doctor.id, form)
    } else {
      await doctorService.create(form)
    }
    emit('saved')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      alert(err.response?.data?.message || 'Error al guardar el doctor.')
    }
  }
}
</script>