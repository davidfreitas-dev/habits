<script setup>
import { IonButton, IonSpinner } from '@ionic/vue';

const props = defineProps({
  isDisabled: Boolean,
  isLoading: Boolean,
  color: {
    type: String,
    default: 'main',
    validator: (value) => ['main', 'outline', 'danger'].includes(value),
  },
});
</script>

<template>
  <ion-button :class="color" :disabled="isLoading || isDisabled">
    <ion-spinner v-if="isLoading" name="dots" />
    <slot v-else />
  </ion-button>
</template>

<style scoped>
ion-button {
  width: 100%;
  height: 3.5rem;
  font-size: 1rem;
  font-weight: 700;
  text-transform: unset;
  letter-spacing: .0225rem;

  --color: var(--color-text-primary);
  --background: var(--color-success);
  --background-hover: var(--color-success);
  --background-activated: var(--color-success);
  --background-focused: var(--color-success);
  --border-radius: 0.375rem;
}

ion-button.outline {
  --background: transparent;
  --background-hover: transparent;
  --background-activated: transparent;
  --background-focused: transparent;
  --border-radius: 0.375rem;
  --border-color: #8B5CF6;
  --border-style: solid;
  --border-width: 1px;
}

ion-button.danger {
  --color: var(--color-danger);
  --background: transparent;
  --background-hover: transparent;
  --background-activated: transparent;
  --background-focused: transparent;
  --border-radius: 0.375rem;
  --border-color: var(--color-danger);
  --border-style: solid;
  --border-width: 1px;
}
</style>