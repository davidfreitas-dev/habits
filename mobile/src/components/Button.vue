<script setup>
import { IonButton, IonSpinner } from '@ionic/vue';

const props = defineProps({
  isDisabled: Boolean,
  isLoading: Boolean,
  outline: Boolean,
});
</script>

<template>
  <ion-button :disabled="isLoading || isDisabled" :class="{ outline }">
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
</style>