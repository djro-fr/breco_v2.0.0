// frontend/breco/src/presentation/features/auth/router/authRoutes.ts
import type { RouteRecordRaw } from 'vue-router'

export const authRoutes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../pages/LoginPage.vue'),
    meta: {
      requiresGuest: true,
      title: 'Connexion',
    },
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../pages/RegisterPage.vue'),
    meta: {
      requiresGuest: true,
      title: 'Inscription',
    },
  },
  {
    path: '/auth/verify-email/:token',
    name: 'VerifyEmail',
    component: () => import('../pages/VerifyEmailPage.vue'),
    meta: { requiresGuest: true, title: 'Vérification email' },
  },
]
