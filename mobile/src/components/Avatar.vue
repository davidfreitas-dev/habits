<script setup>
import { computed } from 'vue';
import { IonRow, IonAvatar, IonTitle, IonText } from '@ionic/vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

const props = defineProps({
  name: {
    type: String,
    default: ''
  },
  image: {
    type: String,
    default: ''
  },
});

const firstName = computed(() => props.name.split(' ')[0]);

const currentDate = dayjs().format('dddd, D MMM');
</script>

<template>
  <router-link to="/settings">
    <ion-row class="ion-justify-content-start ion-align-items-center">
      <ion-avatar v-if="image">
        <img alt="Silhouette of a person's head" :src="image">
      </ion-avatar>
      <div>
        <h3>Olá, {{ firstName || 'Visitante' }}!</h3>
        <ion-text color="medium">
          {{ currentDate }}
        </ion-text>
      </div>
    </ion-row>
  </router-link>
</template>

<style scoped>
a {
  text-decoration: none;
}

ion-avatar {
  margin-right: .75rem;
}

div h3 {
  color: var(--font);
  font-weight: 800;
  font-size: 1.25rem;
  line-height: 1.75rem;
  margin: 0;
}
</style>
