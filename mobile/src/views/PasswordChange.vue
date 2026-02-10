<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonContent, IonPage, IonIcon, onIonViewDidLeave } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import { useProfileStore } from '@/stores/profile'; // Import the profile store
import { useToast } from '@/use/useToast';

import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const isLoading = ref(false);
const profileStore = useProfileStore(); // Initialize the profile store
const router = useRouter();

const formData = reactive({
  currentPassword: null,
  newPassword: null,
  confNewPassword: null
});

const isDisabled = computed(() => !formData.currentPassword || !formData.newPassword || !formData.confNewPassword);

const resetData = () => {
  formData.currentPassword = null;
  formData.newPassword = null;
  formData.confNewPassword = null;
  isLoading.value = false;
};

onIonViewDidLeave(() => {
  resetData();
});

const { showToast } = useToast();

const updatePassword = async () => { 
  if (formData.newPassword !== formData.confNewPassword) {
    showToast('error', 'A nova senha não coincide com a confirmação');
    return;
  }

  isLoading.value = true;

  try {
    await profileStore.changePassword(
      formData.currentPassword,
      formData.newPassword,
      formData.confNewPassword
    );
    
    router.push('/settings');
  } catch (err) {
    console.error('Password change failed:', err);
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
        <Heading title="Alteração de Senha" />

        <form>
          <div>
            <Input
              v-model="formData.currentPassword"
              type="password"
              label="Digite a senha atual"
              placeholder="Senha atual"
            />

            <Input
              v-model="formData.newPassword"
              type="password"
              label="Digite a nova senha"
              placeholder="Nova senha"
            />

            <Input
              v-model="formData.confNewPassword"
              type="password"
              label="Confirme a nova senha"
              placeholder="Repita a nova senha"
            />
          </div>

          <Button
            :is-disabled="isDisabled"
            :is-loading="isLoading"
            @click="updatePassword"
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
