<script setup>
import { ref } from 'vue';
import { IonModal, IonContent } from '@ionic/vue';
import Button from '@/components/Button.vue';

const props = defineProps({
  message: {
    type: String,
    default: ''
  },
});

const modal = ref();

const setOpen = (status) => {
  if (status) {
    modal.value.$el.present();
  } else {
    modal.value.$el.dismiss();
  }
};

defineExpose({ setOpen });

const closeModal = () => {
  modal.value.$el.dismiss();
};

const emit = defineEmits(['on-confirm']);

const confirmAction = () => {
  emit('on-confirm');
  closeModal();
};
</script>

<template>
  <ion-modal
    ref="modal"
    :initial-breakpoint="0.35"
    :breakpoints="[0, 0.35]"
  >
    <ion-content class="ion-padding-top">
      <div class="ion-text-center">
        <p><b>{{ message }}</b></p>
      </div>
      
      <div class="ion-padding">
        <Button class="ion-margin-bottom" @click="confirmAction">
          Confirmar
        </Button>
        <Button color="outline" @click="closeModal">
          Cancelar
        </Button>
      </div>
    </ion-content>
  </ion-modal>
</template>

<style scoped>
ion-content {
  --background: var(--bg-accent)
}
</style>