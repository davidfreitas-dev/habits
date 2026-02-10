<script setup>
import { ref, watch } from 'vue';
import { IonIcon } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import Input from '@/components/Input.vue';
import Checkbox from '@/components/Checkbox.vue';
import Button from '@/components/Button.vue';

const emit = defineEmits(['onError', 'onSubmit']);

const props = defineProps({
  habit: {
    type: Object,
    default: () => ({
      id: null,
      title: '',
      week_days: '',
    }),
  },
});

const formData = ref({
  title: '',
  weekDays: [],
});

watch(
  () => props.habit,
  (newHabit) => {
    if (newHabit) {
      formData.value.title = newHabit.title || '';
      formData.value.weekDays = []; // Initialize to empty array
      if (newHabit.week_days) {
        if (Array.isArray(newHabit.week_days)) {
          formData.value.weekDays = newHabit.week_days.map(Number);
        } else if (typeof newHabit.week_days === 'string' && newHabit.week_days.length > 0) {
          formData.value.weekDays = newHabit.week_days.split(',').map(Number);
        }
      }
    }
  },
  { immediate: true } // Executa imediatamente para sincronizar ao montar
);

const clearFormData = () => {
  formData.value.title = '';
  formData.value.weekDays = [];
};

defineExpose({ clearFormData });

const isLoading = ref(false);

const isDayChecked = (index) => {
  return formData.value.weekDays.includes(index);
};

const toggleWeekDay = (index) => {
  const days = formData.value.weekDays;

  if (days.includes(index)) {
    formData.value.weekDays = days.filter((day) => day !== index);
    return;
  }

  days.push(index);
};

const submitForm = () => {
  if (!formData.value.title || !formData.value.weekDays.length) {
    emit('onError', 'Informe um título para o hábito e selecione os dias.');
    return;
  }

  const formattedData = {
    ...formData.value,
  };

  emit('onSubmit', formattedData);
};

const availableWeekDays = [
  'Domingo',
  'Segunda-feira',
  'Terça-feira',
  'Quarta-feira',
  'Quinta-feira',
  'Sexta-feira',
  'Sábado',
];
</script>

<template>
  <form>    
    <p>Qual seu comprometimento?</p>
    <Input
      v-model="formData.title"
      placeholder="Exercícios, dormir bem, etc..."
    /> 

    <p>Qual a recorrência?</p>
    <Checkbox
      v-for="(weekDay, index) in availableWeekDays"
      :key="index"
      :label="weekDay"
      :is-checked="isDayChecked(index)"
      @handle-checkbox-change="toggleWeekDay(index)"
    />

    <Button
      class="ion-margin-top"
      :is-loading="isLoading"
      @click="submitForm"
    >
      <ion-icon :icon="checkmark" /> Confirmar
    </Button>
  </form>
</template>

<style scoped>
p {
  color: var(--font);
  font-weight: 700;
  margin-top: 1.5rem;
}
</style>
