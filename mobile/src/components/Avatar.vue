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
      <ion-avatar class="ion-margin-end">
        <img v-if="image" :src="image">
        <img v-else src="../assets/avatar.png">
      </ion-avatar>
      <div>
        <h5>Olá, {{ firstName || 'Visitante' }}!</h5>
        <span>{{ currentDate }}</span>
      </div>
    </ion-row>
  </router-link>
</template>

<style scoped>
a {
  text-decoration: none;
}

div h5 {
  color: var(--color-text-primary);
  font-weight: 800;
  line-height: 1.75rem;
  max-width: 200px;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

div span {
  color: var(--color-text-secondary);
  font-size: .9rem;
}

ion-avatar {
  width: 48px;
  height: 48px;
}
</style>
