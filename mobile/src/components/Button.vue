<script setup>
import { IonButton, IonSpinner } from '@ionic/vue';

const props = defineProps({
  isLoading: Boolean,
  isDisabled: Boolean
});
</script>

<template>
  <ion-button :expand="true" :disabled="isDisabled">
    <ion-spinner v-if="isLoading" name="dots" />
    <slot v-else />
  </ion-button>
</template>

<style scoped>
ion-button {
  width: 100%;
  height: 3rem;
  font-weight: 700;
  --background: var(--success);
  --background-hover: var(--success);
  --background-activated: var(--success);
  --background-focused: var(--success);
  --ripple-color: var(--main);
  --border-radius: 0.375rem;
  --color: var(--font);
}

ion-button ion-icon {
  --ionicon-stroke-width: 64px;
  margin-right: .5rem;
}
</style>