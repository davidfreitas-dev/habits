<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <Container>
        <form>
          <h1>habits</h1>

          <Input
            v-model="formData.email"
            type="text"
            label="Endereço de e-mail"
            placeholder="exemplo@email.com"
          /> 

          <Button
            class="ion-margin-top"
            :is-loading="isLoading"
            @click="submitForm"
          >
            Continuar
          </Button>
        </form>

        <router-link to="/signin">
          Voltar ao login
        </router-link>
      </Container>

      <Toast ref="toastRef" />
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, onIonViewDidLeave } from '@ionic/vue';
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';
import axios from '@/api/axios';
import Container from '@/components/Container.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';
import Toast from '@/components/Toast.vue';

const router = useRouter();
const isLoading = ref(false);
const toastRef = ref(undefined);
const formData = reactive({
  email: ''
});

const handleContinue = async () => {
  isLoading.value = true;

  try {
    const response = await axios.post('/forgot', formData);
    toastRef.value?.setOpen(true, response.status, response.message);
    router.push('/forgot/token'); 
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, 'error', error.message);
  }

  isLoading.value = false;
};

const rules = computed(() => {
  return {
    email: { required, email }
  };
});

const v$ = useVuelidate(rules, formData);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();

  if (!isFormCorrect) {
    toastRef.value?.setOpen(true, 'error', 'Informe um e-mail válido');
    return;
  } 
  
  handleContinue();
};

onIonViewDidLeave(() => {
  formData.email = '';
});
</script>

<style scoped>
form {
  display: flex;
  flex-direction: column;
  margin-top: 5rem;
  margin-bottom: 3rem;
  padding: 0 .5rem;
}

form h1 {
  font-size: 3rem;
  text-align: center;
  color: var(--font);
  font-weight: 800;
}

form a {
  display: block;
  font-size: .85rem;
  text-align: right;
  text-decoration: none;
  letter-spacing: .25px;
  margin: 1.25rem 0;
  color: var(--success);
}

a {
  font-size: .85rem;
  text-align: center;
  text-decoration: none;
  letter-spacing: .25px;
  color: var(--success);
}
</style>
