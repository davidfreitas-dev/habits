<script setup>
import { ref, reactive, computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, sameAs } from '@vuelidate/validators';
import { IonPage, IonContent, onIonViewDidLeave } from '@ionic/vue';
import { useAuthStore } from '@/stores/auth';
import { useToast } from '@/use/useToast';
import Container from '@/components/Container.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const authStore = useAuthStore();
const isLoading = ref(false);
const formData = reactive({
  password: '',
  confPassword: ''
});

const { showToast } = useToast();

const handleConfirm = async () => {
  isLoading.value = true;

  try {
    // The authStore.resetPassword method will handle retrieving email and code from localStorage
    await authStore.resetPassword(formData.password, formData.confPassword);
    // Navigation is handled by the authStore after successful reset
  } catch (err) {
    console.error('Password reset failed:', err);
  } finally {
    isLoading.value = false;  
  }
};

const rules = computed(() => {
  return {
    password: { required },
    confPassword: {
      required,
      sameAsPassword: sameAs(computed(() => formData.password))
    }
  };
});

const v$ = useVuelidate(rules, formData);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();

  if (!isFormCorrect) {
    showToast('error', 'Preencha os campos com senhas idênticas');
    return;
  } 
  
  handleConfirm();
};

onIonViewDidLeave(() => {
  formData.password = '';
  formData.confPassword = '';
});
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <Container>
        <form>
          <h1>habits</h1>

          <Input
            v-model="formData.password"
            type="password"
            label="Digite a nova senha"
            placeholder="Sua nova senha"
          /> 

          <Input
            v-model="formData.confPassword"
            type="password"
            label="Confirme a nova senha"
            placeholder="Repita a senha"
          /> 

          <div class="ion-margin-top ion-padding-top">
            <Button :is-loading="isLoading" @click="submitForm">
              Confirmar
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
