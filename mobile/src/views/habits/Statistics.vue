<script setup>
import { IonPage, IonContent } from '@ionic/vue';
import Heading from '@/components/layout/Heading.vue';
import Container from '@/components/layout/Container.vue';
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" class="vertical-safe-area">
      <Container>
        <Heading title="Estatísticas" />
        <p class="ion-padding-top">
          Em breve: Acompanhe seu progresso aqui!
        </p>
      </Container>
    </ion-content>
  </ion-page>
</template>

<style scoped>
p {
  color: var(--color-text-secondary);
  text-align: center;
}
</style>
