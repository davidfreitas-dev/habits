<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar class="ion-safe-area-top">
        <BackButton />
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">      
      <div id="container">
        <h1>Criar hábito</h1>
        
        <p>Qual seu comprometimento?</p>
        <Input
          v-model="title"
          placeholder="Exercícios, dormir bem, etc..."
        /> 

        <p>Qual a recorrência?</p>
        <Checkbox
          v-for="(weekDay, index) in availableWeekDays"
          :key="index"
          :label="weekDay"
          :is-checked="isDayChecked(index)"
          @handle-checkbox-change="handleToggleWeekDay(index)"
        />

        <Button class="ion-margin-top ion-padding-top" @click="handleCreateHabit">
          <ion-icon :icon="checkmark" />    
          Confirmar
        </Button>
        
        <Alert ref="alertRef" />
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonContent, IonIcon } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import { useSessionStore } from '@/stores/session';
import axios from '@/api/axios';
import BackButton from '@/components/BackButton.vue';
import Input from '@/components/Input.vue';
import Checkbox from '@/components/Checkbox.vue';
import Button from '@/components/Button.vue';
import Alert from '@/components/Alert.vue';

const toastRef = ref(undefined);
const title = ref('');
const weekDays = ref([]);
const availableWeekDays = ref([
  'Domingo', 
  'Segunda-feira', 
  'Terça-feira', 
  'Quarta-feira', 
  'Quinta-feira', 
  'Sexta-feira', 
  'Sábado'
]);

const alertRef = ref(null);

const showAlert = (header, message) => {
  alertRef.value?.setOpen(header, message);
};

const handleToggleWeekDay = (weekDayIndex) => {
  if (weekDays.value.includes(weekDayIndex)) {
    const index = weekDays.value.indexOf(weekDayIndex);
    weekDays.value.splice(index, 1);
  } else {
    weekDays.value.push(weekDayIndex);
  }
};

const storeSession = useSessionStore();

const user = computed(() => {
  return storeSession.session;
});

const handleCreateHabit = async () => {
  if (!title.value || !weekDays.value.length) {
    showAlert('Ops', 'Informe um título para o hábito e selecione os dias que quer associar');
    return;
  }
  
  const response = await axios.post('/habits/create', { 
    title: title.value,
    weekDays: weekDays.value.join(','),
    userId: user.value.id 
  });

  if (response.status === 'success') {
    title.value = '';
    weekDays.value = [];
    showAlert('Novo Hábito', response.data);
    return;
  }
  
  toastRef.value?.setOpen(true, response.status, response.data);
};

const isDayChecked = (index) => {
  return weekDays.value.includes(index);
};
</script>

<style scoped>
h1 {
  color: var(--font);
  font-weight: 800;
  font-size: 1.875rem;
  margin: 0;
}

p {
  color: var(--font);
  font-weight: 700;
  font-size: 1;
  margin-top: 1.5rem;
}
</style>