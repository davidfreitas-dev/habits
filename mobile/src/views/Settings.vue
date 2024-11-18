<template>
  <ion-page ref="pageRef">
    <ion-header class="ion-no-border">
      <ion-toolbar class="ion-safe-area-top">
        <BackButton />
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div id="container">
        <h1>Configurações</h1>
        
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
        </ion-list>

        <br>
        
        <ion-list lines="none" class="ion-no-padding">
          <ion-list-header class="ion-no-padding">
            <ion-label class="ion-no-margin ion-padding-bottom ion-padding-top">
              <ion-icon :icon="gridOutline" />
              Mais
            </ion-label>
          </ion-list-header>
          <ion-item class="ion-no-padding" router-link="/terms-and-conditions">
            <ion-label class="ion-no-margin ion-padding-top ion-padding-bottom">
              Termos e condições de uso
            </ion-label>
            <ion-icon slot="end" :icon="chevronForwardOutline" />
          </ion-item>
          <ion-item class="ion-no-padding" router-link="/privacy-policy">
            <ion-label class="ion-no-margin ion-padding-top ion-padding-bottom">
              Política de privacidade
            </ion-label>
            <ion-icon slot="end" :icon="chevronForwardOutline" />
          </ion-item>
        </ion-list>

        <br>

        <Button class="ion-margin-top" @click="handleLogOut">
          <ion-icon slot="start" :icon="exitOutline" />
          Finalizar Sessão
        </Button>

        <Alert ref="alertRef" @on-alert="logOut" />

        <Toast ref="toastRef" />
      </div>
    </ion-content>
  </ion-page>
</template>
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { IonContent, IonPage, IonHeader, IonToolbar, IonItem, IonLabel, IonList, IonListHeader, IonIcon } from '@ionic/vue';
import { personOutline, gridOutline, chevronForwardOutline, exitOutline } from 'ionicons/icons';
import BackButton from '@/components/BackButton.vue';
import Button from '@/components/Button.vue';
import Alert from '@/components/Alert.vue';
import Toast from '@/components/Toast.vue';

const alertRef = ref(null);

const showAlert = (header, message) => {
  alertRef.value?.setOpen(header, message);
};

const handleLogOut = () => {
  showAlert('Deseja realmente sair?', 'Lembre-se que não poderá editar hábitos de datas passadas.');
};

const router = useRouter();

const toastRef = ref(undefined);

const logOut = async () => {
  try {
    localStorage.clear();
    router.push('/');
  } catch (error) {
    toastRef.value?.setOpen(true, error.message);
  }
};
</script>
<style scoped>
h1 {
  color: var(--font);
  font-weight: 800;
  font-size: 1.875rem;
  margin: 0;
}
ion-list {
  background: var(--bg);
  margin-top: 1rem;
}
ion-list-header {
  color: var(--font);
  font-size: 1.1rem;
  margin-bottom: .5rem;
  border-bottom: 1px solid var(--bg-accent);
}
ion-list-header ion-icon {
  font-size: 1.2rem;
  margin-right: .5rem;
  --ionicon-stroke-width: 40px;
}
ion-item {
  color: var(--font-accent);
  font-size: 1.1rem;
  --inner-padding-end: 0;
}
ion-item ion-icon {
  font-size: 1.2rem;
  color: var(--font-accent);
}
ion-label ion-icon {
  margin-bottom: -2px;
}
</style>