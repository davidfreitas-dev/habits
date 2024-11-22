<template>
  <ion-modal
    :is-open="isOpen"
    :can-dismiss="canDismiss"
    :presenting-element="presentingElement"
  >
    <ion-header class="ion-no-border">
      <ion-toolbar class="ion-padding-top">
        <ion-icon 
          slot="start"
          :icon="arrowBack" 
          @click="setOpen(false)" 
        />
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <div id="container">
        <h1>Modal Title</h1>
      </div>
    </ion-content>
  </ion-modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { IonButton, IonModal, IonHeader, IonToolbar, IonContent, IonTitle, IonRow, IonCol, IonButtons, IonIcon } from '@ionic/vue';
import { arrowBack } from 'ionicons/icons';

const props = defineProps({
  presentingElement: { 
    type: Object, 
    default: () => {} 
  }
});

const canDismiss = (data, role) => {
  return role !== 'gesture' && role !== 'backdrop';
};

const selectedItem = ref({});

const isOpen = ref(false);

const setOpen = (status) => {
  isOpen.value = status;
};

defineExpose({setOpen});
</script>

<style scoped>
h1 {
  color: var(--font);
  font-weight: 800;
  font-size: 1.875rem;
  margin: 0;
}
ion-icon {
  font-size: 2rem;
  color: var(--secondary);
  padding: 0 1rem;
}
</style>