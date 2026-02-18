<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { IonContent, IonPage, IonItem, IonLabel, IonList, IonListHeader, IonIcon } from '@ionic/vue';
import { personOutline, gridOutline, chevronForwardOutline, exitOutline } from 'ionicons/icons';
import { useAuthStore } from '@/stores/auth';
import { useLoading } from '@/use/useLoading';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Button from '@/components/Button.vue';
import ModalDialog from '@/components/ModalDialog.vue';

const router = useRouter();

const { withLoading } = useLoading();

const authStore = useAuthStore();

const modalRef = ref(null);
    
const handleLogOut = (item) => {
  modalRef.value?.setOpen(true);
};

const logOut = async () => {
  await withLoading(async () => {
    await authStore.logout();
    router.push('/signin');
  }, 'Erro ao finalizar sessão.');
};
</script>

<template>
  <ion-page ref="pageRef">
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">
      <Container>
        <Heading title="Configurações" />
        
        <ion-list lines="none" class="ion-no-padding">
          <ion-list-header class="ion-no-padding">
            <ion-label class="ion-no-margin ion-padding-bottom ion-padding-top">
              <ion-icon :icon="personOutline" />
              Minha conta
            </ion-label>
          </ion-list-header>
          <ion-item class="ion-no-padding" router-link="/profile">
            <ion-label class="ion-no-margin ion-padding-top ion-padding-bottom">
              Editar perfil
            </ion-label>
            <ion-icon slot="end" :icon="chevronForwardOutline" />
          </ion-item>
          <ion-item class="ion-no-padding" router-link="/password-change">
            <ion-label class="ion-no-margin ion-padding-top ion-padding-bottom">
              Alterar senha
            </ion-label>
            <ion-icon slot="end" :icon="chevronForwardOutline" />
          </ion-item>
          <ion-item class="ion-no-padding" router-link="/delete-account">
            <ion-label class="ion-no-margin ion-padding-top ion-padding-bottom">
              Excluir conta
            </ion-label>
            <ion-icon slot="end" :icon="chevronForwardOutline" />
          </ion-item>
        </ion-list>

        <br>
        
        <ion-list lines="none" class="ion-no-padding">
          <ion-list-header class="ion-no-padding">
            <ion-label class="ion-no-margin ion-padding-bottom ion-padding-top">
              <ion-icon :icon="gridOutline" />
              Mais
            </ion-label>
          </ion-list-header>
          <ion-item class="ion-no-padding" router-link="/about">
            <ion-label class="ion-no-margin ion-padding-top ion-padding-bottom">
              Sobre o app
            </ion-label>
            <ion-icon slot="end" :icon="chevronForwardOutline" />
          </ion-item>
        </ion-list>

        <br>

        <Button class="ion-margin-top" @click="handleLogOut">
          <ion-icon slot="start" :icon="exitOutline" />
          Finalizar Sessão
        </Button>

        <ModalDialog
          ref="modalRef"
          message="Deseja realmente finalizar a sessão?"
          @on-confirm="logOut"
        />
      </Container>
    </ion-content>
  </ion-page>
</template>

<style scoped>
ion-list {
  background: var(--color-background-primary);
  margin-top: 1rem;
}
ion-list-header {
  color: var(--color-text-primary);
  font-size: 1.1rem;
  margin-bottom: .5rem;
  border-bottom: 1px solid var(--color-background-secondary);
}
ion-list-header ion-icon {
  font-size: 1.2rem;
  margin-right: .5rem;
  --ionicon-stroke-width: 40px;
}
ion-item {
  color: var(--color-text-accent);
  font-size: 1.1rem;
  --inner-padding-end: 0;
}
ion-item ion-icon {
  font-size: 1.2rem;
  color: var(--color-text-accent);
}
ion-label ion-icon {
  margin-bottom: -2px;
}
</style>
