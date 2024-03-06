<script setup>
import { IonButton } from '@ionic/vue';
</script>

<template>
  <ion-button :expand="true">
    <slot />
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