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
import { ref } from 'vue';
import { IonPage, IonContent, onIonViewWillEnter } from '@ionic/vue';
import { FirebaseFirestore } from '@capacitor-firebase/firestore';
import { useGenerateRange } from '@/use/useGenerateRange';
import dayjs from 'dayjs';
import Header from '@/components/Header.vue';
import Loading from '@/components/Loading.vue';
import Summary from '@/components/Summary.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();
const datesFromYearStart = ref([]);
const amountOfDaysToFill = ref(0);
const minimumSummaryDatesSize = ref(18 * 5);
const isLoading = ref(true);
const summary = ref(undefined);
const habitWeekDays = ref([]);
const dayHabits = ref([]);
const days = ref([]);

const getCollection = async (reference) => {
  const { snapshots } = await FirebaseFirestore.getCollection({
    reference: reference
  });

  return snapshots;
};

const getSummary = async () => {
  const summary = days.value.map(day => {
    const completed = dayHabits.value.filter(dh => dh.data.day_id === day.id).length;
    const amount = habitWeekDays.value.filter(hwd => hwd.data.week_day === dayjs(day.data.date).day()).length;
    return {
      id: day.id,
      amount: amount,
      completed: completed,
      date: day.data.date,
    };
  });

  return summary;
};

onIonViewWillEnter(async () => {
  isLoading.value = true;
  datesFromYearStart.value = generateDatesFromYearBeginning();
  amountOfDaysToFill.value = minimumSummaryDatesSize.value - datesFromYearStart.value.length;
  habitWeekDays.value = await getCollection('habit_week_days');
  dayHabits.value = await getCollection('day_habits');
  days.value = await getCollection('days');
  summary.value = await getSummary();
  isLoading.value = false;
});
</script>

<style scoped>
div#container {
  margin-bottom: 2rem;
}
</style>
