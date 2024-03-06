<script setup>
import { IonInput } from '@ionic/vue';

const props = defineProps({
  type: { 
    type: String, 
    default: '' 
  },
  placeholder: { 
    type: String, 
    default: '' 
  },
  modelValue: { 
    type: [ String, Number ], 
    default: '' 
  }
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => (
  emit('update:modelValue', event.target.value)
);
</script>

<template>
  <ion-input
    mode="ios"
    :type="type"
    :value="modelValue"
    :placeholder="placeholder"
    @input="updateValue"
  />
</template>

<style scoped>
ion-input {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  
  height: 3rem;
  border-radius: 0.375rem;
  border: 2px solid var(--border);
  background: var(--bg-accent);
  
  --color: var(--font);
  --placeholder-color: var(--placeholder);
  --placeholder-opacity: .8;
  --padding-top: 1rem;
  --padding-bottom: 1rem;
  --padding-start: 1rem;
  --padding-end: 1rem;
}

ion-input:focus-within {
  border: 2px solid var(--success);
}
</style>