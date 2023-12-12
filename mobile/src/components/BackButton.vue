<template>
  <ion-icon 
    slot="start"
    :icon="arrowBack" 
    @click="handleBack" 
  />
</template>

<script setup>
import { useRouter } from 'vue-router';
import { IonIcon } from '@ionic/vue';
import { arrowBack } from 'ionicons/icons';

const props = defineProps({
  route: {
    type: String,
    default: ''
  }
});

const router = useRouter();

const handleBack = () => {
  if (!props.route) {
    router.go(-1);
    return;
  }

  router.push(props.route);
};
</script>

<style scoped>
ion-icon {
  font-size: 2rem;
  color: var(--secondary);
  padding: 0 1rem;
}
</style>