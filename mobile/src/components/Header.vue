<template>
  <ion-header class="ion-no-border">
    <ion-toolbar class="ion-safe-area-top">
      <slot />
    </ion-toolbar>
  </ion-header>
</template>

<script setup>
import { IonHeader, IonToolbar } from '@ionic/vue';
</script>
