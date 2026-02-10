<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonContent, IonPage, IonItem, IonLabel, IonList } from '@ionic/vue';
import { useAuthStore } from '@/stores/auth';
import { useProfileStore } from '@/stores/profile';
import { useToast } from '@/use/useToast';

import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Button from '@/components/Button.vue';

const authStore = useAuthStore();
const profileStore = useProfileStore();

const user = computed(() => {
  return profileStore.profile;
});

const router = useRouter();

const cancelDelete = () => {
  router.back();
};

const isLoading = ref(false);

const { showToast } = useToast();

const confirmDelete = async () => {
  isLoading.value = true;

  try {
    if (profileStore.profile.id) {
      await axios.delete('/users/delete/' + profileStore.profile.id);
      showToast('success', 'Conta excluída com sucesso');
      authStore.clearTokens();
      router.push('/signin');
    }
  } catch (err) {
    const error = err.response?.data || { message: 'Erro ao excluir conta' };
    showToast('error', error.message);
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
        <Heading title="Exclusão de Conta" />

        <div>
          <p><strong>Tem certeza de que deseja excluir sua conta?</strong></p>
          <p>
            Ao confirmar, as seguintes ações serão realizadas:
          </p>
          <ion-list lines="none" class="ion-no-padding">
            <ion-item>
              <ion-label>
                <strong>1. Remoção dos dados pessoais:</strong> Todos os seus dados pessoais armazenados em nossos sistemas serão apagados, incluindo informações de perfil.
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <strong>2. Perda de acesso:</strong> Você não poderá mais acessar sua conta ou utilizar nossos serviços associados ao seu login.
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <strong>3. Remoção dos dados de hábitos:</strong> Todos os seus dados registrados, incluindo hábitos rastreados, metas alcançadas e estatísticas de progresso, serão apagados de forma irreversível.
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <strong>4. Perda de lembretes e notificações:</strong> Qualquer lembrete ou notificação configurada será desativado e não será mais enviado.
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <strong>5. Exclusão de conquistas:</strong> Todas as conquistas desbloqueadas e registros históricos de desempenho serão apagados.
              </ion-label>
            </ion-item>
          </ion-list>
          <p><strong>Esta ação é irreversível!</strong></p>
          <p>Caso tenha dúvidas ou precise de assistência, clique em <strong>Cancelar</strong> e entre em contato com nosso suporte.</p>
        </div>

        <Button class="ion-margin-top" @click="cancelDelete">
          Cancelar
        </Button>

        <Button
          color="danger"
          class="ion-margin-top ion-margin-bottom"
          :is-loading="isLoading"
          @click="confirmDelete"
        >
          Confirmar Exclusão
        </Button>
      </Container>
    </ion-content>
  </ion-page>
</template>