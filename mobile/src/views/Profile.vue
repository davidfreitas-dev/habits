<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { IonPage, IonContent, IonIcon, onIonViewWillEnter } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import { useProfileStore } from '@/stores/profile';
import { useToast } from '@/use/useToast';

import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const profileStore = useProfileStore();
const isLoading = ref(false);

const formData = reactive({
  name: '',
  email: '',
});

const isDisabled = computed(() => !formData.name);

const { showToast } = useToast();

const loadProfileData = () => {
  if (profileStore.user) {
    formData.name = profileStore.user.name || '';
    formData.email = profileStore.user.email || '';
  }
};

onIonViewWillEnter(async () => {
  isLoading.value = true;
  try {
    await profileStore.fetchProfile();
    loadProfileData();
  } catch (err) {
    console.error('Failed to fetch profile:', err);
    showToast('error', err.response?.data?.message || 'Erro ao carregar dados do perfil.');
  } finally {
    isLoading.value = false;
  }
});

// Watch for changes in profileStore.user to update formData
watch(() => profileStore.user, (newUser) => {
  if (newUser) {
    loadProfileData();
  }
}, { deep: true });

const updateProfile = async () => {
  isLoading.value = true;

  try {
    const dataToUpdate = {
      name: formData.name,
      email: formData.email,
    };
    const success = await profileStore.updateProfile(dataToUpdate);
    if (success) {
      showToast('success', 'Perfil atualizado com sucesso!');
    }
  } catch (err) {
    console.error('Profile update failed:', err);
    showToast('error', err.response?.data?.message || 'Erro ao atualizar dados do perfil.');
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
        <Heading title="Edição de Perfil" />

        <form>
          <div>
            <Input
              v-model="formData.name"
              type="text"
              label="Nome"
              placeholder="Digite seu nome"
            />

            <Input
              v-model="formData.email"
              type="email"
              label="E-mail"
              placeholder="Digite seu e-mail"
            />
          </div>

          <Button
            :is-disabled="isDisabled"
            :is-loading="isLoading"
            @click="updateProfile"
          >
            <ion-icon :icon="checkmark" /> Confirmar
          </Button>
        </form>
      </Container>
    </ion-content>
  </ion-page>
</template>

<style scoped>
form div {
  display: flex;
  flex-direction: column;
  margin-bottom: 2rem;
}

a {
  font-size: .85rem;
  text-align: center;
  text-decoration: none;
  letter-spacing: .25px;
  color: var(--success);
}
</style>
