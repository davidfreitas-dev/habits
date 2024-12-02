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
        <ion-button class="ion-margin-bottom" @click="confirmAction">
          Confirmar
        </ion-button>
        <ion-button @click="closeModal">
          Cancelar
        </ion-button>
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

ion-button {
  width: 100%;
  height: 2.75rem;
  font-size: 1rem;
  font-weight: 700;
  text-transform: unset;
  letter-spacing: .0225rem;
  padding-left: .5rem;
  padding-right: .5rem;
  
  --color: var(--font);
  --background: var(--success);
  --background-hover: var(--success);
  --background-activated: var(--success);
  --background-focused: var(--success);
  --border-radius: 0.375rem;
}

ion-button:last-child {
  --background: transparent;
  --background-hover: transparent;
  --background-activated: transparent;
  --background-focused: transparent;
  --border-radius: 0.375rem;
  --border-color: #8B5CF6;
  --border-style: solid;
  --border-width: 1px;
}
</style>