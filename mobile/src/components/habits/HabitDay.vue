<script setup>
import { computed } from 'vue';
import { useDayDimensions } from '@/composables/useDayDimensions';

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

const dayClass = computed(() => {
  const percentage = amountAccomplishedPercentage.value;
  
  if (percentage === 0) return 'habit-day-0';
  if (percentage < 20)  return 'habit-day-20';
  if (percentage < 40)  return 'habit-day-40';
  if (percentage < 60)  return 'habit-day-60';
  if (percentage < 80)  return 'habit-day-80';
  return 'habit-day-100';
});
</script>

<template>
  <div
    :style="daySize"
    :class="[ dayClass, { active: isCurrentDay } ]"
  />
</template>

<style scoped>
div {
  margin: .25rem;
  border-width: 2px;
  border-style: solid;
  border-radius: .5rem;
}
div.active {
  border-width: 4px !important;
  border-color: #fff !important;
}
</style>
