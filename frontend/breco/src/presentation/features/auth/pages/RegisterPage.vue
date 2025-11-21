// frontend/Breco/src/presentation/features/auth/pages/RegisterPage.vue

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const phone = ref('')
const password = ref('')
const firstName = ref('')
const lastName = ref('')
const driver = ref(false)
const gender = ref('')
const zipCode = ref('')
const town = ref('')
const carModel = ref('')
const carColor = ref('')
const carSeatNb = ref(0)

const currentStep = ref(1)
const passwordConfirm = ref('')

const isLoading = computed(() => authStore.isLoading)
const error = computed(() => authStore.error)

// Step by step validation
const step1Valid = computed(
  () =>
    email.value.length > 0 &&
    email.value.includes('@') &&
    password.value.length >= 8 &&
    password.value === passwordConfirm.value &&
    phone.value.length > 0,
)

const step2Valid = computed(
  () => firstName.value.length > 0 && lastName.value.length > 0 && gender.value.length > 0,
)

// No validation needed for step 3 (car)
const step3Valid = computed(() => true)

// Detailed password error messages
const passwordError = computed(() => {
  if (password.value.length < 8 && password.value.length > 0) {
    return 'Le mot de passe doit contenir au moins 8 caractères'
  }
  if (password.value !== passwordConfirm.value && passwordConfirm.value.length > 0) {
    return 'Les mots de passe ne correspondent pas'
  }
  return ''
})

const nextStep = () => {
  if (currentStep.value === 1 && step1Valid.value) {
    currentStep.value = 2
  } else if (currentStep.value === 2 && step2Valid.value) {
    currentStep.value = 3
  } else if (currentStep.value === 3 && step3Valid.value) {
    currentStep.value = 4
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value -= 1
  }
}



const handleRegister = async () => {
  try {
    await authStore.register(
      email.value,
      phone.value,
      password.value,
      firstName.value,
      lastName.value,
      driver.value,
      gender.value || undefined,
      zipCode.value || undefined,
      town.value || undefined,
      carModel.value || undefined,
      carColor.value || undefined,
      carSeatNb.value || undefined,
    )

    router.push({ name: 'Dashboard' })
  } catch (err) {
    console.error('Register error:', err)
  }
}
</script>

<template>
  <div class="whiteWindow register-container ma0">
    <div class="register-card">
      <h1 class="text-center">Créer un compte Breco</h1>

      <div class="progress-indicator">
        <div class="progress-step" :class="{ active: currentStep >= 1, completed: currentStep > 1 }">
          <span>1</span>
          <p>Contact</p>
        </div>
        <div class="progress-line" :class="{ completed: currentStep > 1 }"></div>

        <div class="progress-step" :class="{ active: currentStep >= 2, completed: currentStep > 2 }">
          <span>2</span>
          <p>Identité</p>
        </div>
        <div class="progress-line" :class="{ completed: currentStep > 2 }"></div>

        <div class="progress-step" :class="{ active: currentStep >= 3, completed: currentStep > 3 }">
          <span>3</span>
          <p>Véhicule</p>
        </div>
        <div class="progress-line" :class="{ completed: currentStep > 3 }"></div>

        <div class="progress-step" :class="{ active: currentStep >= 4, completed: currentStep > 4 }">
          <span>4</span>
          <p>Confirmation</p>
        </div>
      </div>

      <!-- Step 1: Contact -->
      <div v-if="currentStep === 1" class="step-content">
        <h2>Pour vous contacter</h2>

        <input
          v-model="email"
          type="email"
          placeholder="Email"
          aria-label="Email"
          required
        />
        <p class="helper-text">Nous vous enverrons un email de confirmation</p>

        <input
          v-model="password"
          type="password"
          placeholder="Mot de passe (min 8 caractères)"
          aria-label="Mot de passe"
          required
        />
        <input
          v-model="passwordConfirm"
          type="password"
          placeholder="Confirmez le mot de passe"
          aria-label="Confirmez le mot de passe"
          required
        />
        <p v-if="passwordError" class="error-text">{{ passwordError }}</p>
        <p class="helper-text">Minimum 8 caractères recommandés</p>

        <input
          v-model="phone"
          type="tel"
          placeholder="Téléphone"
          aria-label="Téléphone"
          required
        />
      </div>

      <!-- Step 2: Identity -->
      <div v-if="currentStep === 2" class="step-content">
        <h2>Votre identité</h2>

        <label>Genre</label>
        <div class="button-group-gender">
          <button
            type="button"
            class="gender-btn"
            :class="{ active: gender === 'Homme' }"
            @click="gender = 'Homme'"
          >
            Homme
          </button>
          <button
            type="button"
            class="gender-btn"
            :class="{ active: gender === 'Femme' }"
            @click="gender = 'Femme'"
          >
            Femme
          </button>
          <button
            type="button"
            class="gender-btn"
            :class="{ active: gender === 'je préfère ne pas le dire' }"
            @click="gender = 'je préfère ne pas le dire'"
          >
            Ne pas dire
          </button>
        </div>

        <input
          v-model="firstName"
          type="text"
          placeholder="Prénom"
          aria-label="Prénom"
          required
        />
        <input
          v-model="lastName"
          type="text"
          placeholder="Nom"
          aria-label="Nom"
          required
        />

        <input
          v-model="zipCode"
          type="text"
          placeholder="Code Postal"
          aria-label="Code Postal"
        />
        <input
          v-model="town"
          type="text"
          placeholder="Ville"
          aria-label="Ville"
        />
      </div>

      <!-- Step 3: Car -->
      <div v-if="currentStep === 3" class="step-content">
        <h2>Votre véhicule</h2>

        <label>Souhaitez-vous utiliser votre voiture ?</label>
        <div class="button-group-driver">
          <button
            type="button"
            class="driver-btn"
            :class="{ active: driver === true }"
            @click="driver = true"
          >
            Oui
          </button>
          <button
            type="button"
            class="driver-btn"
            :class="{ active: driver === false }"
            @click="driver = false"
          >
            Non
          </button>
        </div>

        <input
          v-model="carModel"
          :disabled="!driver"
          type="text"
          placeholder="Modèle (ex: Toyota Prius)"
          aria-label="Modèle"
        />
        <input
          v-model="carColor"
          :disabled="!driver"
          type="text"
          placeholder="Couleur"
          aria-label="Couleur"
        />
        <label>Nombre de places disponibles</label>
        <input
          v-model="carSeatNb"
          :disabled="!driver"
          type="number"
        />
      </div>

      <!-- Step 4: Confirmation -->
      <div v-if="currentStep === 4" class="step-content">
        <h2>Récapitulatif</h2>
        <div class="summary">
          <div class="summary-item">
            <strong>Nom complet :</strong> {{ firstName }} {{ lastName }}
          </div>
          <div class="summary-item">
            <strong>Email :</strong> {{ email }}
          </div>
          <div class="summary-item">
            <strong>Téléphone :</strong> {{ phone }}
          </div>
          <div class="summary-item">
            <strong>Genre :</strong> {{ gender }}
          </div>
          <div v-if="zipCode || town" class="summary-item">
            <strong>Localisation :</strong> {{ zipCode }} {{ town }}
          </div>
          <div v-if="driver" class="summary-item">
            <strong>Véhicule :</strong> {{ carModel }} ({{ carColor }})
            - {{ carSeatNb }} place(s)
          </div>
        </div>

        <p v-if="error" class="error-message">{{ error }}</p>
      </div>

      <!-- Navigation Buttons -->
      <div class="button-group">
        <button
          v-if="currentStep > 1"
          type="button"
          class="btn-secondary"
          @click="previousStep"
        >
          Retour
        </button>

        <button
          v-if="currentStep < 4"
          type="button"
          class="btn-action"
          :disabled="
            (currentStep === 1 && !step1Valid) ||
            (currentStep === 2 && !step2Valid) ||
            (currentStep === 3 && !step3Valid)
          "
          @click="nextStep"
        >
          Suivant
        </button>

        <button
          v-if="currentStep === 4"
          type="submit"
          class="btn-action"
          :disabled="isLoading"
          @click="handleRegister"
        >
          {{ isLoading ? 'Inscription...' : 'Créer mon compte' }}
        </button>
      </div>

      <p class="login-link">
        Vous avez un compte ?
        <router-link to="/login">Se connecter</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.register-container {
  max-width: 580px;
}

.register-card {
  max-width: 100%;
  width: 100%;
}

h1 {
  margin-top: 0px;
  line-height: 1;
  font-weight: 800;
  font-size: var(--fontL);
  padding-bottom: 16px;
}

h2 {
  font-size: 20px;
  margin-bottom: 20px;
  color: #333;
}

div.whiteWindow {
  margin-top: 30px;
  padding: 30px 16px;
}

div.whiteWindow input {
  width: calc(100% - 10px);
  border: none;
  background-color: var(--dark-white);
  border-bottom: 1px solid var(--primary-color);
  margin-bottom: 20px;
  font-size: var(--fontXS);
  padding: 2px 5px;
}

.progress-indicator {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 40px;
  gap: 8px;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.progress-step span {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: #e0e0e0;
  color: #999;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-bottom: 4px;
  transition: all 0.3s;
}

.progress-step.active span {
  background-color: var(--primary-color);
  color: white;
}

.progress-step.completed span {
  background-color: var(--action);
  color: white;
}

.progress-step p {
  font-size: 12px;
  color: #999;
  text-align: center;
}

.progress-line {
  flex: 1;
  height: 2px;
  background-color: #e0e0e0;
  margin: 0 4px;
  transition: all 0.3s;
}

.progress-line.completed {
  background-color: var(--action);
}

.step-content {
  margin-bottom: 30px;
}

input:focus {
  outline: none;
  border-color: var(--primary-color-dark);
  box-shadow: 0 0 0 3px rgba(0, 121, 148, 0.1);
}

input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
  opacity: .4;
}

.helper-text {
  font-size: 12px;
  color: #999;
  margin-top: -8px;
  margin-bottom: 12px;
}

.error-text {
  font-size: 12px;
  color: #f44336;
  margin-top: -8px;
  margin-bottom: 12px;
}

.error-message {
  color: #f44336;
  background-color: #ffebee;
  padding: 12px;
  border-radius: 4px;
  margin-bottom: 12px;
  font-size: 14px;
}

.button-group-gender, .button-group-driver {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
}

.gender-btn, .driver-btn {
  flex: 1;
  padding: 10px 16px;
  border: 2px solid #ddd;
  background-color: white;
  color: #333;
  border-radius: 4px;
  cursor: pointer;
  font-family: 'Baloo 2', sans-serif;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s;
}

.gender-btn:hover, .driver-btn:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.gender-btn.active, .driver-btn.active {
  background-color: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.radio-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 12px;
}

.radio-group div {
  display: flex;
  align-items: center;
  gap: 8px;
}

.radio-group input {
  margin: 0;
  width: auto;
  border: none;
  margin-bottom: 0;
}

.radio-group label {
  margin: 0;
  font-size: 14px;
  border: none;
  background-color: transparent;
  padding: 0;
  margin-bottom: 0;
}

.summary {
  background-color: #f5f5f5;
  padding: 16px;
  border-radius: 4px;
  margin-bottom: 16px;
}

.summary-item {
  padding: 8px 0;
  font-size: 14px;
  color: #333;
}

.summary-item strong {
  color: var(--primary-color-dark);
}

.button-group {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.login-link {
  text-align: center;
  font-size: 14px;
  color: #666;
}

.login-link a {
  color: var(--primary-color-dark);
  text-decoration: none;
  font-weight: 600;
}

.login-link a:hover {
  text-decoration: underline;
}

@media (max-width: 600px) {
  .register-card {
    padding: 24px;
  }

  .progress-indicator {
    margin-bottom: 24px;
  }

  .progress-step span {
    width: 32px;
    height: 32px;
    font-size: 12px;
  }

  .progress-step p {
    font-size: 10px;
  }
}
</style>
