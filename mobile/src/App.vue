<script setup>
import { onMounted } from 'vue';
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { Capacitor } from '@capacitor/core';
import { useNetwork } from '@/composables/useNetwork';
import { useStatusBar } from '@/composables/useStatusBar';

const { isConnected } = useNetwork();

const { setStatusBar, Style } = useStatusBar();

onMounted(async () => {
  await setStatusBar(Style.Dark);
  if (Capacitor.isNativePlatform()) {
    window.screen.orientation.lock('portrait');
  }  
});
</script>

<template>
  <ion-app>
    <div v-if="!isConnected" class="offline-banner">
      Você está offline
    </div>
    <ion-router-outlet />
  </ion-app>
</template>

<style scoped>
.offline-banner {
  background: var(--ion-color-danger);
  color: white;
  text-align: center;
  padding: 6px;
  font-size: 0.85rem;
}
</style>
