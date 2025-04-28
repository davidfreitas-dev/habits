<script setup>
import { ref } from 'vue';
import { IonToast } from '@ionic/vue';
import { alertCircleOutline } from 'ionicons/icons';

const props = defineProps({
  toastData: {
    type: Object,
    default: () => ({
      color: '',
      message: ''
    })
  }
});

const isOpenRef = ref(false);

const showToast = () => {
  isOpenRef.value = true;
};

const hideToast = () => {
  isOpenRef.value = false;
};

defineExpose({ showToast });
</script>

<template>
  <ion-toast
    :icon="alertCircleOutline"
    :is-open="isOpenRef"
    :message="props.toastData.message"
    :color="props.toastData.color"
    :duration="2500"
    @did-dismiss="hideToast"
  />
</template>

