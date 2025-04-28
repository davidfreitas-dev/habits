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
        <Loading v-if="isLoading" />
        <Summary
          v-if="!isLoading"
          :dates-from-year-start="datesFromYearStart"
          :summary="summary"
        />
      </Container>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { jwtDecode } from 'jwt-decode';
import { IonPage, IonContent, IonRow, onIonViewWillEnter } from '@ionic/vue';
import { useGenerateRange } from '@/use/useGenerateRange';
import { useSessionStore } from '@/stores/session';
import { useToast } from '@/use/useToast';

import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Avatar from '@/components/Avatar.vue';
import ButtonNew from '@/components/ButtonNew.vue';
import WeekDays from '@/components/WeekDays.vue';
import Container from '@/components/Container.vue';
import Loading from '@/components/Loading.vue';
import Summary from '@/components/Summary.vue';

const { generateDatesFromYearBeginning } = useGenerateRange();
const amountOfDaysToFill = ref(0);
const datesFromYearStart = ref([]);
const minimumSummaryDatesSize = ref(18 * 5);
const storeSession = useSessionStore();

onIonViewWillEnter(() => {
  datesFromYearStart.value = generateDatesFromYearBeginning();
  amountOfDaysToFill.value = minimumSummaryDatesSize.value - datesFromYearStart.value.length;
  getSummary();
});

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

const isLoading = ref(true);
const summary = ref([]);

const { showToast } = useToast();

const getSummary = async () => {
  try {
    const response = await axios.post('/habits/summary', { 
      userId: user.value.id 
    });
    
    summary.value = Array.isArray(response.data) ? response.data : [];
  } catch (err) {
    const error = err.response.data;
    showToast('error', error.message);
  }
  
  isLoading.value = false;
};
</script>
