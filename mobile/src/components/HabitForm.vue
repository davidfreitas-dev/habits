<template>
  <form>
    <h1>Criar hábito</h1>
    
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

<script setup>
import { ref, watch } from 'vue';
import { IonIcon } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import Input from '@/components/Input.vue';
import Checkbox from '@/components/Checkbox.vue';
import Button from '@/components/Button.vue';

const emit = defineEmits(['onError', 'onSubmit']);

const formData = ref({
  title: '',
  weekDays: []
});

const availableWeekDays = [
  'Domingo', 
  'Segunda-feira', 
  'Terça-feira', 
  'Quarta-feira', 
  'Quinta-feira', 
  'Sexta-feira', 
  'Sábado'
];

const isLoading = ref(false);

const isDayChecked = (index) => {
  return formData.value.weekDays.includes(index);
};

const toggleWeekDay = (index) => {
  const days = formData.value.weekDays;

  if (days.includes(index)) {
    formData.value.weekDays = days.filter(day => day !== index);
    return;
  } 
  
  days.push(index);
};

const submitForm = () => {
  if (!formData.value.title || !formData.value.weekDays.length) {
    emit('onError', 'Informe um título para o hábito e selecione os dias.');
    return;
  }

  emit('onSubmit', formData.value);
};

const clearFormData = () => {
  formData.value.title = '';
  formData.value.weekDays = [];
};

defineExpose({clearFormData});
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
  margin-top: 1.5rem;
}
</style>
