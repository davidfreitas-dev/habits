<script setup>
import { computed } from 'vue';
import { useDayDimensions } from '@/use/useDayDimensions';

const props = defineProps({
  isCurrentDay: Boolean,
  amountOfHabits: {
    type: Number,
    default: 0,
    required: true
  },
  amountCompleted: {
    type: Number,
    default: 0,
    required: true
  }
});

const { daySize } = useDayDimensions();

const amountAccomplishedPercentage = computed(() => {
  return props.amountCompleted
    ? Math.round((props.amountCompleted / props.amountOfHabits) * 100)
    : 0;
});

const dayStyle = computed(() => {
  if (amountAccomplishedPercentage.value === 0)
    return {
      'background': '#18181b',
      'border-color': '#27272a',
    };

  if (amountAccomplishedPercentage.value > 0 && amountAccomplishedPercentage.value < 20)
    return {
      'background': '#4c1d95',
      'border-color': '#6d28d9',
    };

  if (amountAccomplishedPercentage.value >= 20 && amountAccomplishedPercentage.value < 40)
    return {
      'background': '#5b21b6',
      'border-color': '#7c3aed',
    };

  if (amountAccomplishedPercentage.value >= 40 && amountAccomplishedPercentage.value < 60)
    return {
      'background': '#6d28d9',
      'border-color': '#8b5cf6',
    };

  if (amountAccomplishedPercentage.value >= 60 && amountAccomplishedPercentage.value < 80)
    return {
      'background': '#7c3aed',
      'border-color': '#8b5cf6',
    };
    
  if (amountAccomplishedPercentage.value >= 80)
    return {
      'background': '#8b5cf6',
      'border-color': '#a78bfa',
    };

  return '';
});
</script>

<template>
  <div
    :style="[ daySize, dayStyle ]"
    :class="{ active: isCurrentDay }"
  />
</template>

<style scoped>
div {
  margin: .25rem;
  background: var(--color-background-secondary);
  border-width: 2px;
  border-style: solid;
  border-radius: .5rem;
}
div.active {
  border-width: 4px !important;
  border-color: #fff !important;
}
</style>
