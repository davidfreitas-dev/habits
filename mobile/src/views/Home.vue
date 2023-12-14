<template>
  <ion-page>
    <Header />
    <ion-content :fullscreen="true">
      <div id="container">
        <Loading v-if="isLoading" />
        <Summary
          v-if="!isLoading"
          :summary="summary"
          :dates-from-year-start="datesFromYearStart"
        />
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { onMounted, ref, computed, } from 'vue';
import { IonPage, IonContent } from '@ionic/vue';
import { FirebaseFirestore } from '@capacitor-firebase/firestore';
import { useGenerateRange } from '@/use/useGenerateRange';
import dayjs from 'dayjs';
import Header from '@/components/Header.vue';
import Loading from '@/components/Loading.vue';
import Summary from '@/components/Summary.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();
const isLoading = ref(true);
const datesFromYearStart = ref([]);
const amountOfDaysToFill = ref(0);
const minimumSummaryDatesSize = ref(18 * 5);
const habitWeekDays = ref([]);
const dayHabits = ref([]);
const days = ref([]);

const addHabitWeekDaysSnapshotListener = async () => {
  const callbackId = await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'habit_week_days',
    },
    (event, error) => {
      if (error) {
        console.error(error);
      } else {
        habitWeekDays.value = event.snapshots;
      }
    }
  );
  return callbackId;
};

const addDayHabitsSnapshotListener = async () => {
  const callbackId = await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'day_habits',
    },
    (event, error) => {
      if (error) {
        console.error(error);
      } else {
        dayHabits.value = event.snapshots;
      }
    }
  );
  return callbackId;
};

const addDaysSnapshotListener = async () => {
  const callbackId = await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'days',
    },
    (event, error) => {
      if (error) {
        console.error(error);
      } else {
        days.value = event.snapshots;
      }
    }
  );
  return callbackId;
};

const summary = computed(() => {
  return days.value.map(day => {
    const completed = dayHabits.value.filter(dh => dh.data.day_id === day.id).length;
    const amount = habitWeekDays.value.filter(hwd => hwd.data.week_day === dayjs(day.data.date).day()).length;
    return {
      id: day.id,
      amount: amount,
      completed: completed,
      date: day.data.date,
    };
  }) || [];
});

const addListeners = async () => {
  await addHabitWeekDaysSnapshotListener();
  await addDayHabitsSnapshotListener();
  await addDaysSnapshotListener();
};

onMounted(async () => {
  isLoading.value = true;
  datesFromYearStart.value = generateDatesFromYearBeginning();
  amountOfDaysToFill.value = minimumSummaryDatesSize.value - datesFromYearStart.value.length;
  await addListeners();
  isLoading.value = false;
});
</script>

<style scoped>
div#container {
  margin-bottom: 2rem;
}
</style>
