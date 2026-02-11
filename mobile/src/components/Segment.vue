<template>
  <ion-segment
    mode="md"
    v-model="value"
    @ion-change="onSegmentChange"
  >
    <ion-segment-button 
      v-for="(segment, index) in segments" 
      :key="index" 
      :value="segment.value"
    >
      <ion-label>{{ segment.label }}</ion-label>
    </ion-segment-button>
  </ion-segment>
</template>

<script setup>
import { ref, watch } from 'vue';
import { IonSegment, IonSegmentButton, IonLabel } from '@ionic/vue';

const props = defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  segments: {
    type: Array,
    required: true,
    default: () => [],
  },
});

const emits = defineEmits(['update:modelValue']);

const value = ref(props.modelValue);

const onSegmentChange = (event) => {
  emits('update:modelValue', event.detail.value);
};

watch(() => props.modelValue, (newValue) => {
  value.value = newValue;
});
</script>

<style scoped>
ion-segment {
  position: sticky;
  top: 0;
  z-index: 10;
  padding: 0 .5rem;
  background: var(--bg);
}

ion-segment-button::part(indicator-background) {
  background: var(--success);
}

/* Material Design styles */
ion-segment-button.md::part(native) {
  font-weight: 600;
  color: var(--font);
}

.segment-button-checked.md::part(native) {
  color: var(--success);
}

ion-segment-button.md::part(indicator-background) {
  height: 4px;
}

/* iOS styles */
ion-segment-button.ios::part(native) {
  color: var(--success);
}

.segment-button-checked.ios::part(native) {
  color: #fff;
}

ion-segment-button.ios::part(indicator-background) {
  border-radius: 20px;
}
</style>
