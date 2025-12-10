// frontend\breco\src\presentation\router\index.ts

import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
import { authRoutes } from '@/presentation/features/auth/router/authRoutes'

// Public routes
const publicRoutes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/presentation/app/pages/HomePage.vue'),
    meta: { title: 'Accueil' },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/presentation/app/pages/DashboardPage.vue'),
    meta: { requiresAuth: true, title: 'Tableau de bord' },
  },
  {
      path: '/search',
      name: 'Search',
      component: () => import('@/presentation/app/pages/SearchPage.vue'),
      meta: { requiresAuth: true, title: 'Rechercher un trajet' },
  },
]

// Combine all routes
const routes: RouteRecordRaw[] = [
  ...publicRoutes,
  ...authRoutes,
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/presentation/app/pages/NotFoundPage.vue'),
    meta: { title: '404 - Page non trouvée' },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Global guard - Check authentication at first load
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // empty error state before each navigation
  authStore.error = null

  // Check authentication at first load
  if (!authStore.token && localStorage.getItem('token')) {
    await authStore.checkAuth()
  }

  // Redirection for protected routes
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({
      name: 'Login',
      query: { redirect: to.fullPath },
    })
  }
  // Redirection if authenticated and goes to Search
  else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'Search' })
  } else {
    next()
  }
})

// Update the page title
router.afterEach((to) => {
  document.title = `${(to.meta.title as string) || 'Page'} - Breco`
})

export default router
