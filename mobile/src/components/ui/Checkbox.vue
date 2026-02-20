<template>
  <ion-row class="ion-justify-content-start ion-align-items-center">
    <ion-checkbox
      mode="md"
      :checked="isChecked"
      :disabled="isDisabled"
      @ion-change="$emit('handleCheckboxChange')"
    />
    <ion-label :class="{ disabled: isDisabled }" @click="handleItem">
      {{ label }}
    </ion-label>
  </ion-row>
</template>

<script setup>
import { IonRow, IonCheckbox, IonLabel } from '@ionic/vue';

const props = defineProps({
  isChecked: Boolean,
  isDisabled: Boolean,
  label: {
    type: String,
    default: ''
  },
});

const emit = defineEmits(['handleCheckboxChange', 'handleItem']);

const handleItem = () => {
  emit('handleItem');
};
</script>

<style scoped>
ion-row {
  margin-bottom: .5rem;
}
ion-checkbox {
  --size: 2rem;
  --checkmark-width: 5px;
  --checkbox-background: var(--color-background-secondary);
  --checkbox-background-checked: var(--color-success);
  --border-color-checked: transparent;
}
ion-checkbox::part(container) {
  padding: 6px;
  border-radius: .5rem;
  border-color: transparent;
}
ion-label {
  color: var(--color-text-primary);
  font-size: 1.1rem;
  margin-left: .75rem;
}
ion-label.disabled {
  opacity: 0.5;
}
</style>