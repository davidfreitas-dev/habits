<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { trash } from 'ionicons/icons';
import { IonPage, IonIcon, IonContent } from '@ionic/vue';
import { useProfileStore } from '@/stores/profile';
import { useToast } from '@/use/useToast';
import { useHabitStore } from '@/stores/habits';
import { useLoading } from '@/use/useLoading';
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
const { isLoading, withLoading } = useLoading();

onMounted(async () => {
  await withLoading(async () => {
    await profileStore.fetchProfile();
    if (!habit.value.id) return;
    const fetchedHabit = await habitStore.getHabitDetails(habit.value.id);
    habit.value = fetchedHabit;
  }, 'Erro ao carregar detalhes do hábito.');
});

const habitFormRef = ref(null);
const dialogRef = ref(null);

const handleFormError = (message) => {
  showToast('error', message);
};

const createHabit = async (formData) => {
  await withLoading(async () => {
    await habitStore.createHabit(formData.title, formData.weekDays);
    habitFormRef.value?.clearFormData();
    showToast('success', 'Hábito criado com sucesso!');
  }, 'Erro ao criar hábito.');
};

const updateHabit = async (formData) => {
  await withLoading(async () => {
    await habitStore.updateHabit(habit.value.id, formData.title, formData.weekDays);
    showToast('success', 'Hábito atualizado com sucesso!');
  }, 'Erro ao atualizar hábito.');
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
  await withLoading(async () => {
    await habitStore.deleteHabit(habit.value.id);
    showToast('success', 'Hábito excluído com sucesso!');
    router.go(-1);
  }, 'Erro ao excluir hábito.');
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
