<script setup>
import { ref, computed } from 'vue';
import { IonInput, IonLabel, IonIcon } from '@ionic/vue';
import { eyeOutline, eyeOffOutline } from 'ionicons/icons';

const props = defineProps({
  type: { 
    type: String, 
    default: '' 
  },
  label: { 
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

const updateValue = (event) => {
  emit('update:modelValue', event.detail.value);
};

const isPasswordVisible = ref(false);

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};

const inputType = computed(() => {
  if (props.type === 'password') {
    return isPasswordVisible.value ? 'text' : 'password';
  }
  return props.type;
});
</script>

<template>
  <ion-label>
    {{ label }}
  </ion-label>

  <div class="input-container">
    <ion-input
      mode="ios"
      :type="inputType"
      :value="modelValue"
      :placeholder="placeholder"
      @ion-input="updateValue"
    />

    <ion-icon
      v-if="type === 'password'"
      :key="isPasswordVisible ? 'eye-off' : 'eye'"
      :icon="isPasswordVisible ? eyeOffOutline : eyeOutline"
      @click="togglePasswordVisibility"
    />
  </div>
</template>

<style scoped>
ion-label {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-top: 1.5rem;
  margin-bottom: 1rem;
}

ion-input {  
  border-radius: 0.375rem;
  border: 2px solid var(--color-border-default);
  background: var(--color-background-secondary);
  
  --color: var(--color-text-primary);
  --placeholder-color: var(--placeholder);
  --placeholder-opacity: .8;
  --padding-top: 1rem;
  --padding-bottom: 1rem;
  --padding-start: 1rem;
  --padding-end: 1rem;
}

ion-input:focus-within {
  border: 2px solid var(--color-primary);
}

.input-container {
  position: relative;
}

ion-icon {
  position: absolute;
  top: 50%;
  right: 1rem;
  transform: translateY(-50%);
  font-size: 1.5rem;
  color: var(--color-text-primary);
  cursor: pointer;
  z-index: 10;
}
</style>