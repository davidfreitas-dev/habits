<template>
  <ion-modal
    :is-open="isOpen"
    :can-dismiss="true"
    :initial-breakpoint="0.35"
    :breakpoints="[0.35]"
    backdrop-dismiss="false"
  >
    <ion-content class="ion-padding-top">
      <ion-text color="light" class="ion-text-center">
        <p><b>{{ message }}</b></p>
      </ion-text>
      
      <div class="ion-padding">
        <Button class="ion-margin-bottom" @click="confirmAction">
          Confirmar
        </Button>
        <Button :outline="true" @click="closeModal">
          Cancelar
        </Button>
      </div>
    </ion-content>
  </ion-modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { IonModal, IonHeader, IonContent, IonTitle, IonGrid, IonRow, IonCol, IonButton } from '@ionic/vue';
import Button from '@/components/Button.vue';

const props = defineProps({
  message: {
    type: String,
    default: ''
  },
});

const isOpen = ref(false);

const setOpen = (status) => {
  isOpen.value = status;
};

defineExpose({setOpen});

const closeModal = () => {
  isOpen.value = false;
};

const emit = defineEmits(['onConfirm']);

const confirmAction = () => {
  emit('onConfirm');
  closeModal();
};

const canDismiss = (data, role) => {
  return role !== 'gesture' && role !== 'backdrop';
};
</script>

<style scoped>
ion-content {
  --background: var(--bg-accent)
}
</style>