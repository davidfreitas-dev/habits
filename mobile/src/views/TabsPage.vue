<template>
  <ion-page>
    <ion-tabs>
      <ion-router-outlet />
      <ion-tab-bar slot="bottom" class="ion-safe-area-bottom">
        <ion-tab-button tab="home" href="/tabs/home">
          <ion-icon :icon="homeOutline" />
          <ion-label>Início</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="statistics" href="/tabs/statistics">
          <ion-icon :icon="statsChartOutline" />
          <ion-label>Estatísticas</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="options" href="/tabs/options">
          <ion-icon :icon="personOutline" />
          <ion-label>Opções</ion-label>
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>

<script setup>
import { IonTabBar, IonTabButton, IonTabs, IonLabel, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import { homeOutline, statsChartOutline, personOutline } from 'ionicons/icons';
</script>

<style scoped>
ion-tab-bar {
  --background: var(--color-background-primary);
  --border-color: var(--color-background-secondary);
  height: 65px;
}

ion-tab-button {
  --color: var(--color-text-secondary);
  --color-selected: var(--color-primary);
}

ion-icon {
  font-size: 24px;
}

ion-label {
  font-size: 12px;
  font-weight: 500;
}
</style>
