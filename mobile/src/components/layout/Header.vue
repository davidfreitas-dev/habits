<template>
  <ion-header class="ion-no-border ion-safe-area-top">
    <ion-toolbar>
      <slot />
    </ion-toolbar>
  </ion-header>
</template>

<script setup>
import { IonHeader, IonToolbar } from '@ionic/vue';
</script>
