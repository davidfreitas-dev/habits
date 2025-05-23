<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <Container>
        <form>
          <h1>habits</h1>

          <Input
            v-model="formData.name"
            type="text"
            label="Seu nome e sobrenome"
            placeholder="Fulano de Tal"
          /> 

          <Input
            v-model="formData.email"
            type="text"
            label="Seu melhor e-mail"
            placeholder="exemplo@email.com"
          /> 
          
          <Input
            v-model="formData.password"
            type="password"
            label="Sua senha"
            placeholder="Digite sua senha"
          /> 

          <div class="ion-margin-top ion-padding-top">
            <Button :is-loading="isLoading" @click="submitForm">
              Criar conta
            </Button>

            <div class="separator">
              <span>ou</span>
            </div>

            <Button color="outline" router-link="/signin">
              Já tenho uma conta
            </Button>

            <div>
              Ao criar uma conta, você concorda com nossos 
              <router-link to="/about">
                Termos de Uso e Política de Privacidade
              </router-link>.
            </div>
          </div>
        </form>
      </Container>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';
import { IonPage, IonContent, onIonViewDidLeave } from '@ionic/vue';
import { useSessionStore } from '@/stores/session';
import { useToast } from '@/use/useToast';

import axios from '@/api/axios';
import Container from '@/components/Container.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const storeSession = useSessionStore();
const router = useRouter();
const isLoading = ref(false);
const formData = reactive({
  name: '',
  email: '',
  password: ''
});

const { showToast } = useToast();

const signUp = async () => {
  isLoading.value = true;

  try {
    const response = await axios.post('/signup', formData);
    await storeSession.setSession({ token: response.data });
    router.push('/'); 
  } catch (err) {
    const error = err.response.data;
    showToast('error', error.message);
  }

  isLoading.value = false;
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
    showToast('error', 'Preencha todos os campos corretamente');
    return;
  } 
  
  signUp();
};

onIonViewDidLeave(() => {
  formData.name = '';
  formData.email = '';
  formData.password = '';
});
</script>

<style scoped>
form {
  display: flex;
  flex-direction: column;
  margin: 3rem 0;
  padding: 0 .5rem;
}

form h1 {
  font-size: 3rem;
  text-align: center;
  color: var(--font);
  font-weight: 800;
}

form div {
  font-size: .85rem;
  text-align: center;
  line-height: 1.6;
  margin: 1rem 0;
}

form a {
  font-size: .85rem;
  text-decoration: none;
  letter-spacing: .25px;
  margin: 1.25rem 0;
  color: var(--success);
}

.separator {
  display: flex;
  align-items: center;
  margin: 1.25rem 0;
  color: var(--font);
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
}

.separator::before,
.separator::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--font);
  margin: 0 0.75rem;
}
</style>
