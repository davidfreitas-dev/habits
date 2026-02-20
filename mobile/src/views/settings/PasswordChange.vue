<script setup>
import { reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonContent, IonPage, onIonViewDidLeave } from '@ionic/vue';
import { useProfileStore } from '@/stores/profile';
import { useToast } from '@/composables/useToast';
import { useLoading } from '@/composables/useLoading';
import Header from '@/components/layout/Header.vue';
import Heading from '@/components/layout/Heading.vue';
import Container from '@/components/layout/Container.vue';
import BackButton from '@/components/layout/BackButton.vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';

const profileStore = useProfileStore();
const router = useRouter();

const formData = reactive({
  currentPassword: null,
  newPassword: null,
  confNewPassword: null
});

const isDisabled = computed(() => !formData.currentPassword || !formData.newPassword || !formData.confNewPassword);

const { showToast } = useToast();
const { isLoading, withLoading } = useLoading();

const resetData = () => {
  formData.currentPassword = null;
  formData.newPassword = null;
  formData.confNewPassword = null;
};

onIonViewDidLeave(() => {
  resetData();
});

const updatePassword = async () => { 
  if (formData.newPassword !== formData.confNewPassword) {
    showToast('info', 'A nova senha não coincide com a confirmação');
    return;
  }

  await withLoading(async () => {
    const response = await profileStore.changePassword(
      formData.currentPassword,
      formData.newPassword,
      formData.confNewPassword
    );
    showToast('success', response.message || 'Senha alterada com sucesso!');
    router.push('/tabs/options');
  }, 'Erro ao alterar a senha.');
};
</script>

<template>
  <ion-page>
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">
      <Container>
        <Heading title="Alteração de Senha" />

        <form>
          <div>
            <Input
              v-model="formData.currentPassword"
              type="password"
              label="Digite a senha atual"
              placeholder="Senha atual"
            />

            <Input
              v-model="formData.newPassword"
              type="password"
              label="Digite a nova senha"
              placeholder="Nova senha"
            />

            <Input
              v-model="formData.confNewPassword"
              type="password"
              label="Confirme a nova senha"
              placeholder="Repita a nova senha"
            />
          </div>

          <Button
            color="primary"
            :is-disabled="isDisabled"
            :is-loading="isLoading"
            @click="updatePassword"
          >
            Confirmar
          </Button>
        </form>
      </Container>
    </ion-content>
  </ion-page>
</template>

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
  color: var(--color-secondary);
}
</style>
