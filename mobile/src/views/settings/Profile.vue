<script setup>
import { reactive, computed, watch } from 'vue';
import { IonPage, IonContent, IonIcon, onIonViewWillEnter } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import { useProfileStore } from '@/stores/profile';
import { useToast } from '@/composables/useToast';
import { useLoading } from '@/composables/useLoading'; 
import Header from '@/components/layout/Header.vue';
import Heading from '@/components/layout/Heading.vue';
import Container from '@/components/layout/Container.vue';
import BackButton from '@/components/layout/BackButton.vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';

const profileStore = useProfileStore();

const formData = reactive({
  name: '',
  email: '',
});

const isDisabled = computed(() => !formData.name);

const { showToast } = useToast();
const { isLoading, withLoading } = useLoading();

const loadProfileData = () => {
  if (profileStore.user) {
    formData.name = profileStore.user.name || '';
    formData.email = profileStore.user.email || '';
  }
};

onIonViewWillEnter(async () => {
  await withLoading(async () => {
    await profileStore.fetchProfile();
    loadProfileData();
  }, 'Erro ao carregar dados do perfil.');
});

// Watch for changes in profileStore.user to update formData
watch(() => profileStore.user, (newUser) => {
  if (newUser) {
    loadProfileData();
  }
}, { deep: true });

const updateProfile = async () => {
  await withLoading(async () => {
    await profileStore.updateProfile({
      name: formData.name,
      email: formData.email,
    });
    showToast('success', 'Perfil atualizado com sucesso!');
  }, 'Erro ao atualizar dados do perfil.');
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
  color: var(--color-success);
}
</style>
