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
import { onMounted, ref } from 'vue';
import { IonPage, IonContent } from '@ionic/vue';
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

onMounted(() => {
  datesFromYearStart.value = generateDatesFromYearBeginning();
  amountOfDaysToFill.value = minimumSummaryDatesSize.value - datesFromYearStart.value.length;
  getSummary();
});

const isLoading = ref(true);
const toast = ref(undefined);
const summary = ref([]);

const getSummary = async () => {
  const response = await axios.post('/habits/summary', { userId: 1 });
  
  if (response.status === 'success') {
    summary.value = response.data;
  } else {
    console.log(response.data);
    // Exibir toast com a mensagem de erro
  }

  isLoading.value = false;
};
</script>

<style scoped>
div#container {
  margin-bottom: 2rem;
}
</style>
