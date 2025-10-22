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
  <div class="content">
    <header>
      <nav>
        <div class="nav-brand">
          <router-link to="/">
            <img
              src="../shared/assets/logo_breco_black.svg"
              height="32"
              alt="retour à l'accueil de Breco"
            />
          </router-link>
        </div>

        <!-- Bouton menu hamburger avec 3 barres -->
        <button class="menu-toggle" @click="isMenuOpen = !isMenuOpen" aria-label="Menu">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <!-- Menu (caché sur mobile) -->
        <div class="nav-menu" :class="{ open: isMenuOpen }">
          <div v-if="!isAuthenticated">
            <router-link to="/login" @click="isMenuOpen = false">Connexion</router-link>
            <router-link to="/register" @click="isMenuOpen = false">Inscription</router-link>
          </div>

          <div v-if="isAuthenticated">
            <router-link to="/dashboard" @click="isMenuOpen = false">Dashboard</router-link>
            <span>{{ authStore.user?.firstName }}</span>
            <button @click="handleLogout">Déconnexion</button>
          </div>
        </div>
      </nav>
    </header>

    <main class="main-content">
      <router-view />
    </main>

    <footer>
      <p>&copy; 2025 Breco</p>
    </footer>
  </div>
</template>

<style scoped>
div.content {
  margin: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-image: url('sebastian-Vc5XMryq8JM-unsplash.jpg');
  background-size: cover;
  background-attachment: fixed;
  background-repeat: no-repeat;
  background-color: #000;
  position: relative;
  padding: 0 16px;
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
  z-index: 1;
}

header {
  background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 100;
  margin: 0 -16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

nav {
  max-width: 100%;
  margin: 0 16px;
  padding: 8px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 60px;
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

.nav-menu {
  display: flex;
  gap: 20px;
  align-items: center;
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

.main-content {
  flex: 1;
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 40px 20px;
  box-sizing: border-box;
  position: relative;
  z-index: 2;
}

footer {
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  text-align: center;
  position: relative;
  z-index: 2;
  padding: 0px;
  margin: 0 -16px;
  height: 40px;
  display: flex;
  align-items: center;
}
footer p {
  margin: 0 auto;
  padding: 0;
  font-size: var(--fontXXS);
  color: #bbbbbb;
}

/* Mobile */
@media (max-width: 768px) {
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
    background-color: var(--primary-color);
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
