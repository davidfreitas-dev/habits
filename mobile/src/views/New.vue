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

        <Button
          text="Confirmar"
          @click="handleCreateHabit"
        />
        
        <Alert ref="alertRef" />
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonContent } from '@ionic/vue';
import { FirebaseFirestore } from '@capacitor-firebase/firestore';
import dayjs from 'dayjs';
import BackButton from '@/components/BackButton.vue';
import Input from '@/components/Input.vue';
import Checkbox from '@/components/Checkbox.vue';
import Button from '@/components/Button.vue';
import Alert from '@/components/Alert.vue';

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

const handleCreateHabitWeekDay = async (habitid, weekday) => {
  await FirebaseFirestore.addDocument({
    reference: 'habit_week_days',
    data: {
      habit_id: habitid,
      week_day: weekday
    }
  });
};

const handleCreateHabit = async () => {
  if (!title.value || !weekDays.value.length) {
    showAlert('Ops', 'Informe um título para o hábito e selecione os dias que quer associar');
    return;
  }

  const today = dayjs().startOf('day').toDate();

  const response = await FirebaseFirestore.addDocument({
    reference: 'habits',
    data: {
      title: title.value,
      created_at: today,
      week_days: [ ...weekDays.value ]
    }
  });

  const habitid = response.reference.id;

  if (!habitid) {
    showAlert('Ops', 'Não foi possível criar o hábito');
    return;
  }

  weekDays.value.forEach(async (weekDay) => {
    await handleCreateHabitWeekDay(habitid, weekDay);
  });

  title.value = '';
  weekDays.value = [];
  showAlert('Novo Hábito', 'Hábito criado com sucesso!');
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