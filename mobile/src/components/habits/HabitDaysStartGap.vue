<script setup>
import { ref } from 'vue';
import dayjs from 'dayjs';
import { useDayDimensions } from '@/composables/useDayDimensions';

const { daySize } = useDayDimensions();

const startWeekdayFromYear = ref(dayjs().startOf('year').day());
</script>

<template>
  <template
    v-for="(n, index) in startWeekdayFromYear"
    :key="index"
  >
    <div :style="[ daySize ]" />
  </template>
</template>

<style scoped>
div {
  margin: .25rem;
}
</style>
