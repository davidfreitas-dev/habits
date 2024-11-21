<template>
  <ion-header class="ion-no-border">
    <ion-toolbar class="ion-safe-area-top">
      <ion-row class="ion-justify-content-between ion-align-items-center">
        <Logo />
        <ion-button router-link="/habit">
          <ion-icon :icon="add" />          
          <span>Novo</span>
        </ion-button>
      </ion-row>
    </ion-toolbar>
    <WeekDays />
  </ion-header>
</template>

<script setup>
import { IonHeader, IonToolbar, IonRow, IonButton, IonIcon } from '@ionic/vue';
import { add } from 'ionicons/icons';
import Logo from '@/components/Logo.vue';
import WeekDays from '@/components/WeekDays.vue';
</script>

<style scoped>
ion-button {
  height: 2.75rem;
  padding-left: 1rem;
  padding-right: 1rem;
  
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