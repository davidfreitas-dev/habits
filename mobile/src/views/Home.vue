<script setup>
import { ref, computed } from 'vue';
import { jwtDecode } from 'jwt-decode';
import { IonPage, IonContent, IonRow, onIonViewWillEnter, IonText } from '@ionic/vue';
import { useGenerateRange } from '@/use/useGenerateRange';
import { useSessionStore } from '@/stores/session';
import { useLoading } from '@/use/useLoading';

import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Avatar from '@/components/Avatar.vue';
import ButtonNew from '@/components/ButtonNew.vue';
import WeekDays from '@/components/WeekDays.vue';
import Container from '@/components/Container.vue';
import Summary from '@/components/Summary.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();

const { withLoading, isLoading } = useLoading();

const storeSession = useSessionStore();

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

const summary = ref([]);

const getSummary = async () => {
  const response = await axios.post('/habits/summary', {
    userId: user.value.id
  });

  summary.value = Array.isArray(response.data) ? response.data : [];
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
        <Avatar :name="user.name" />
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
