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

        <Toast ref="toastRef" />
      </Container>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { jwtDecode } from 'jwt-decode';
import { IonPage, IonContent, IonIcon, onIonViewDidLeave } from '@ionic/vue';
import { checkmark } from 'ionicons/icons';
import { useSessionStore } from '@/stores/session';
import axios from '@/api/axios';
import Header from '@/components/Header.vue';
import Heading from '@/components/Heading.vue';
import Container from '@/components/Container.vue';
import BackButton from '@/components/BackButton.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';
import Toast from '@/components/Toast.vue';

const isLoading = ref(false);

const formData = reactive({
  name: null,
  email: null
});

const isDisabled = computed(() => !formData.name || !formData.email);

const resetData = () => {
  formData.name = null;
  formData.email = null;
  isLoading.value = false;
};

onIonViewDidLeave(() => {
  resetData();
});

const toastRef = ref(undefined);

const storeSession = useSessionStore();

const router = useRouter();

const user = computed(() => {
  return storeSession.session && storeSession.session.token 
    ? jwtDecode(storeSession.session.token) 
    : null;
});

onMounted(async () => {
  if (user.value) {
    formData.name = user.value.name || '';
    formData.email = user.value.email || '';
  }
});

const updateProfile = async () => {
  isLoading.value = true;

  try {
    const response = await axios.put('/users/update/' + user.value.id, {
      name: formData.name,
      email: formData.email
    });

    await storeSession.setSession({ token: response.data });

    toastRef.value?.setOpen(true, 'success', 'Perfil atualizado com sucesso');
  } catch (err) {
    const error = err.response?.data || { message: 'Erro ao atualizar perfil' };
    toastRef.value?.setOpen(true, 'error', error.message);
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
