<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar class="ion-safe-area-top">
        <BackButton />
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <Loading v-if="isLoading" />
      
      <div
        v-else
        id="container"
      >        
        <Breadcrumb
          :week-day="dayOfWeek"
          :date="dayAndMonth"
        />

        <ProgressBar :progress="progressPercentage" />

        <Checkbox
          v-for="habit in dayInfo.possibleHabits"
          :key="habit.id"
          :label="habit.title"
          :is-checked="isHabitChecked(habit.id)"
          :is-disabled="isDateInPast"
          @handle-checkbox-change="handleToggleHabit(habit.id)"
        />

        <div
          v-if="isDateInPast"
          class="message"
        >
          Você não pode editar hábitos de uma data passada.
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonContent, onIonViewWillEnter } from '@ionic/vue';
import axios from '@/api/axios';
import BackButton from '@/components/BackButton.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
import ProgressBar from '@/components/ProgressBar.vue';
import Checkbox from '@/components/Checkbox.vue';
import Loading from '@/components/Loading.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

const route = useRoute();
const date = ref(route.params.date);
const parsedDate = ref(dayjs(date.value).startOf('day'));
const weekDay = ref(parsedDate.value.day());
const dayOfWeek = ref(parsedDate.value.format('dddd'));
const dayAndMonth = ref(parsedDate.value.format('DD/MM'));
const isDateInPast = ref(dayjs(date.value).endOf('day').isBefore(new Date));

const dayInfo = ref({
  possibleHabits: [],
  completedHabits: []
});

const isLoading = ref(true);

const getDayInfo = async () => {
  const date = parsedDate.value.toDate();

  const response = await axios.post('/habits/day', {
    userId: 1,
    date: date
  });
  
  if (response.status === 'success') {
    dayInfo.value = response.data;
  } else {
    console.log(response.data);
    // Exibir toast com a mensagem de erro
  }

  isLoading.value = false;
};

onIonViewWillEnter(async () => {
  await getDayInfo();
});

const handleToggleHabit = async (habitid) => {
  const response = await axios.put(`/habits/${habitid}/toggle`, { userId: 1 });
  
  if (response.status === 'error') {
    // Exibir toast com a mensagem de erro
    console.log(response.data);
    return;
  }

  await getDayInfo();
};

const isHabitChecked = (habitid) => {
  return dayInfo.value.completedHabits.some(habit => habit.habit_id === habitid);
};

const progressPercentage = computed(() => {
  return dayInfo.value.completedHabits.length
    ? Math.round((dayInfo.value.completedHabits.length / dayInfo.value.possibleHabits.length) * 100)
    : 0;
});
</script>

<style>
div.message {
  font-size: .85rem;
  text-align: center;
  color: var(--font);
  margin-top: 2.5rem;
}
</style>