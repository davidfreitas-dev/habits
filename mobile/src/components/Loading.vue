<template>
  <ion-loading :is-open="true" />
</template>

<script setup>
import { IonLoading } from '@ionic/vue';
</script>

<style scoped>
ion-loading {
  --background: transparent;
  --spinner-color: var(--primary);
}
</style>