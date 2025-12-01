<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import FormInput from '@/presentation/shared/components/FormInput.vue'
import { emailSchema, phoneSchema, passwordSchema, nameSchema } from '@/utils/validationSchemas'
import { UserSchema } from '@/domain/entities/User'
import type { CreateUserData } from '@/domain/entities/User';
import { ZodError } from 'zod'

type RegisterFormField = keyof CreateUserData | 'password';

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const phone = ref('')
const password = ref('')
const firstName = ref('')
const lastName = ref('')
const driver = ref(false)
const gender = ref<'Homme' | 'Femme' | 'Ne pas dire' | ''>('')
const zipCode = ref('')
const town = ref('')
const carModel = ref('')
const carColor = ref('')
const carSeatNb = ref(0)

const currentStep = ref(1)
const passwordConfirm = ref('')

// Errors per field
const errors = ref<Record<string, string>>({})

const isLoading = computed(() => authStore.isLoading)
const globalError = computed(() => authStore.error)

// Real-time validation for each field
const validateField = (field: RegisterFormField, value: string | number | boolean) => {
  const optionalFields = ['zipCode', 'town', 'carModel', 'carColor', 'carSeatNb']
  if (optionalFields.includes(field as string) && (!value || (typeof value === 'string' && value.trim().length === 0))) {
    delete errors.value[field as string]
    return
  }
  try {
    switch (field) {
      case 'email':
        emailSchema.parse(value)
        break
      case 'phone':
        phoneSchema.parse(value)
        break
      case 'password':
        if (typeof value === 'string') passwordSchema.parse(value)
        break
      case 'firstName':
        if (typeof value === 'string') nameSchema('prénom').parse(value)
        break
      case 'lastName':
        if (typeof value === 'string') nameSchema('nom').parse(value)
        break
      case 'zipCode':
        if (typeof value === 'string' && value.trim().length > 0) {
          const zipCodeSchema = UserSchema.shape.zipCode
          zipCodeSchema.parse(value)
        } else {
          delete errors.value[field as string]
          return
        }
        break
    }
    delete errors.value[field as string]
  } catch (error) {
    if (error instanceof ZodError) {
      errors.value[field as string] = error.issues[0]?.message || 'Champ invalide'
    } else if (error instanceof Error) {
      errors.value[field as string] = error.message
    }
  }
}

const handleBlur = (field: RegisterFormField, value: string | number | boolean) => {
  validateField(field, value)
}
// Step by step validation
const step1Valid = computed(() => {
  return (
    email.value.length > 0 &&
    !errors.value.email &&
    password.value.length >= 8 &&
    !errors.value.password &&
    password.value === passwordConfirm.value &&
    phone.value.length > 0 &&
    !errors.value.phone
  )
})
const step2Valid = computed(() => {
  return (
    firstName.value.length > 0 &&
    !errors.value.firstName &&
    lastName.value.length > 0 &&
    !errors.value.lastName
  )
})
const step3Valid = computed(() => true)

// Password confirmation error
const passwordConfirmError = computed(() => {
  if (password.value !== passwordConfirm.value && passwordConfirm.value.length > 0) {
    return 'Les mots de passe ne correspondent pas'
  }
  return ''
})

const vehicleInfoConfirmation = computed(() => {
  if (driver.value) {
    if (carModel.value && carColor.value && carSeatNb.value) {
       let message =`${carModel.value} (${carColor.value}) - ${carSeatNb.value}`
       if(carSeatNb.value <= 1 ) message += ' place disponible'
       else message += ' places disponibles'
       return message
    } else {
      return 'Véhicule incomplet'
    }
  }
  return 'Aucun véhicule utilisé'
})

const nextStep = () => {
  if (currentStep.value === 1) {
    validateField('email', email.value)
    validateField('phone', phone.value)
    validateField('password', password.value)
    if (password.value !== passwordConfirm.value) {
      errors.value.passwordConfirm = 'Les mots de passe ne correspondent pas'
    } else {
      delete errors.value.passwordConfirm
    }

    if (step1Valid.value) {
      currentStep.value = 2
    }
  } else if (currentStep.value === 2) {
    validateField('firstName', firstName.value)
    validateField('lastName', lastName.value)
    validateField('zipCode', zipCode.value)

    if (!gender.value || gender.value.length === 0) {
      errors.value.gender = 'Le genre est requis'
    } else {
      delete errors.value.gender
    }

    if (step2Valid.value && !errors.value.gender) {
      currentStep.value = 3
    }
  } else if (currentStep.value === 3) {
    currentStep.value = 4
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value -= 1
  }
}

const handleRegister = async () => {
  //Errors reset
  errors.value = {}

  try {
    const cleanedPhone = phone.value.replace(/\s/g, '')

    await authStore.register(
      email.value,
      cleanedPhone,
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
  <div
    class="flex self-center flex-col mx-auto px-6 pt-6 pb-9.5 w-full max-w-[580px] bg-white rounded-md shadow-window"
  >
    <div class="max-w-full w-full">
      <h1 class="text-center pb-9 mt-0 leading-none font-black text-2xl">Créer un compte Breco</h1>

      <div class="flex justify-between items-center mb-10 gap-2">
        <!-- Step 1 -->
        <div class="flex flex-col items-center flex-1">
          <span
            class="flex mb-1 transition-all duration-300 items-center justify-center w-9 h-9 rounded-full font-bold"
            :class="currentStep === 1 ? 'bg-primary-light text-black' : 'bg-gray-dark text-white'"
          >
            1
          </span>
          <p class="text-gray-dark text-center text-md">Contact</p>
        </div>
        <!-- Separator 1-2 -->
        <div
          class="flex-1 h-0.5 my-0 mx-1 mb-6 transition-all duration-300"
          :class="currentStep > 1 ? 'bg-gray-dark' : 'bg-gray-light'"
        ></div>

        <!-- Step 2 -->
        <div class="flex flex-col items-center flex-1">
          <span
            class="flex mb-1 transition-all duration-300 items-center justify-center w-9 h-9 rounded-full font-bold"
            :class="[
              currentStep < 2
                ? 'bg-gray-light text-black'
                : currentStep === 2
                  ? 'bg-primary-light text-black'
                  : 'bg-gray-dark text-white',
            ]"
          >
            2
          </span>
          <p class="text-gray-dark text-center text-md">Identité</p>
        </div>
        <!-- Separator 2-3 -->
        <div
          class="flex-1 h-0.5 my-0 mx-1 mb-6 transition-all duration-300"
          :class="currentStep > 2 ? 'bg-gray-dark' : 'bg-gray-light'"
        ></div>

        <!-- Step 3 -->
        <div class="flex flex-col items-center flex-1">
          <span
            class="flex mb-1 transition-all duration-300 items-center justify-center w-9 h-9 rounded-full font-bold"
            :class="[
              currentStep < 3
                ? 'bg-gray-light text-black'
                : currentStep === 3
                  ? 'bg-primary-light text-black'
                  : 'bg-gray-dark text-white',
            ]"
          >
            3
          </span>
          <p class="text-gray-dark text-center text-md">Véhicule</p>
        </div>
        <!-- Separator 3-4 -->
        <div
          class="flex-1 h-0.5 my-0 mx-1 mb-6 transition-all duration-300"
          :class="currentStep > 3 ? 'bg-gray-dark' : 'bg-gray-light'"
        ></div>

        <!-- Step 4 -->
        <div class="flex flex-col items-center flex-1">
          <span
            class="flex mb-1 transition-all duration-300 items-center justify-center w-9 h-9 rounded-full font-bold"
            :class="[
              currentStep < 4
                ? 'bg-gray-light text-black'
                : currentStep === 4
                  ? 'bg-primary-light text-black'
                  : 'bg-gray-dark text-white',
            ]"
          >
            4
          </span>
          <p class="text-gray-dark text-center text-md">Confirmation</p>
        </div>
      </div>
      <!-- Step 1: Contact -->
      <div v-if="currentStep === 1" class="mb-7.5">
        <h2 class="text-2xl mb-4 text-black font-medium">Pour vous contacter</h2>

        <div class="mb-4">
          <FormInput
            v-model="email"
            type="email"
            placeholder="E-mail"
            label="E-mail"
            aria-label="E-mail"
            required
            :hasError="Boolean(errors.email)"
            @blur="handleBlur('email', email)"
          />
          <p v-if="errors.email" class="error-text mt-1 mb-6">{{ errors.email }}</p>
        </div>
        <p class="text-gray-dark text-md -mt-7 mb-6">
          Nous vous enverrons un e-mail de confirmation
        </p>

        <div class="mb-4">
          <FormInput
            v-model="password"
            type="password"
            placeholder="Mot de passe (min 8 caractères)"
            label="Mot de passe"
            aria-label="Mot de passe"
            required
            :hasError="Boolean(errors.password)"
            @blur="handleBlur('password', password)"
          />
          <p v-if="errors.password" class="error-text mt-1">{{ errors.password }}</p>
        </div>

        <div class="mb-4">
          <FormInput
            v-model="passwordConfirm"
            type="password"
            placeholder="Confirmez le mot de passe"
            label="Confirmez le mot de passe"
            aria-label="Confirmez le mot de passe"
            required
            :hasError="Boolean(passwordConfirmError)"
          />
          <p v-if="passwordConfirmError" class="error-text mt-1">{{ passwordConfirmError }}</p>
        </div>

        <div class="mb-4">
          <FormInput
            v-model="phone"
            type="tel"
            placeholder="Téléphone"
            label="Téléphone"
            aria-label="Téléphone"
            required
            class="w-40"
            :hasError="Boolean(errors.phone)"
            @blur="handleBlur('phone', phone)"
          />
          <p v-if="errors.phone" class="error-text mt-1">{{ errors.phone }}</p>
        </div>
      </div>

      <!-- Step 2: Identity -->
      <div v-if="currentStep === 2" class="mb-7.5">
        <h2 class="text-2xl mb-0 text-black font-medium">Votre identité</h2>

        <label class="block mt-1 mb-1 text-md text-primary-dark font-medium"
          >Genre<span class="text-error"> *</span></label
        >
        <div class="flex gap-2 mb-5">
          <button
            type="button"
            class="px-2 py-1 border border-primary-light rounded-md"
            :class="[
              gender === 'Homme' ? 'bg-primary-light text-black' : '',
              errors.gender ? 'border-error -mb-5' : '',
            ]"
            @click="
              gender = 'Homme',
              delete errors.gender
            "
          >
            Homme
          </button>
          <button
            type="button"
            class="px-2 py-1 border border-primary-light rounded-md"
            :class="[
              gender === 'Femme' ? 'bg-primary-light text-black' : '',
              errors.gender ? 'border-error -mb-5' : '',
            ]"
            @click="
              gender = 'Femme',
              delete errors.gender
            "
          >
            Femme
          </button>
          <button
            type="button"
            class="px-2 py-1 border border-primary-light rounded-md"
            :class="[
              gender === 'Ne pas dire' ? 'bg-primary-light text-black' : '',
              errors.gender ? 'border-error -mb-5' : '',
            ]"
            @click="
              gender = 'Ne pas dire',
              delete errors.gender
            "
          >
            Ne pas dire
          </button>
        </div>

        <p v-if="errors.gender" class="error-text mt-1 mb-4">{{ errors.gender }}</p>

        <div class="mb-4">
          <FormInput
            v-model="firstName"
            type="text"
            placeholder="Prénom"
            label="Prénom"
            aria-label="Prénom"
            required
            :hasError="Boolean(errors.firstName)"
            @blur="handleBlur('firstName', firstName)"
          />
          <p v-if="errors.firstName" class="error-text mt-1">{{ errors.firstName }}</p>
        </div>

        <div class="mb-4">
          <FormInput
            v-model="lastName"
            type="text"
            placeholder="Nom"
            label="Nom"
            aria-label="Nom"
            required
            :hasError="Boolean(errors.lastName)"
            @blur="handleBlur('lastName', lastName)"
          />
          <p v-if="errors.lastName" class="error-text mt-1">{{ errors.lastName }}</p>
        </div>

        <div class="mb-4">
          <FormInput
            v-model="zipCode"
            type="text"
            placeholder="Code Postal"
            label="Code Postal"
            aria-label="Code Postal"
            class="w-40"
            :hasError="Boolean(errors.zipCode)"
            @blur="handleBlur('zipCode', zipCode)"
          />
          <p v-if="errors.zipCode" class="error-text mt-1">{{ errors.zipCode }}</p>
        </div>
        <div class="mb-4">
          <FormInput
            v-model="town"
            type="text"
            placeholder="Ville"
            label="Ville"
            aria-label="Ville"
          />
        </div>
      </div>
      <!-- Step 3: Car -->
      <div v-if="currentStep === 3" class="mb-7.5">
        <h2 class="text-2xl mb-0 text-black font-medium">Votre véhicule</h2>

        <label class="block mt-1 mb-1 text-md text-primary-dark font-medium"
          >Souhaitez-vous utiliser votre voiture ?</label
        >
        <div class="flex gap-2 mb-5">
          <button
            type="button"
            class="px-2 py-1 border border-primary-light rounded-md"
            :class="driver === true ? 'bg-primary-light text-black' : ''"
            @click="driver = true"
          >
            Oui
          </button>
          <button
            type="button"
            class="px-2 py-1 border border-primary-light rounded-md"
            :class="driver === false ? 'bg-primary-light text-black' : ''"
            @click="driver = false"
          >
            Non
          </button>
        </div>
        <div v-if="driver">
          <div class="mb-4">
            <FormInput
              v-model="carModel"
              type="text"
              placeholder="Modèle (ex: Toyota Prius)"
              label="Modèle"
              aria-label="Modèle"
            />
          </div>
          <div class="mb-4">
            <FormInput
              v-model="carColor"
              type="text"
              placeholder="Couleur"
              label="Couleur"
              aria-label="Couleur"
            />
            <label class="block -mb-4 text-md text-primary-dark font-medium"
              >Nombre de places disponibles</label
            >
          </div>
          <div class="mb-4">
            <FormInput v-model="carSeatNb" type="number" class="w-20" />
          </div>
        </div>
      </div>

      <!-- Step 4: Confirmation -->
      <div v-if="currentStep === 4" class="mb-7.5">
        <h2 class="text-2xl mb-2 text-black font-medium">Récapitulatif</h2>
        <div class="p-4 rounded mb-4 bg-white-back">
          <div class="py-2 text-sm">
            <strong class="text-primary-dark">Nom complet :</strong> {{ firstName }} {{ lastName }}
          </div>
          <div class="py-2 text-sm">
            <strong class="text-primary-dark">Email :</strong> {{ email }}
          </div>
          <div class="py-2 text-sm">
            <strong class="text-primary-dark">Téléphone :</strong> {{ phone }}
          </div>
          <div class="py-2 text-sm">
            <strong class="text-primary-dark">Genre :</strong> {{ gender }}
          </div>
          <div v-if="zipCode || town" class="py-2 text-sm">
            <strong class="text-primary-dark">Localisation :</strong> {{ zipCode }} {{ town }}
          </div>
          <div v-if="driver" class="py-2 text-sm">
            <strong class="text-primary-dark">Véhicule :</strong> {{ vehicleInfoConfirmation }}
          </div>
        </div>

        <p v-if="globalError" class="error-message">{{ globalError }}</p>
      </div>

      <div class="text-center mb-4" v-if="currentStep < 4">
        <p>
          <em>Les champs avec <span class="text-error"> *</span> sont obligatoires</em>
        </p>
      </div>

      <div class="flex gap-3 mb-4">
        <button v-if="currentStep > 1" type="button" class="btn-secondary" @click="previousStep">
          &lsaquo; Retour
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
          Suivant &rsaquo;;
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

      <p class="text-center text-md text-gray-dark">
        Vous avez un compte ?
        <router-link to="/login">Se connecter</router-link>
      </p>
    </div>
  </div>
</template>
