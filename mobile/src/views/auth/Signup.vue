<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div id="container">
        <form>
          <h1>habits</h1>

          <Input
            type="text"
            v-model="formData.name"
            placeholder="Nome e sobrenome"
          /> 

          <Input
            type="text"
            v-model="formData.email"
            placeholder="Endereço de e-mail"
          /> 

          <Input
            type="password"
            v-model="formData.password"
            placeholder="Sua senha"
          /> 

          <Button :is-loading="isLoading" @click="submitForm">
            Criar conta
          </Button>
        </form>

        <router-link to="/signin">
          Já possuo uma conta
        </router-link>
      </div>

      <Toast ref="toastRef" />
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';
import axios from '@/api/axios';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';
import Toast from '@/components/Toast.vue';

const storeSession = useSessionStore();
const router = useRouter();
const isLoading = ref(false);
const toastRef = ref(undefined);
const formData = reactive({
  name: '',
  email: '',
  password: ''
});

const signUp = async () => {
  isLoading.value = true;

  const response = await axios.post('/signup', formData);

  isLoading.value = false;

  if (response.status === 'error') {
    toastRef.value?.setOpen(true, response.status, response.data);
    return;
  }

  storeSession.setSession(response.data);

  router.push('/'); 
};

const rules = computed(() => {
  return {
    name: { required },
    email: { required, email },
    password: { required }
  };
});

const v$ = useVuelidate(rules, formData);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();

  if (!isFormCorrect) {
    toastRef.value?.setOpen(true, 'error', 'Preencha todos os campos corretamente');
    return;
  } 
  
  signUp();
};
</script>

<style scoped>
#container {
  display: flex;
  flex-direction: column;
}
form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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
  text-align: right;
}

a {
  font-size: .85rem;
  text-align: center;
  text-decoration: none;
  letter-spacing: .25px;
  color: var(--success);
}
</style>