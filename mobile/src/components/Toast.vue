<script setup>
import { ref } from 'vue';
import { IonToast } from '@ionic/vue';

const isOpenRef = ref(false);
const toastColor = ref('');
const message = ref(false);

const setOpen = (state, color, msg) => {
  isOpenRef.value = state;
  toastColor.value = color;
  message.value = msg;
};

defineExpose({setOpen});
</script>

<template>
  <ion-toast
    :is-open="isOpenRef"
    :color="toastColor"
    :message="message"
    :duration="2000"
    @did-dismiss="setOpen(false)"
  />
</template>

