<script setup>
import { ref, computed } from 'vue';
import { IonPage, IonContent, IonRow, onIonViewWillEnter, IonText } from '@ionic/vue';
import { useGenerateRange } from '@/use/useGenerateRange';
import { useProfileStore } from '@/stores/profile';
import { useHabitStore } from '@/stores/habits';
import { useLoading } from '@/use/useLoading';
import Header from '@/components/Header.vue';
import Avatar from '@/components/Avatar.vue';
import ButtonNew from '@/components/ButtonNew.vue';
import WeekDays from '@/components/WeekDays.vue';
import Container from '@/components/Container.vue';
import Summary from '@/components/Summary.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();
const { withLoading, isLoading } = useLoading();

const profileStore = useProfileStore();
const habitStore = useHabitStore();

const user = computed(() => {
  return profileStore.user;
});

const summary = ref([]);

const getSummary = async () => {
  const response = await habitStore.getHabitsSummary();
  summary.value = Array.isArray(response) ? response : [];
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
          v-if="!isLoading && summary.length"
          :dates-from-year-start="datesFromYearStart"
          :summary="summary"
        />
        <ion-text
          v-if="!isLoading && !summary.length"
          color="medium"
          class="ion-text-center ion-padding"
        >
          Nenhum dado encontrado.
        </ion-text>
      </Container>
    </ion-content>
  </ion-page>
</template>