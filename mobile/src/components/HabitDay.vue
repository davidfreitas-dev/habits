<template>
  <div
    :style="[ daySize, dayStyle ]"
    :class="{ active: isCurrentDay }"
  />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

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

const WEEK_DAYS = 7;

const SCREEN_HORIZONTAL_PADDING = (32 * 2) / 5;

const screenDimensions = ref({ width: window.innerWidth, height: window.innerHeight });

const updateScreenDimensions = () => {
  screenDimensions.value = { width: window.innerWidth, height: window.innerHeight };
};

onMounted(() => {
  window.addEventListener('resize', updateScreenDimensions);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateScreenDimensions);
});

const daySize = computed(() => {
  const size = Math.trunc((screenDimensions.value.width / WEEK_DAYS) - SCREEN_HORIZONTAL_PADDING);
  
  return { width: size + 'px', height: size + 'px' };
});

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

<style scoped>
div {
  margin: .25rem;
  background: var(--bg-accent);
  border-width: 2px;
  border-style: solid;
  border-radius: .5rem;
}
div.active {
  border-width: 4px !important;
  border-color: #fff !important;
}
</style>