<script setup>
import { onMounted } from 'vue';
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { Capacitor } from '@capacitor/core';
import { useToast } from '@/use/useToast';
import Toast from '@/components/Toast.vue';

const { toastRef, toastData } = useToast();

onMounted(() => {
  if (Capacitor.isNativePlatform()) {
    window.screen.orientation.lock('portrait');
  }  
});
</script>

<template>
  <ion-app>
    <ion-router-outlet />
    <Toast ref="toastRef" :toast-data="toastData" />
  </ion-app>
</template>
