<template>
  <ion-page>
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">      
      <Container>
        <Heading :title="pageTitle" />

        <HabitForm
          ref="habitFormRef"
          :habit="habit"
          :is-loading="isLoading"
          @on-submit="handleHabit"
          @on-error="showAlert"
        />

        <ion-button
          v-if="route.params.id"
          fill="clear"
          color="danger"
          expand="block"
          class="ion-margin-top"
          @click="handleDelete"
        >
          <ion-icon
            slot="start"
            size="small"
            :icon="trash"
            aria-hidden="true"
          />
          Excluir
        </ion-button>
      </Container>

      <ModalDialog
        ref="dialogRef"
        message="Deseja realmente excluir este hábito?"
        @on-confirm="deleteHabit"
      />

      <Alert ref="alertRef" />

      <Toast ref="toastRef" />
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { jwtDecode } from 'jwt-decode';
import { trash } from 'ionicons/icons';
import { IonPage, IonHeader, IonToolbar, IonButton, IonIcon, IonContent } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import HabitForm from '@/components/HabitForm.vue';
import ModalDialog from '@/components/ModalDialog.vue';
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

const toastRef = ref(null);

onMounted(async () => {
  if (!habit.value.id) return;
  
  try {
    const response = await axios.get('/habits/' + habit.value.id);
    habit.value = response.data;
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, 'error', error.message);
  }
});

const isLoading = ref(false);
const alertRef = ref(null);
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
    toastRef.value?.setOpen(true, 'error', error.message);
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
    toastRef.value?.setOpen(true, 'error', error.message);
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

const dialogRef = ref(null);

const handleDelete = () => {
  dialogRef.value?.setOpen(true);
};

const router = useRouter();

const deleteHabit = async (formData) => {
  isLoading.value = true;

  try {
    await axios.delete('/habits/delete/' + habit.value.id);
    router.go(-1);
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, 'error', error.message);
  }

  isLoading.value = false;
};
</script>
