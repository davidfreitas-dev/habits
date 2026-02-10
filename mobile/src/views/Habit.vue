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

const profileStore = useProfileStore();
const habitStore = useHabitStore();

const route = useRoute();
const router = useRouter();

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
    const fetchedHabit = await habitStore.getHabitDetails(habit.value.id);
    habit.value = fetchedHabit;
  } catch (err) {
    console.error('Error fetching habit details:', err);
    showToast('error', err.response?.data?.message || 'Erro ao carregar detalhes do hábito.');
    router.go(-1);
  }
});

const isLoading = ref(false);
const habitFormRef = ref(null);
const dialogRef = ref(null);

const handleFormError = (message) => {
  showToast('error', message);
};

const createHabit = async (formData) => {
  isLoading.value = true;
  try {
    await habitStore.createHabit(formData.title, formData.weekDays);
    habitFormRef.value?.clearFormData();
    showToast('success', 'Hábito criado com sucesso!');
  } catch (err) {
    console.error('Error creating habit:', err);
    showToast('error', err.response?.data?.message || 'Erro ao criar hábito.');
  } finally {
    isLoading.value = false;
  }
};

const updateHabit = async (formData) => {
  isLoading.value = true;
  try {
    await habitStore.updateHabit(habit.value.id, formData.title, formData.weekDays);
    showToast('success', 'Hábito atualizado com sucesso!');
  } catch (err) {
    console.error('Error updating habit:', err);
    showToast('error', err.response?.data?.message || 'Erro ao atualizar hábito.');
  } finally {
    isLoading.value = false;
  }
};

const handleHabit = (formData) => {
  if (habit.value.id) {
    updateHabit(formData);
  } else {
    createHabit(formData);
  }
};

const handleDelete = () => {
  dialogRef.value?.setOpen(true);
};

const deleteHabit = async () => {
  isLoading.value = true;
  try {
    await habitStore.deleteHabit(habit.value.id);
    showToast('success', 'Hábito excluído com sucesso!');
    router.go(-1);
  } catch (err) {
    console.error('Error deleting habit:', err);
    showToast('error', err.response?.data?.message || 'Erro ao excluir hábito.');
  } finally {
    isLoading.value = false;
  }
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
          @on-error="handleFormError"
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

      <!-- Removed Alert component as showAlert is removed -->
    </ion-content>
  </ion-page>
</template>
