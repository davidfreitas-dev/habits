<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { trash } from 'ionicons/icons';
import { IonPage, IonIcon, IonContent } from '@ionic/vue';
import { useProfileStore } from '@/stores/profile';
import { useToast } from '@/use/useToast';
import { useHabitStore } from '@/stores/habits';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import Button from '@/components/Button.vue';
import BackButton from '@/components/BackButton.vue';
import HabitForm from '@/components/HabitForm.vue';
import ModalDialog from '@/components/ModalDialog.vue';
import Alert from '@/components/Alert.vue';

const profileStore = useProfileStore();
const habitStore = useHabitStore();

const route = useRoute();

const pageTitle = computed(() => {
  return route.params.id ? 'Editar hábito' : 'Criar hábito';
});

const habit = ref({
  id: route.params.id,
  title: '',
  week_days: '',
});

const { showToast } = useToast();

onMounted(async () => {
  await profileStore.fetchProfile();
  if (!habit.value.id) return;
  
  try {
    habit.value = await habitStore.getHabitDetails(habit.value.id);
  } catch (err) {
    showToast('error', err.message);
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
    await habitStore.createHabit(formData.title, formData.weekDays);
    habitFormRef.value?.clearFormData();
    showAlert('Novo Hábito', 'Hábito criado com sucesso!');    
  } catch (err) {
    showToast('error', err.message);
  }

  isLoading.value = false;
};

const updateHabit = async (formData) => {
  isLoading.value = true;

  try {
    await habitStore.updateHabit(habit.value.id, formData.title, formData.weekDays);
    showAlert('Atualização Hábito', 'Hábito atualizado com sucesso!');
  } catch (err) {
    showToast('error', err.message);
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

const deleteHabit = async () => {
  isLoading.value = true;

  try {
    await habitStore.deleteHabit(habit.value.id);
    router.go(-1);
  } catch (err) {
    showToast('error', err.message);
  }

  isLoading.value = false;
};
</script>

<template>
  <ion-page>
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">      
      <Container>
        <Heading :title="pageTitle" />

        <HabitForm
          v-if="!route.params.id || habit.title"
          ref="habitFormRef"
          :habit="habit"
          :is-loading="isLoading"
          @on-submit="handleHabit"
          @on-error="showAlert"
        />

        <Button
          v-if="route.params.id"
          color="danger"
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
        </Button>
      </Container>

      <ModalDialog
        ref="dialogRef"
        message="Deseja realmente excluir este hábito?"
        @on-confirm="deleteHabit"
      />

      <Alert ref="alertRef" />
    </ion-content>
  </ion-page>
</template>
