<!-- src/views/Users/Form.vue -->
<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded p-6 w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">
        {{ user ? 'Editar usuario' : 'Nuevo usuario' }}
      </h2>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block text-sm mb-1">Nombre *</label>
          <input v-model="form.name" type="text" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.name" class="text-red-600 text-sm">{{ errors.name[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">Email *</label>
          <input v-model="form.email" type="email" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
        </div>

        <div class="mb-3">
          <label class="block text-sm mb-1">
            {{ user ? 'Nueva contraseña (opcional)' : 'Contraseña *' }}
          </label>
          <input v-model="form.password" type="password" class="border rounded w-full px-3 py-2" />
          <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password[0] }}</p>
        </div>

        <div class="mb-4">
          <label class="block text-sm mb-1">Rol *</label>
          <select v-model="form.role" class="border rounded w-full px-3 py-2">
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
          </select>
          <p v-if="errors.role" class="text-red-600 text-sm">{{ errors.role[0] }}</p>
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
import userService from '@/services/userService'

const props = defineProps({ user: Object })
const emit = defineEmits(['close', 'saved'])

const form = reactive({
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
  role: props.user?.role || 'staff',
})

const errors = ref({})

async function submit() {
  errors.value = {}
  try {
    if (props.user) {
      const payload = { ...form }
      if (!payload.password) delete payload.password
      await userService.update(props.user.id, payload)
    } else {
      await userService.create(form)
    }
    emit('saved')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      alert(err.response?.data?.message || 'Error al guardar el usuario.')
    }
  }
}
</script>