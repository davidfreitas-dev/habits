<script setup>
import { onMounted, watch } from 'vue';
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { Capacitor } from '@capacitor/core';
import { useNetwork } from '@/composables/useNetwork';
import { useStatusBar } from '@/composables/useStatusBar';
import { useThemeStore } from '@/stores/theme';

const { isConnected } = useNetwork();
const { setStatusBar, Style } = useStatusBar();
const themeStore = useThemeStore();

const applyTheme = (isDark) => {
  document.body.classList.toggle('dark', isDark);
  setStatusBar(isDark ? Style.Dark : Style.Light);
};

watch(() => themeStore.isDarkMode, (isDark) => {
  applyTheme(isDark);
});

onMounted(() => {
  if (themeStore.isInitialLoad) {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    themeStore.setDarkMode(prefersDark);
  } else {
    applyTheme(themeStore.isDarkMode);
  }

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
