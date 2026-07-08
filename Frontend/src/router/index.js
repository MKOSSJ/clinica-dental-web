import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import LoginView from '@/views/Login.vue'
import ForgotPasswordView from '@/views/ForgotPassword.vue'
import UnauthorizedView from '@/views/Unauthorized.vue'
import DashboardView from '@/views/Dashboard.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
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
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true },
    },
    {
      path: '/admin',
      name: 'admin',
      component: DashboardView,
      meta: { requiresAuth: true, roles: ['admin'] },
    },
    {
      path: '/pacientes',
      name: 'patients',
      component: () => import('@/views/Patients/Index.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/doctores',
      name: 'doctors',
      component: () => import('@/views/Doctors/Index.vue'),
      meta: { requiresAuth: true },
    }
  ],
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
    return
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
    return
  }

  if (to.meta.roles && !to.meta.roles.includes(authStore.role)) {
    next({ name: 'unauthorized' })
    return
  }

  next()
})


export default router
