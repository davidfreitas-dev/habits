<script setup>
import { onMounted } from 'vue';
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { Capacitor } from '@capacitor/core';

onMounted(() => {
  if (Capacitor.isNativePlatform()) {
    window.screen.orientation.lock('portrait');
  }  
});
</script>

<template>
  <ion-app>
    <ion-router-outlet />
  </ion-app>
</template>
