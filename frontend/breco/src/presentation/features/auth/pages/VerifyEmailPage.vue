<!-- frontend/breco/src/presentation/features/auth/pages/VerifyEmailPage.vue -->
<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'

  const route = useRoute()
  const router = useRouter()
  const loading = ref(true)
  const error = ref<string | null>(null)
  const success = ref(false)

  const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8081/api'

  const verifyEmail = async () => {
    const token = route.params.token as string

    if (!token) {
      error.value = 'Token de vérification manquant'
      loading.value = false
      return
    }

    try {
      const response = await fetch(`${API_URL}/auth/verify-email/${token}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })

      const data = await response.json()

      if (response.ok) {
        success.value = true
        // Re-direction to login page after 3 seconds
        setTimeout(() => {
          router.push({ name: 'Login' })
        }, 3000)
      } else {
        error.value = data.error || 'Erreur lors de la vérification'
      }
    } catch (err) {
      error.value = 'Erreur de connexion au serveur'
      console.error('Verification error:', err)
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    verifyEmail()
  })
  </script>

  <template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
      <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <!-- Loading state -->
        <div v-if="loading" class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600">Vérification de votre email en cours...</p>
        </div>

        <!-- Success state -->
        <div v-else-if="success" class="text-center">
          <div class="mb-4">
            <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Email vérifié !</h2>
          <p class="text-gray-600 mb-4">Votre adresse email a été vérifiée avec succès.</p>
          <p class="text-sm text-gray-500">Redirection vers la page de connexion...</p>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="text-center">
          <div class="mb-4">
            <svg class="w-16 h-16 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Erreur de vérification</h2>
          <p class="text-red-600 mb-4">{{ error }}</p>
          <button
            @click="router.push({ name: 'Login' })"
            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            Retour à la connexion
          </button>
        </div>
      </div>
    </div>
  </template>
