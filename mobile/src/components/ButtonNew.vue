<template>
  <ion-button router-link="/habit">
    <ion-icon :icon="add" />          
    <span>Novo</span>
  </ion-button>
</template>

<script setup>
import { IonButton, IonIcon } from '@ionic/vue';
import { add } from 'ionicons/icons';
</script>

<style scoped>
ion-button {
  height: 3rem;
  padding-left: 1rem;
  padding-right: 1rem;
  text-transform: unset;
  
  --background: transparent;
  --background-hover: transparent;
  --background-activated: transparent;
  --background-focused: transparent;
  --border-radius: 0.375rem;
  --border-color: #8B5CF6;
  --border-style: solid;
  --border-width: 1px;
}

ion-button ion-icon {
  color: #8B5CF6;
  font-size: 20px;
}

ion-button span {
  color: #fff;
  margin-left: 0.75rem;
  font-weight: 600;
  font-size: 1rem; 
}
</style>