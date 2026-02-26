<script setup>
import { IonToggle } from '@ionic/vue';

defineProps({
  checked: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:checked']);

const onToggleChange = (event) => {
  emit('update:checked', event.detail.checked);
};
</script>

<template>
  <ion-toggle
    slot="end"
    :checked="checked"
    @ion-change="onToggleChange"
  />
</template>

<style scoped>
ion-toggle {
    --track-background: var(--color-background-elevated);
    --track-background-checked: var(--color-background-elevated);
    --handle-background-checked: var(--color-success);
}
</style>
