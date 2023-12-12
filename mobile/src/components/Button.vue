<script setup>
import { IonButton, IonIcon } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';

const props = defineProps({
  text: { 
    type: String,
    default: '' 
  },
});
</script>

<template>
  <ion-button :expand="true">
    <ion-icon :icon="checkmark" />
    
    {{ text }}
  </ion-button>
</template>

<style scoped>
ion-button {
  width: 100%;
  height: 3.5rem;
  font-weight: 700;
  margin-top: 1.5rem;
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