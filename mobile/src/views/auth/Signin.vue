<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div id="container">
        <form>
          <h1>habits</h1>

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

          <a href="#">
            Esqueci a senha
          </a>

          <Button @click="handleSignin"> 
            Entrar
          </Button>
        </form>

        <a href="#">
          Criar minha conta
        </a>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { IonPage, IonContent } from '@ionic/vue';
import Input from '@/components/Input.vue';
import Button from '@/components/Button.vue';

const formData = ref({
  email: '',
  password: ''
});

const handleSignin = () => {

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