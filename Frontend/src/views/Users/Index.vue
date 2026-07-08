<!-- src/views/Users/Index.vue -->
<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-xl font-bold">Usuarios</h1>
      <button @click="openCreate" class="bg-blue-600 text-white px-4 py-2 rounded">
        + Nuevo usuario
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
          <th class="p-2">Email</th>
          <th class="p-2">Rol</th>
          <th class="p-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id" class="border-b">
          <td class="p-2">{{ user.name }}</td>
          <td class="p-2">{{ user.email }}</td>
          <td class="p-2">
            <span class="px-2 py-1 rounded text-xs bg-gray-100">{{ user.role }}</span>
          </td>
          <td class="p-2 space-x-2">
            <button @click="openEdit(user)" class="text-blue-600">Editar</button>
            <button
              v-if="user.id !== authStore.user?.id"
              @click="confirmDelete(user)"
              class="text-red-600"
            >
              Eliminar
            </button>
          </td>
        </tr>
        <tr v-if="users.length === 0">
          <td colspan="4" class="p-4 text-center text-gray-500">Sin resultados</td>
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

    <UserForm
      v-if="showForm"
      :user="selectedUser"
      @close="showForm = false"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import userService from '@/services/userService'
import UserForm from './Form.vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

const users = ref([])
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const errorMessage = ref('')
const showForm = ref(false)
const selectedUser = ref(null)

let debounceTimer = null
function debouncedSearch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchUsers()
  }, 400)
}

async function fetchUsers() {
  try {
    const { data } = await userService.list({
      search: search.value,
      page: currentPage.value,
    })
    users.value = data.data.data
    lastPage.value = data.data.last_page
  } catch (err) {
    errorMessage.value = 'Error al cargar usuarios.'
  }
}

function goToPage(page) {
  currentPage.value = page
  fetchUsers()
}

function openCreate() {
  selectedUser.value = null
  showForm.value = true
}

function openEdit(user) {
  selectedUser.value = user
  showForm.value = true
}

async function confirmDelete(user) {
  if (!confirm(`¿Eliminar a ${user.name}?`)) return
  try {
    await userService.remove(user.id)
    fetchUsers()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'No se pudo eliminar el usuario.'
  }
}

function handleSaved() {
  showForm.value = false
  fetchUsers()
}

onMounted(fetchUsers)
</script>