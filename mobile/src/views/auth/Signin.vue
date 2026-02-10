<script setup>
import { ref, reactive, computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';
import { IonPage, IonContent, onIonViewDidLeave } from '@ionic/vue';
import { useAuthStore } from '@/stores/auth';
import { useToast } from '@/use/useToast';
import Container from '@/components/Container.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const authStore = useAuthStore();
const isLoading = ref(false);
const formData = reactive({
  email: '',
  password: ''
});

const { showToast } = useToast();

const signIn = async () => {
  isLoading.value = true;

  try {
    await authStore.login(formData);
  } catch (err) {
    console.error('Login failed:', err);
  } finally {
    isLoading.value = false;
  }
};

const rules = computed(() => {
  return {
    email: { required, email },
    password: { required }
  };
});

const v$ = useVuelidate(rules, formData);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();

  if (!isFormCorrect) {
    showToast('error', 'Informe um e-mail válido e a senha');
    return;
  } 
  
  signIn();
};

onIonViewDidLeave(() => {
  formData.email = '';
  formData.password = '';
});
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <Container>
        <form>
          <h1>habits</h1>

          <Input
            v-model="formData.email"
            type="text"
            label="Seu e-mail"
            placeholder="exemplo@email.com"
          /> 

          <Input
            v-model="formData.password"
            type="password"
            label="Sua senha"
            placeholder="Digite sua senha"
          /> 

          <router-link to="/forgot">
            Esqueci a senha
          </router-link>

          <Button :is-loading="isLoading" @click="submitForm">
            Entrar
          </Button>

          <div class="separator">
            <span>ou</span>
          </div>

          <Button color="outline" router-link="/signup">
            Criar minha conta
          </Button>
        </form>
      </Container>
    </ion-content>
  </ion-page>
</template>

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

form a {
  font-size: .85rem;
  text-decoration: none;
  letter-spacing: .25px;
  width: fit-content;
  margin: 1.25rem 0 1.25rem auto;
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