<script setup>
import { useRouter } from 'vue-router';
import { ref, reactive, computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';
import { IonPage, IonContent, onIonViewDidLeave } from '@ionic/vue';
import { useAuthStore } from '@/stores/auth';
import { useToast } from '@/composables/useToast';
import Container from '@/components/layout/Container.vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';

const authStore = useAuthStore();
const isLoading = ref(false);
const formData = reactive({
  name: '',
  email: '',
  password: ''
});

const { showToast } = useToast();
const router = useRouter();

const signUp = async () => {
  isLoading.value = true;

  try {
    await authStore.register(formData);
    router.push('/');
  } catch (err) {
    console.error('Registration failed:', err);
    showToast('error', err.response?.data?.message || 'Erro ao criar conta.');
  } finally {
    isLoading.value = false;
  }
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
    showToast('info', 'Preencha todos os campos corretamente');
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
            <Button
              :is-loading="isLoading"
              :is-disabled="v$.$invalid"
              @click="submitForm"
            >
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
  color: var(--color-text-primary);
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
  color: var(--color-success);
}

.separator {
  display: flex;
  align-items: center;
  margin: 1.25rem 0;
  color: var(--color-text-primary);
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
}

.separator::before,
.separator::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--color-text-primary);
  margin: 0 0.75rem;
}
</style>
