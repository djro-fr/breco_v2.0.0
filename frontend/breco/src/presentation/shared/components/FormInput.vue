<script setup lang="ts">
const props = defineProps<{
  modelValue: string | number
  type?: string
  placeholder?: string
  label?: string
  ariaLabel?: string
  disabled?: boolean
  required?: boolean
  hasError?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  'blur': [value: string | number]
}>()

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement

  let value = target.value

  // Phone number formatting
  if (props.type === 'tel') {
    // Remove non-digit characters
    value = value.replace(/\D/g, '')

    if (value.length > 10) {
      value = value.slice(0, 10)
    }

    // Format in pairs
    const formatted = value.match(/.{1,2}/g)?.join(' ') || value
    target.value = formatted
    value = formatted
  }

  emit('update:modelValue', target.value)
}

const handleBlur = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('blur', target.value)
}
</script>

<template>
  <div class="relative">
    <input
      :value="modelValue"
      :type="type || 'text'"
      :placeholder="placeholder"
      :aria-label="ariaLabel || placeholder"
      :disabled="disabled"
      :required="required"
      @input="handleInput"
      @blur="handleBlur"
      :class="{
        'border-error': hasError,
        'border-primary-light mb-5 focus:border-action': !hasError,
        'mt-5': label && modelValue
      }"
    />
    <label
      v-if="label && modelValue"
      class="absolute left-0 -top-1 text-md text-primary-dark font-medium transition-opacity duration-200"
    >
      {{ label }}<span v-if="required" class="text-error"> *</span>
    </label>
  </div>
</template>
