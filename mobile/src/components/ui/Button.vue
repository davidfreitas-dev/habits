<script setup>
import { IonButton, IonSpinner } from '@ionic/vue';

const props = defineProps({
  isDisabled: Boolean,
  isLoading: Boolean,
  color: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'primary', 'danger', 'outline'].includes(value),
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

  --color: var(--color-background-primary);
  --background: var(--color-neutral-100);
  --background-hover: var(--color-neutral-100);
  --background-activated: var(--color-neutral-100);
  --background-focused: var(--color-neutral-100);
  --border-radius: 0.375rem;
}

ion-button.primary {
  --color: var(--color-background-primary);
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --background-activated: var(--color-primary-focus);
  --background-focused: var(--color-primary-focus);
  --border-radius: 0.375rem;
}

ion-button.danger {
  --color: var(--color-text-primary);
  --background: var(--color-danger);
  --background-hover: var(--color-danger-hover);
  --background-activated: var(--color-danger-focus);
  --background-focused: var(--color-danger-focus);
  --border-radius: 0.375rem;
}

ion-button.outline {    
  --color: var(--color-text-primary);
  --background: transparent;
  --background-hover: transparent;
  --background-activated: transparent;
  --background-focused: transparent;
  --border-radius: 0.375rem;
  --border-color: var(--color-primary-accent);
  --border-style: solid;
  --border-width: 1px;
}
</style>