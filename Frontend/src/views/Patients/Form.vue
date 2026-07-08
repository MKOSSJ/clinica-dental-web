<!-- src/views/Patients/Form.vue -->
<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">
        {{ patient ? 'Editar paciente' : 'Nuevo paciente' }}
      </h2>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block text-sm mb-1">Nombre *</label>
          <input v-model="form.name" type="text" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.name" class="text-red-600 text-sm">{{ errors.name[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Teléfono</label>
          <input v-model="form.phone" type="text" class="border rounded w-full px-3 py-2" />
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Email</label>
          <input v-model="form.email" type="email" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Fecha de nacimiento</label>
          <input v-model="form.birth_date" type="date" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.birth_date" class="text-red-600 text-sm">{{ errors.birth_date[0] }}</p>
        </div>

        <div class="mb-4">
          <label class="block text-sm mb-1">Dirección</label>
          <input v-model="form.address" type="text" class="border rounded w-full px-3 py-2" />
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
import patientService from '@/services/patientService'

const props = defineProps({ patient: Object })
const emit = defineEmits(['close', 'saved'])

const form = reactive({
  name: props.patient?.name || '',
  phone: props.patient?.phone || '',
  email: props.patient?.email || '',
  birth_date: props.patient?.birth_date || '',
  address: props.patient?.address || '',
})

const errors = ref({})

async function submit() {
  errors.value = {}
  try {
    if (props.patient) {
      await patientService.update(props.patient.id, form)
    } else {
      await patientService.create(form)
    }
    emit('saved')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      alert(err.response?.data?.message || 'Error al guardar el paciente.')
    }
  }
}
</script>