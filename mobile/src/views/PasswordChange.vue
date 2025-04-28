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

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { jwtDecode } from 'jwt-decode';
import { IonContent, IonPage, IonIcon, onIonViewDidLeave } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import { useSessionStore } from '@/stores/session';
import { useToast } from '@/use/useToast';

import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const isLoading = ref(false);

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

const storeSession = useSessionStore();

const router = useRouter();

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

const { showToast } = useToast();

const updatePassword = async () => { 
  if (formData.newPassword !== formData.confNewPassword) {
    showToast('error', 'A nova senha não coincide com a confirmação');
    return;
  }

  const request = {
    userId: user.value.id,
    email: user.value.email,
    password: formData.currentPassword
  };

  isLoading.value = true;

  try {
    await axios.post('/signin', {
      email: user.value.email,
      password: formData.currentPassword
    });

    await axios.post('/forgot/reset', {
      userId: user.value.id,
      password: formData.newPassword
    });

    showToast('success', 'Senha alterada com sucesso');
    
    router.push('/settings');
  } catch (err) {
    const error = err.response.data;
    showToast('error', error.message);
  }
  
  isLoading.value = false;
};
</script>

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
