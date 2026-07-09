import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import LoginView from '@/views/Login.vue'
import ForgotPasswordView from '@/views/ForgotPassword.vue'
import UnauthorizedView from '@/views/Unauthorized.vue'
import AppLayout from '@/layouts/AppLayout.vue'

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guest: true },
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPasswordView,
    meta: { guest: true },
  },
  {
    path: '/unauthorized',
    name: 'unauthorized',
    component: UnauthorizedView,
  },
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { roles: ['admin', 'staff'] },
      },
      {
        path: 'admin',
        name: 'admin',
        component: () => import('@/views/Dashboard.vue'),
        meta: { roles: ['admin'] },
      },
      {
        path: 'pacientes',
        name: 'patients',
        component: () => import('@/views/Patients/Index.vue'),
        meta: { roles: ['admin', 'staff'] },
      },
      {
        path: 'doctores',
        name: 'doctors',
        component: () => import('@/views/Doctors/Index.vue'),
        meta: { roles: ['admin', 'staff'] },
      },
      {
        path: 'citas',
        name: 'appointments',
        component: () => import('@/views/Appointments/Index.vue'),
        meta: { roles: ['admin', 'staff'] },
      },
      {
        path: 'citas/historial/:patientId',
        name: 'appointments-history',
        component: () => import('@/views/Appointments/History.vue'),
        meta: { roles: ['admin', 'staff'] },
      },
      {
        path: 'usuarios',
        name: 'users',
        component: () => import('@/views/Users/Index.vue'),
        meta: { roles: ['admin'] },
      },
      {
        path: 'reportes',
        name: 'reports',
        component: () => import('@/views/Reports/Index.vue'),
        meta: { roles: ['admin'] },
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)

  if (requiresAuth && !authStore.isAuthenticated) {
    return next({ name: 'login' })
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    return next({ name: 'dashboard' })
  }

  const roles = to.meta.roles

  if (roles && !roles.includes(authStore.role)) {
    return next({ name: 'unauthorized' })
  }

  next()
})

export default router