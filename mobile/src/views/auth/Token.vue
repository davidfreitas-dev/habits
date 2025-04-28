<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <Container>
        <form>
          <h1>habits</h1>

          <Input
            v-model="formData.token"
            type="text"
            label="Informe o token"
            placeholder="Token de recuperação"
          /> 
          
          <div class="ion-margin-top ion-padding-top">
            <Button :is-loading="isLoading" @click="submitForm">
              Continuar
            </Button>

            <div class="separator">
              <span>ou</span>
            </div>

            <Button color="outline" router-link="/signin">
              Voltar ao login
            </Button>
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
import { required } from '@vuelidate/validators';
import { IonPage, IonContent } from '@ionic/vue';
import { useToast } from '@/use/useToast';

import axios from '@/api/axios';
import Container from '@/components/Container.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const router = useRouter();
const isLoading = ref(false);
const formData = reactive({
  token: ''
});

const { showToast } = useToast();

const handleContinue = async () => {
  isLoading.value = true;

  try {
    const response = await axios.post('/forgot/token', formData);
    const data = JSON.stringify(response.data);
    router.push({ name: 'Reset', query: { data } });
  } catch (err) {
    const error = err.response.data;
    showToast('error', error.message);
  }

  isLoading.value = false;
};

const rules = computed(() => {
  return {
    token: { required }
  };
});

const v$ = useVuelidate(rules, formData);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();

  if (!isFormCorrect) {
    showToast('error', 'Informe o token de redefinição de senha');
    return;
  } 
  
  handleContinue();
};
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
