<!-- src/layouts/AppLayout.vue -->
<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'bg-gray-900 text-white flex flex-col transition-all duration-200',
        collapsed ? 'w-16' : 'w-64',
      ]"
    >
      <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <span v-if="!collapsed" class="font-bold text-lg">ClinicaDental</span>
        <button @click="collapsed = !collapsed" class="text-gray-300 hover:text-white">
          <MenuIcon class="w-6 h-6" />
        </button>
      </div>

      <nav class="flex-1 overflow-y-auto py-4 space-y-1">
        <router-link
          v-for="item in visibleMenu"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
          active-class="bg-gray-800 text-white border-l-4 border-blue-500"
        >
          <component :is="item.icon" class="w-5 h-5 shrink-0" />
          <span v-if="!collapsed">{{ item.label }}</span>
        </router-link>
      </nav>

      <div class="border-t border-gray-700 p-4">
        <div v-if="!collapsed" class="mb-2 text-sm text-gray-400">
          {{ authStore.user?.name }}
          <span class="block text-xs uppercase text-gray-500">{{ authStore.role }}</span>
        </div>
        <button
          @click="authStore.logout"
          class="w-full flex items-center gap-2 justify-center bg-red-600 hover:bg-red-700 text-white text-sm py-2 rounded"
        >
          <LogOutIcon class="w-4 h-4" />
          <span v-if="!collapsed">Cerrar sesión</span>
        </button>
      </div>
    </aside>

    <!-- Contenido -->
    <main class="flex-1 overflow-y-auto">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import {
  LayoutDashboard as DashboardIcon,
  Users as UsersIcon,
  Stethoscope as DoctorsIcon,
  CalendarDays as AppointmentsIcon,
  UserCog as PatientsIcon,
  BarChart3 as ReportsIcon,
  MapPin as IntegrationsIcon,
  Menu as MenuIcon,
  LogOut as LogOutIcon,
} from 'lucide-vue-next'

const authStore = useAuthStore()
const collapsed = ref(false)

const menu = [
  { to: '/dashboard', label: 'Panel', icon: DashboardIcon, roles: ['admin', 'staff'] },
  { to: '/pacientes', label: 'Pacientes', icon: PatientsIcon, roles: ['admin', 'staff'] },
  { to: '/doctores', label: 'Doctores', icon: DoctorsIcon, roles: ['admin', 'staff'] },
  { to: '/citas', label: 'Citas', icon: AppointmentsIcon, roles: ['admin', 'staff'] },
  { to: '/reportes', label: 'Reportes', icon: ReportsIcon, roles: ['admin'] },
  { to: '/usuarios', label: 'Usuarios', icon: UsersIcon, roles: ['admin'] },
  { to: '/integraciones', label: 'Clínica', icon: IntegrationsIcon, roles: ['admin', 'staff'] },
]

const visibleMenu = computed(() =>
  menu.filter((item) => item.roles.includes(authStore.role))
)
</script>