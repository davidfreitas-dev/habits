<template>
  <ion-page>
    <Header />
    <ion-content :fullscreen="true">
      <div id="container">
        <Loading v-if="isLoading" />
        <Summary
          v-if="!isLoading"
          :dates-from-year-start="datesFromYearStart"
          :summary="summary"
        />
      </div>
      <Toast ref="toast" />
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
import Toast from '@/components/Toast.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();
const toast = ref(null);
const loadingCount = ref(0);

const habitWeekDays = ref([]);
const addHabitWeekDaysSnapshotListener = async () => {
  const callbackId = await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'habit_week_days',
    },
    (event, error) => {
      loadingCount.value++;

      if (error) {
        toast.value?.setOpen(true, 'danger', error);
      } else {
        habitWeekDays.value = event.snapshots;
      }
    }
  );
  return callbackId;
};

const dayHabits = ref([]);
const addDayHabitsSnapshotListener = async () => {
  const callbackId = await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'day_habits',
    },
    (event, error) => {
      loadingCount.value++;

      if (error) {
        toast.value?.setOpen(true, 'danger', error);
      } else {
        dayHabits.value = event.snapshots;
      }
    }
  );
  return callbackId;
};

const days = ref([]);
const addDaysSnapshotListener = async () => {
  const callbackId = await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'days',
    },
    (event, error) => {
      loadingCount.value++;

      if (error) {
        toast.value?.setOpen(true, 'danger', error);
      } else {
        days.value = event.snapshots;
      }
    }
  );
  return callbackId;
};

const isLoading = computed(() => {
  return loadingCount.value < 3;
});

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

const datesFromYearStart = ref([]);
const amountOfDaysToFill = ref(0);
const minimumSummaryDatesSize = ref(18 * 5);

const addListeners = async () => {
  await addHabitWeekDaysSnapshotListener();
  await addDayHabitsSnapshotListener();
  await addDaysSnapshotListener();
};

onMounted(async () => {
  datesFromYearStart.value = generateDatesFromYearBeginning();
  amountOfDaysToFill.value = minimumSummaryDatesSize.value - datesFromYearStart.value.length;
  await addListeners();
});
</script>

<style scoped>
div#container {
  margin-bottom: 2rem;
}
</style>
