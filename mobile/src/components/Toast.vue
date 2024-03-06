<script setup>
import { ref } from 'vue';
import { IonToast } from '@ionic/vue';
import { alertCircleOutline } from 'ionicons/icons';

const isOpenRef = ref(false);
const color = ref('');
const message = ref(false);

const setOpen = (state, type, msg) => {
  isOpenRef.value = state;
  message.value = msg;
  color.value = type === 'error' ? 'danger' : 'success';
};

defineExpose({setOpen});
</script>

<template>
  <ion-toast
    :icon="alertCircleOutline"
    :is-open="isOpenRef"
    :message="message"
    :color="color"
    :duration="2000"
    @did-dismiss="setOpen(false)"
  />
</template>

