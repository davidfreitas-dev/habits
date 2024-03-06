<template>
  <ion-page>
    <Header />
    <ion-content :fullscreen="true">
      <div id="container">
        <Loading v-if="isLoading" />
        <Summary
          v-if="!isLoading && summary.length"
          :dates-from-year-start="datesFromYearStart"
          :summary="summary"
        />
        <p v-if="!isLoading && !summary.length" class="ion-text-center">
          <ion-text color="light">
            Você ainda não criou nenhum hábito
          </ion-text>
        </p>
      </div>
      <Toast ref="toast" />
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { IonPage, IonContent, IonText, onIonViewWillEnter } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import { useGenerateRange } from '@/use/useGenerateRange';
import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Loading from '@/components/Loading.vue';
import Summary from '@/components/Summary.vue';
import Toast from '@/components/Toast.vue';

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
  return storeSession.session;
});

const isLoading = ref(true);
const toastRef = ref(undefined);
const summary = ref([]);

const getSummary = async () => {
  const response = await axios.post('/habits/summary', { userId: user.value.id });
  
  if (response.status === 'success') {
    summary.value = response.data;
  } else {
    toastRef.value?.setOpen(true, response.status, response.data);
  }

  isLoading.value = false;
};
</script>

<style scoped>
div#container {
  margin-bottom: 2rem;
}
</style>
