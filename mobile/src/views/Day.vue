<template>
  <ion-page>
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">
      <Loading v-if="isLoading" />
      
      <Container v-else>        
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
          @handle-item="router.push('/habit/' + habit.id)"
          @handle-checkbox-change="handleToggleHabit(habit.id)"
        />

        <ion-text v-if="!dayInfo.possibleHabits.length && !isDateInPast">
          <p>Você ainda não criou nenhum hábito.</p>
        </ion-text>

        <ion-text v-if="isDateInPast">
          <p>Você não pode editar hábitos de uma data passada.</p>
        </ion-text>
      </Container>

      <Toast ref="toastRef" />
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { jwtDecode } from 'jwt-decode';
import { IonPage, IonHeader, IonToolbar, IonContent, IonText, onIonViewWillEnter } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
import ProgressBar from '@/components/ProgressBar.vue';
import Checkbox from '@/components/Checkbox.vue';
import Loading from '@/components/Loading.vue';
import Toast from '@/components/Toast.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

const route = useRoute();
const router = useRouter();
const date = ref(route.params.date);
const parsedDate = ref(dayjs(date.value).startOf('day'));
const dayOfWeek = ref(parsedDate.value.format('dddd'));
const dayAndMonth = ref(parsedDate.value.format('DD/MM'));
const isDateInPast = ref(dayjs(date.value).endOf('day').isBefore(new Date));
const storeSession = useSessionStore();

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

const dayInfo = ref({
  possibleHabits: [],
  completedHabits: []
});

const isLoading = ref(true);
const toastRef = ref(undefined);

const getDayInfo = async () => {
  try {
    const date = parsedDate.value.toDate();

    const response = await axios.post('/habits/day', {
      userId: user.value.id,
      date: date
    });

    dayInfo.value = response.data;
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, 'error', error.message);
  }

  isLoading.value = false;
};

onIonViewWillEnter(() => {
  getDayInfo();
});

const handleToggleHabit = async (habitId) => {
  try {
    await axios.put(`/habits/${habitId}/toggle`, { 
      userId: user.value.id 
    });
    
    await getDayInfo();
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, 'error', error.message);
  }
};

const isHabitChecked = (habitId) => {
  return dayInfo.value.completedHabits.some(habit => habit.habit_id === habitId);
};

const progressPercentage = computed(() => {
  return dayInfo.value.completedHabits.length
    ? Math.round((dayInfo.value.completedHabits.length / dayInfo.value.possibleHabits.length) * 100)
    : 0;
});
</script>

<style>
div#container {
  display: block !important;
}
ion-text {
  font-size: .85rem;
  text-align: center;
  color: var(--font);
  padding: 2.5rem;
}
</style>