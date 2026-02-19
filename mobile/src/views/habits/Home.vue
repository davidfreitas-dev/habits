<script setup>
import { ref, computed } from 'vue';
import { IonPage, IonContent, IonRow, onIonViewWillEnter, IonText } from '@ionic/vue';
import { useGenerateRange } from '@/composables/useGenerateRange';
import { useProfileStore } from '@/stores/profile';
import { useHabitStore } from '@/stores/habits';
import { useLoading } from '@/composables/useLoading';
import { useToast } from '@/composables/useToast';
import Header from '@/components/layout/Header.vue';
import Avatar from '@/components/layout/Avatar.vue';
import ButtonNew from '@/components/habits/ButtonNew.vue';
import WeekDays from '@/components/habits/WeekDays.vue';
import Container from '@/components/layout/Container.vue';
import Summary from '@/components/habits/Summary.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();
const { withLoading } = useLoading();

const profileStore = useProfileStore();
const habitStore = useHabitStore();

const user = computed(() => {
  return profileStore.user;
});

const summary = ref([]);
const { showToast } = useToast();

const getSummary = async () => {
  try {
    const response = await habitStore.getHabitsSummary();
    summary.value = Array.isArray(response) ? response : [];
  } catch (err) {
    console.error('Error fetching habits summary:', err);
    showToast('error', err.response?.data?.message || 'Erro ao carregar resumo de hábitos.');
    throw err;
  }
};

const amountOfDaysToFill = ref(0);
const datesFromYearStart = ref([]);
const minimumSummaryDatesSize = ref(18 * 5);

onIonViewWillEnter(() => {
  datesFromYearStart.value = generateDatesFromYearBeginning();
  amountOfDaysToFill.value = minimumSummaryDatesSize.value - datesFromYearStart.value.length;
  withLoading(getSummary, 'Erro ao carregar o resumo de hábitos.');
});
</script>

<template>
  <ion-page>
    <Header>
      <ion-row class="ion-justify-content-between ion-align-items-center ion-padding">
        <Avatar :name="user?.name || 'Convidado'" />
        <ButtonNew />
      </ion-row>
      <WeekDays />
    </Header>
    <ion-content :fullscreen="true">
      <Container class="ion-margin-bottom">
        <Summary
          v-if="summary.length"
          :dates-from-year-start="datesFromYearStart"
          :summary="summary"
        />
        <ion-text
          v-if="!summary.length"
          color="medium"
          class="ion-text-center ion-padding"
        >
          Nenhum dado encontrado.
        </ion-text>
      </Container>
    </ion-content>
  </ion-page>
</template>

<style scoped>
/* Scoped styles */
</style>
