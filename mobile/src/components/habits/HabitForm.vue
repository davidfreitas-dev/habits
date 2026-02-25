<script setup>
import { ref, watch } from 'vue';
import { IonDatetime, IonDatetimeButton, IonModal, IonItem, IonLabel } from '@ionic/vue';
import Input from '@/components/ui/Input.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Button from '@/components/ui/Button.vue';
import Toggle from '@/components/ui/Toggle.vue';

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
  reminderEnabled: false,
  reminderTime: new Date().toISOString(),
});

watch(
  () => props.habit,
  (newHabit) => {
    if (newHabit) {
      formData.value.title = newHabit.title || '';
      formData.value.weekDays = [];
      if (newHabit.week_days) {
        if (Array.isArray(newHabit.week_days)) {
          formData.value.weekDays = newHabit.week_days.map(Number);
        } else if (typeof newHabit.week_days === 'string' && newHabit.week_days.length > 0) {
          formData.value.weekDays = newHabit.week_days.split(',').map(Number);
        }
      }
    }
  },
  { immediate: true }
);

const clearFormData = () => {
  formData.value.title = '';
  formData.value.weekDays = [];
  formData.value.reminderEnabled = false;
  formData.value.reminderTime = new Date().toISOString();
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

  emit('onSubmit', { ...formData.value });
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

    <!-- Reminder Section -->
    <ion-item class="ion-no-padding reminder-item">
      <ion-label class="ion-no-margin">
        Ativar lembrete
      </ion-label>
      <Toggle v-model:checked="formData.reminderEnabled" />
    </ion-item>

    <transition name="fade">
      <ion-item v-if="formData.reminderEnabled" class="ion-no-padding time-item">
        <ion-label class="ion-no-margin">
          Horário
        </ion-label>
        <ion-datetime-button datetime="reminder-datetime" />
        <ion-modal :keep-contents-mounted="true">
          <ion-datetime
            id="reminder-datetime"
            v-model="formData.reminderTime"
            presentation="time"
            locale="pt-BR"
          />
        </ion-modal>
      </ion-item>
    </transition>

    <Button
      color="primary"
      class="ion-margin-top"
      :is-loading="isLoading"
      @click="submitForm"
    >
      Confirmar
    </Button>
  </form>
</template>

<style scoped>
p {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-top: 1.5rem;
}

.reminder-item {
  color: var(--color-text-accent);
  font-size: 1.1rem;
  margin-top: 1.5rem;
  padding-top: .25rem;
  --inner-padding-end: 0;
}

.time-item {
  color: var(--color-text-accent);
  font-size: 1.1rem;
  --inner-padding-end: 0;
}

.reminder-icon {
  font-size: 1.2rem;
  margin-right: .5rem;
  --ionicon-stroke-width: 40px;
  color: var(--color-text-primary);
  flex-shrink: 0;
}

ion-datetime-button::part(native) {
  background: var(--color-background-secondary);
  color: var(--color-text-accent);
  border-radius: 8px;
  font-size: 1rem;
  padding: 6px 12px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

ion-modal {
  --background: var(--color-background-elevated);
  --backdrop-opacity: 0.7;
  --border-radius: 16px;
}

ion-modal ion-datetime {
  background: var(--color-background-elevated);
  border-radius: 16px;
  color: var(--color-text-primary);
  --wheel-highlight-background: var(--color-background-elevated);
  --wheel-fade-background-rgb: var(--color-background-elevated-rgb);
}

ion-modal ion-datetime::part(wheel-item) {
  color: var(--color-text-primary);
}

ion-modal ion-datetime::part(wheel-item active) {
  color: var(--color-primary);
}
</style>