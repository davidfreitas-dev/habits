<script setup>
import { onMounted, watch } from 'vue';
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { Capacitor } from '@capacitor/core';
import { useNetwork } from '@/composables/useNetwork';
import { useStatusBar } from '@/composables/useStatusBar';
import { useNotifications } from '@/composables/useNotifications';
import { useThemeStore } from '@/stores/theme';
import { useHabitStore } from '@/stores/habits';
import { NotificationService } from '@/services/NotificationService';

const { isConnected } = useNetwork();
const { setStatusBar, Style } = useStatusBar();
const { requestPermission } = useNotifications();
const themeStore = useThemeStore();
const habitStore = useHabitStore();

const applyTheme = (isDark) => {
  document.body.classList.toggle('dark', isDark);
  setStatusBar(isDark ? Style.Dark : Style.Light);
};

watch(() => themeStore.isDarkMode, (isDark) => {
  applyTheme(isDark);
});

onMounted(async () => {
  if (themeStore.isInitialLoad) {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    themeStore.setDarkMode(prefersDark);
  } else {
    applyTheme(themeStore.isDarkMode);
  }

  if (Capacitor.isNativePlatform()) {
    window.screen.orientation.lock('portrait');
    const granted = await requestPermission();
    if (granted) {
      try {
        const habits = await habitStore.fetchAllHabits();
        await NotificationService.rescheduleAllNotifications(habits);
      } catch (error) {
        console.error('Error rescheduling notifications:', error);
      }
    }
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
