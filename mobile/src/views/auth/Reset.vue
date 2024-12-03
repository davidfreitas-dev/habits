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

            <Button :outline="true" router-link="/signin">
              Voltar ao login
            </Button>
          </div>
        </form>
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
import { required, sameAs } from '@vuelidate/validators';
import axios from '@/api/axios';
import Container from '@/components/Container.vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';
import Toast from '@/components/Toast.vue';

const props = defineProps({
  data: {
    type: String,
    default: ''
  }
});

const router = useRouter();
const isLoading = ref(false);
const toastRef = ref(undefined);
const formData = reactive({
  password: '',
  confPassword: ''
});

const handleConfirm = async () => {
  isLoading.value = true;

  try {
    const data = JSON.parse(props.data);

    const response = await axios.post('/forgot/reset', {
      password: formData.password,
      recoveryId: data.recoveryId,
      userId: data.userId
    });

    toastRef.value?.setOpen(true, response.status, response.message);

    router.push('/');
  } catch (err) {
    const error = err.response.data;
    toastRef.value?.setOpen(true, 'error', error.message);
  }

  isLoading.value = false;  
};

const rules = computed(() => {
  return {
    password: { required },
    confPassword: {
      required,
      sameAsPassword: sameAs(formData.password)
    }
  };
});

const v$ = useVuelidate(rules, formData);

const submitForm = async () => {
  const isFormCorrect = await v$.value.$validate();

  if (!isFormCorrect) {
    toastRef.value?.setOpen(true, 'error', 'Preencha os campos com senhas idênticas');
    return;
  } 
  
  handleConfirm();
};

onIonViewDidLeave(() => {
  formData.password = '';
  formData.confPassword = '';
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

.separator {
  display: flex;
  align-items: center;
  margin: 2rem 0;
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
