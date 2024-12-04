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
          <p>Se desejar continuar, clique no botão abaixo. Caso tenha dúvidas ou precise de assistência, entre em contato com nosso suporte.</p>
        </div>

        <Button class="ion-margin-top ion-margin-bottom" @click="cancelDelete">
          Cancelar
        </Button>

        <Button
          color="danger"
          :is-loading="isLoading"
          @click="confirmDelete"
        >
          Confirmar Exclusão
        </Button>

        <Toast ref="toastRef" />
      </Container>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { jwtDecode } from 'jwt-decode';
import { IonContent, IonPage, IonItem, IonLabel, IonList } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Button from '@/components/Button.vue';
import Toast from '@/components/Toast.vue';

const storeSession = useSessionStore();

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

const router = useRouter();

const cancelDelete = () => {
  router.back();
};

const isLoading = ref(false);

const toastRef = ref(null);

const confirmDelete = async () => {
  isLoading.value = true;

  try {
    if (user.value.id) {
      await axios.delete('/users/delete' + user.value.id);
      toastRef.value?.setOpen(true, 'success', 'Conta excluída com sucesso');
      storeSession.clearSession();
      router.push('/signin');
    }
  } catch (err) {
    const error = err.response?.data || { message: 'Erro ao excluir conta' };
    toastRef.value?.setOpen(true, 'error', error.message);
  } 
  
  isLoading.value = false;
};
</script>
