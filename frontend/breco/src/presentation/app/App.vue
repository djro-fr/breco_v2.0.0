<!-- frontend\breco\src\presentation\app\App.vue -->
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()
const isMenuOpen = ref(false)

const isAuthenticated = computed(() => authStore.isAuthenticated)

onMounted(async () => {
  await authStore.checkAuth()
})

const handleLogout = async (): Promise<void> => {
  await authStore.logout()
  isMenuOpen.value = false
  router.push({ name: 'Login' })
}
</script>

<template>
  <div class="content m-0 py-0 flex flex-col min-h-screen">
    <header class="sticky bg-white top-0 z-100 my-0">
      <nav class="my-0 mx-4 py-2 px-0 flex max-w-full justify-between items-center h-15">
        <div class="nav-brand">
          <router-link to="/">
            <img
              src="../shared/assets/logo_breco_black.svg"
              class="h-8 hover:opacity-80"
              alt="retour à l'accueil de Breco"
            />
          </router-link>
        </div>

        <button class="menu-toggle" @click="isMenuOpen = !isMenuOpen" aria-label="Menu">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <div class="nav-menu flex gap-5 items-center max-[768px]:bg-white-dark max-[768px]:opacity-97" :class="{ open: isMenuOpen }">
          <div v-if="!isAuthenticated">
            <router-link to="/login" @click="isMenuOpen = false">Connexion</router-link>
            <router-link to="/register" @click="isMenuOpen = false">Inscription</router-link>
          </div>

          <div v-if="isAuthenticated" class="w-fit flex flex-row mb-0 mt-1.5">
            <span class="font-normal text-lg flex self-center max-[768px]:-mt-4 max-[768px]:-mb-6 "><router-link to="/dashboard" @click="isMenuOpen = false">Dashboard</router-link></span>
            <span class="font-normal text-lg flex self-center mr-3 max-[768px]:mb-2"
              >&nbsp;de&nbsp;<strong>{{ authStore.user?.firstName }}</strong>
            </span>
            <button @click="handleLogout" class="block btn-secondary mx-auto mb-1.5">Déconnexion</button>
          </div>
        </div>
      </nav>
    </header>

    <main class="box-border relative flex min-[769px]:w-full max-[768px]:w-[calc(100% - 32px)] mx-4 my-0 px-0 py-6 flex-1">
      <router-view />
    </main>

    <footer class="z-2 relative flex h-10 mt-auto p-0 items-center justify-center text-center text-white bg-black ">
      <p class="my-0 mx-auto p-0 text-gray-light text-xs">&copy; 2025 Breco</p>
    </footer>
  </div>
</template>

<style scoped>

div.content {
  background-image: url('/sebastian-Vc5XMryq8JM-unsplash.jpg');
  background-size: cover;
  background-attachment: fixed;
  background-repeat: no-repeat;
  background-color: #000;
  position: relative;
}
div.content::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(3, 17, 46, 0.4);
  pointer-events: none;
}

.menu-toggle {
  display: none;
  background: none;
  position: relative;
  border: none;
  cursor: pointer;
  width: 30px;
  height: 36px;
  padding: 0;
  top: 2px;
}
.menu-toggle span {
  display: block;
  width: 100%;
  height: 3px;
  border-radius: 2px;
  background-color: #333;
  margin: 5px 0;
  transition: all 0.3s ease;
  position: absolute;
}
.menu-toggle span:nth-child(1) {
  top: 0;
}
.menu-toggle span:nth-child(2) {
  top: 8px;
}
.menu-toggle span:nth-child(3) {
  top: 16px;
}
.menu-toggle:has(+ .nav-menu.open) span:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}
.menu-toggle:has(+ .nav-menu.open) span:nth-child(2) {
  opacity: 0;
  transform: scale(0);
}
.menu-toggle:has(+ .nav-menu.open) span:nth-child(3) {
  transform: rotate(-45deg) translate(6px, -6px);
}


.nav-menu a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  padding-left: 16px;
}
.nav-menu a:hover {
  opacity: 0.7;
  border: none;
}


/* Mobile */
@media (max-width: 768px) {
  div.logout {
    display: flex;
  }

  .menu-toggle {
    display: block;
  }

  .nav-menu {
    position: absolute;
    display: none;
    top: 52px;
    margin: 0;
    padding: 16px;
    left: 0;
    right: 0;
    flex-direction: column;
    z-index: 200;
    box-shadow: 0 10px 10px rgba(0, 0, 0, 0.2);
  }
  .nav-menu div {
    display: flex;
    flex-direction: column;
    width: 100%;
  }
  .nav-menu div a {
    width: 100%;
    padding: 8px 0 8px 0;
    text-align: center;
    margin-bottom: 6px;
    color: black;
    font-size: var(--fontS);
  }
  .nav-menu div a:hover {
    opacity: 0.7;
  }

  .nav-menu.open {
    display: flex;
  }

  nav {
    height: auto;
  }

  .main-content {
    padding: 20px;
  }
}
</style>
