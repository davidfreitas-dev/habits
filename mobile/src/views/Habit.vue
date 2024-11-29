<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar class="ion-safe-area-top">
        <BackButton />
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">      
      <div id="container">
        <h1>{{ pageTitle }}</h1>

        <HabitForm
          ref="habitFormRef"
          :habit="habit"
          :is-loading="isLoading"
          @on-submit="handleHabit"
          @on-error="showAlert"
        />

        <Alert ref="alertRef" />
      
        <Toast ref="toastRef" />
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { jwtDecode } from 'jwt-decode';
import { IonPage, IonHeader, IonToolbar, IonContent } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import axios from '@/api/axios';
import BackButton from '@/components/BackButton.vue';
import HabitForm from '@/components/HabitForm.vue';
import Alert from '@/components/Alert.vue';
import Toast from '@/components/Toast.vue';

const storeSession = useSessionStore();

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

const route = useRoute();

const pageTitle = computed(() => {
  return route.params.id ? 'Editar hábito' : 'Criar hábito';
});

const habit = ref({
  id: route.params.id,
  title: '',
  week_days: '',
});

onMounted(async () => {
  if (!habit.value.id) return;
  
  try {
    const response = await axios.get('/habits/' + habit.value.id);
    habit.value = response.data;
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, error.status, error.message);
  }
});

const isLoading = ref(false);
const alertRef = ref(null);
const toastRef = ref(null);
const habitFormRef = ref(null);

const showAlert = (header, message) => {
  alertRef.value?.setOpen(header, message);
};

const createHabit = async (formData) => {
  isLoading.value = true;

  try {
    const response = await axios.post('/habits/create', { 
      title: formData.title,
      weekDays: formData.weekDays,
      userId: user.value.id 
    });

    habitFormRef.value?.clearFormData();
    
    showAlert('Novo Hábito', response.message);
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, error.status, error.message);
  }

  isLoading.value = false;
};

const updateHabit = async (formData) => {
  isLoading.value = true;

  try {
    const response = await axios.put('/habits/update/' + habit.value.id, { 
      title: formData.title,
      weekDays: formData.weekDays
    });
    
    showAlert('Atualização Hábito', response.message);
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, error.status, error.message);
  }

  isLoading.value = false;
};

const handleHabit = (formData) => {
  if (habit.value.id) {
    updateHabit(formData);
    return;
  }

  createHabit(formData);
};
</script>

<style scoped>
h1 {
  color: var(--font);
  font-weight: 800;
  font-size: 1.875rem;
  margin: 0;
}
</style>
