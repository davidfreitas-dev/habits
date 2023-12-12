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
    default: null 
  }
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => (
  emit('update:modelValue', event.target.value)
);
</script>

<template>
  <div>
    <ion-input
      @input="updateValue"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      autocapitalize="on"
      autocorrect="on"
      mode="ios"
    />
  </div>
</template>

<style scoped>
div {
  display: flex;
  justify-content: center;
  align-items: center;
  
  height: 3rem;
  padding: .5rem 1rem;
  border-radius: 0.375rem;
  border: 2px solid var(--border);
  background: var(--bg-accent);
}

div ion-input {
  --color: var(--font);
  --background: transparent;
  --placeholder-color: var(--placeholder);
  --placeholder-opacity: .8;
}
</style>