<script setup>
import { useRouter } from 'vue-router';
import dayjs from '@/lib/dayjs';
import HabitDaysStartGap from '@/components/habits/HabitDaysStartGap.vue';
import HabitDay from '@/components/habits/HabitDay.vue';

const props = defineProps({
  summary: {
    type: Array,
    default: () => [],
    required: true
  },
  datesFromYearStart: {
    type: Array,
    default: () => [],
    required: true
  }
});

const router = useRouter();

const handleNavigate = (date) => {
  router.push({
    name: 'Day', 
    params: {
      date: dayjs.utc(date).format('YYYY-MM-DD')
    }
  });
};

const isCurrentDay = (date) => {
  const today = dayjs.utc().startOf('day');
  return dayjs.utc(date).isSame(today, 'day');
};

const findDayInSummary = (date) => {
  return props.summary.find(day => {
    return dayjs.utc(date).isSame(day.date, 'day');
  });
};

const getAmount = (date) => {
  const dayWithHabits = findDayInSummary(date);
  return dayWithHabits ? dayWithHabits.total : 0;
};

const getCompleted = (date) => {
  const dayWithHabits = findDayInSummary(date);
  return dayWithHabits ? dayWithHabits.completed : 0;
};
</script>

<template>
  <div>
    <HabitDaysStartGap />    
    <HabitDay
      v-for="(date, index) in datesFromYearStart"
      :key="index"
      :is-current-day="isCurrentDay(date)"
      :amount-of-habits="getAmount(date)"
      :amount-completed="getCompleted(date)"
      @click="handleNavigate(date)"
    />
  </div>
</template>

<style scoped>
div {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  justify-items: center;
  overflow: auto;
}
</style>