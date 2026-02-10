<script setup lang="ts">
import { hourSchema } from '@/utils/validationSchemas'
import { ref } from 'vue'
import { ZodError } from 'zod'

type SearchFormField = 'heureDepart' | 'heureArrivee'

// Form data
const heureDepart = ref('')
const heureArrivee = ref('')

// Errors per field
const errors = ref<Record<string, string>>({})

// Auto-format time as user types (HH:MM)
const formatTimeInput = (value: string): string => {
  // if user types "1:12", we want to allow it and format to "01:12"
  if (value.includes(':')) {
    const parts = value.split(':')
    const hours = parts[0]?.replace(/\D/g, '').slice(0, 2)
    const minutes = parts[1] ? parts[1].replace(/\D/g, '').slice(0, 2) : ''
    if (minutes) {
      return `${hours}:${minutes}`
    }
    return `${hours}:`
  }

  // Else, remove all non-digits
  const digits = value.replace(/\D/g, '')
  // Limit to 4 digits max
  const limited = digits.slice(0, 4)

  // Format as HH:MM
  if (limited.length === 0) return ''
  if (limited.length <= 2) return limited
  return `${limited.slice(0, 2)}:${limited.slice(2)}`
}

// Complete time with leading zero if needed (on blur)
const completeTimeFormat = (value: string): string => {
  if (!value) return ''

  // Already correctly formatted
  if (/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/.test(value)) {
    return value
  }

  // Handle "1:23" → "01:23" or "12:3" → "12:03"
  if (value.includes(':')) {
    const parts = value.split(':')
    const hours = (parts[0] || '0').padStart(2, '0')
    const minutes = (parts[1] || '00').padStart(2, '0')
    return `${hours}:${minutes}`
  }

  // Remove all non-digits
  const digits = value.replace(/\D/g, '')

  // Handle different lengths
  if (digits.length === 0) return ''
  if (digits.length === 1) return `0${digits}:00`
  if (digits.length === 2) return `${digits}:00`
  if (digits.length === 3) return `0${digits[0]}:${digits.slice(1)}`
  if (digits.length >= 4) return `${digits.slice(0, 2)}:${digits.slice(2, 4)}`

  return value
}

// Handle input event (real-time masking)
const handleTimeInput = (field: SearchFormField, event: Event) => {
  const input = event.target as HTMLInputElement
  // Format the value
  const formatted = formatTimeInput(input.value)
  // Update the model
  if (field === 'heureDepart') {
    heureDepart.value = formatted
  } else {
    heureArrivee.value = formatted
  }
}

const handleBlur = (field: SearchFormField) => {
  // Complete the format on blur
  const value = field === 'heureDepart' ? heureDepart.value : heureArrivee.value
  const completed = completeTimeFormat(value)

  // Update the model with completed value
  if (field === 'heureDepart') {
    heureDepart.value = completed
  } else {
    heureArrivee.value = completed
  }
}

const handleSubmit = (event: Event) => {
  event.preventDefault()
  errors.value = {}

  try {
    // Validate hours according to schema before sending to backend
    if (heureDepart.value) {
      hourSchema.parse(heureDepart.value)
    }
    if (heureArrivee.value) {
      hourSchema.parse(heureArrivee.value)
    }
    // Send data to backend
    console.log('Formulaire valide !', { heureDepart: heureDepart.value, heureArrivee: heureArrivee.value })
  } catch (error) {
    if (error instanceof ZodError) {
      // Global error handling
      errors.value.form = error.issues[0]?.message || 'Format d\'heure invalide'
    }
  }
}

</script>

<template>
  <div class="background"></div>
  <div class="absolute top-0 block h-full w-full overflow-hidden">
    <div class="flex flex-col h-[35%] items-center justify-center">
      <h1 class="text-6xl pb-6 text-white leading-10">Breco</h1>
      <h2 class="m-0 p-0 text-3xl text-white leading-5 text-center font-normal">
        Covoiturage en&nbsp;Bretagne
      </h2>
    </div>
    <div
      id="searchWindow"
      class="absolute flex flex-col justify-center h-[65%] w-full bottom-0 bg-white rounded-t-5xl mx-auto"
    >
      <h3 class="pt-0 mb-4 mx-0 text-primary-dark text-3xl text-center font-medium">Vous recherchez</h3>
      <form @submit="handleSubmit" class="pt-0 px-5 py-3 pb-3 max-w-3xl mx-auto">
        <div id="trajet" class="relative my-0 mx-auto p-0 w-full">
          <div id="Depart" class="flex flex-row w-full pb-7">
            <div class="relative w-full pr-[5vw] sm:pr-6 flex flex-col">
              <label for="villeDepart" class="text-primary-dark text-md font-semibold"
                >Depuis</label
              >
              <input type="text" id="villeDepart" name="villeDepart" autocomplete="off" class="focus:border-action" />
              <ul
                id="listeDepart"
                class="hidden absolute z-2 w-[calc(90%+4px)] shadow-window t-[37px] bg-white pl-0"
              ></ul>
            </div>
            <div class="relative w-20 flex flex-col">
              <label for="heureDepart" class="text-primary-dark text-md font-semibold">À</label>
              <input
                type="text"
                v-model="heureDepart"
                id="heureDepart"
                name="heureDepart"
                autocomplete="off"
                placeholder="00:00"
                :class="{
                  'focus:border-action': !errors.heureDepart,
                  'border-red-500': errors.heureDepart
                }"
                @input="handleTimeInput('heureDepart', $event)"
                @blur="handleBlur('heureDepart')"
              />
              <span v-if="errors.heureDepart" class="text-red-500 text-xs mt-1">
                {{ errors.heureDepart }}
              </span>
            </div>
          </div>
          <div id="Arrivee" class="flex flex-row w-full mb-10">
            <div class="relative w-full pr-[5vw] sm:pr-6 flex flex-col">
              <label for="villeArrivee" class="text-primary-dark text-md font-semibold">Vers</label>
              <input type="text" id="villeArrivee" name="villeArrivee" autocomplete="off"  class="focus:border-action"  />
              <ul
                id="listeArrivee"
                class="hidden absolute z-2 w-[calc(90%+4px)] shadow-window t-[37px] bg-white pl-0"
              ></ul>
            </div>
            <div class="relative w-20 flex flex-col">
              <label for="heureArrivee" class="text-primary-dark text-md font-semibold">À</label>
              <input
                type="text"
                v-model="heureArrivee"
                id="heureArrivee"
                name="heureArrivee"
                autocomplete="off"
                placeholder="00:00"
                :class="{
                  'focus:border-action': !errors.heureArrivee,
                  'border-red-500': errors.heureArrivee
                }"
                @input="handleTimeInput('heureArrivee', $event)"
                @blur="handleBlur('heureArrivee')"
              />
              <span v-if="errors.heureArrivee" class="text-red-500 text-xs mt-1">
                {{ errors.heureArrivee }}
              </span>
            </div>
          </div>
        </div>
        <div id="jours" class="mt-5 flex justify-center gap-1 flex-wrap">
          <input type="checkbox" class="hidden" id="J1" name="J1" /><label
            for="J1"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20 rounded-l-4xl rounded-r-lg font-bold transition-transform duration-100 active:scale-90"
            >Lu</label>
          <input type="checkbox" class="hidden" id="J2" name="J2" /><label
            for="J2"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20 rounded-lg font-bold transition-transform duration-100 active:scale-90"
            >Ma</label>
          <input type="checkbox" class="hidden" id="J3" name="J3" /><label
            for="J3"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20   rounded-lg font-bold transition-transform duration-100 active:scale-90"
            >Me</label>
          <input type="checkbox" class="hidden" id="J4" name="J4" /><label
            for="J4"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20 rounded-lg font-bold transition-transform duration-100 active:scale-90"
            >Je</label>
          <input type="checkbox" class="hidden" id="J5" name="J5" /><label
            for="J5"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20 rounded-lg font-bold transition-transform duration-100 active:scale-90"
            >Ve</label>
          <input type="checkbox" class="hidden" id="J6" name="J6" /><label
            for="J6"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20 rounded-lg font-bold transition-transform duration-100 active:scale-90"
            >Sa</label>
          <input type="checkbox" class="hidden" id="J7" name="J7" /><label
            for="J7"
            class="px-5 py-3 cursor-pointer text-primary-dark bg-primary-lightest20 rounded-l-lg rounded-4xl font-bold transition-transform duration-100 active:scale-90"
            >Di</label>
        </div>
        <div v-if="errors.form" class="text-error text-center mb-4">
          {{ errors.form }}
        </div>
        <button type="submit" class="btn-action mt-12 mb-4">Rechercher</button>
      </form>
    </div>
  </div>
</template>

<style scoped>
#listeDepart li,
#listeArrivee li {
  padding: 10px;
  list-style: none;
  font-size: var(--fontS);
}
#listeDepart li:hover,
#listeArrivee li:hover {
  background-color: var(--dark-white);
  color: var(--green);
  cursor: pointer;
}

#jours input[type='checkbox']:checked + label  {
  background-color: var(--color-primary-lightest);
  border-color: var(--color-primary-lightest);
  color: black;
}
#jours input[type='checkbox']:hover + label  {
  background-color: var(--color-primary-lightest40);
  color: var(--text-primary-dark);
}

#jours input[type='checkbox']:checked:hover + label  {
  background-color: var(--color-primary-lightest85);
  color: var(--text-primary-dark);
}
</style>
