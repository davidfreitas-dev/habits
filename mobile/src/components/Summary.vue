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

<script setup>
import { useRouter } from 'vue-router';
import dayjs from 'dayjs';
import HabitDaysStartGap from '@/components/HabitDaysStartGap.vue';
import HabitDay from '@/components/HabitDay.vue';

const props = defineProps({
  summary: {
    type: Object,
    default: () => {},
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
      date: date.toISOString()
    }
  });
};

const isCurrentDay = (date) => {
  const today = dayjs().startOf('day').toDate();
  const isCurrentDay = dayjs(date).isSame(today);
  return isCurrentDay;
};

const getAmount = (date) => {
  const dayWithHabits = props.summary.find(day => {
    return dayjs(date).isSame(day.date, 'day');
  });
  
  return dayWithHabits ? dayWithHabits.amount : 0;
};

const getCompleted = (date) => {
  const dayWithHabits = props.summary.find(day => {
    return dayjs(date).isSame(day.date, 'day');
  });
  
  return dayWithHabits ? dayWithHabits.completed : 0;
};
</script>

<style scoped>
div {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  justify-items: center;
  overflow: auto;
}
</style>