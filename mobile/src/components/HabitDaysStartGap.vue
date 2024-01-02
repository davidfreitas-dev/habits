<template>
  <template
    v-for="(n, index) in startWeekdayFromYear"
    :key="index"
  >
    <div :style="[ daySize ]" />
  </template>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import dayjs from 'dayjs';

const WEEK_DAYS = 7;

const SCREEN_HORIZONTAL_PADDING = (32 * 2) / 5;

const screenDimensions = ref({ width: window.innerWidth, height: window.innerHeight });

const startWeekdayFromYear = ref(dayjs().startOf('year').day());

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
</script>

<style scoped>
div {
  margin: .25rem;
}
</style>