<script setup>
import { IonButton, IonSpinner } from '@ionic/vue';

const props = defineProps({
  isLoading: Boolean
});
</script>

<template>
  <ion-button :disabled="isLoading">
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

  --color: var(--font);
  --background: var(--success);
  --background-hover: var(--success);
  --background-activated: var(--success);
  --background-focused: var(--success);
  --border-radius: 0.375rem;
}
</style>