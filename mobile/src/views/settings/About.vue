<template>
  <ion-page>
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">
      <Container>
        <Segment v-model="selectedSegment" :segments="segments" />

        <div v-if="selectedSegment === 'first'" class="ion-padding">
          <h1>Política de Privacidade</h1>
          <section>
            <h2>Informações Gerais</h2>
            <p>Esta política de privacidade aplica-se ao aplicativo *Habits - Rastreador de Hábitos* (doravante denominado "Aplicativo"), desenvolvido por David Freitas ("Provedor de Serviço") como um serviço gratuito. Este serviço é fornecido "como está".</p>
          </section>
          <section>
            <h2>Coleta e Uso de Informações</h2>
            <p>O Aplicativo pode coletar informações como:</p>
            <ul>
              <li>Endereço IP do seu dispositivo</li>
              <li>Páginas visitadas, tempo e data da visita</li>
              <li>Sistema operacional do seu dispositivo</li>
            </ul>
            <p>O Aplicativo não coleta informações de localização precisa do dispositivo.</p>
          </section>
          <section>
            <h2>Terceiros</h2>
            <p>Os dados podem ser compartilhados com terceiros confiáveis para análise e melhorias, respeitando esta política de privacidade.</p>
            <p>Serviços de terceiros utilizados:</p>
            <ul>
              <li><a href="https://www.google.com/policies/privacy/" target="_blank">Google Play Services</a></li>
            </ul>
          </section>
          <section>
            <h2>Segurança</h2>
            <p>O Provedor de Serviço implementa medidas físicas, eletrônicas e procedimentais para proteger as informações coletadas.</p>
          </section>
          <section>
            <h2>Alterações</h2>
            <p>Esta política pode ser atualizada periodicamente. Verifique esta página regularmente.</p>
          </section>
          <section>
            <h2>Contato</h2>
            <p>Para dúvidas ou sugestões sobre privacidade, envie um e-mail para <a href="mailto:davidfreitas.dev@outlook.com">davidfreitas.dev@outlook.com</a>.</p>
          </section>
        </div>

        <div v-if="selectedSegment === 'second'" class="ion-padding">
          <h1>Termos de Uso</h1>
          <section>
            <h2>Uso do Aplicativo</h2>
            <p>Ao baixar ou usar o Aplicativo, você concorda com os seguintes termos:</p>
            <ul>
              <li>Proibição de copiar, modificar ou extrair o código-fonte do Aplicativo.</li>
              <li>Manutenção da segurança do dispositivo.</li>
            </ul>
          </section>
          <section>
            <h2>Serviços de Terceiros</h2>
            <p>O Aplicativo utiliza serviços de terceiros que possuem seus próprios Termos de Uso:</p>
            <ul>
              <li><a href="https://policies.google.com/terms" target="_blank">Google Play Services</a></li>
            </ul>
          </section>
          <section>
            <h2>Alterações nos Termos</h2>
            <p>Os termos podem ser atualizados periodicamente. Verifique esta página regularmente.</p>
          </section>
          <section>
            <h2>Contato</h2>
            <p>Para dúvidas ou sugestões sobre os Termos de Uso, envie um e-mail para <a href="mailto:davidfreitas.dev@outlook.com">davidfreitas.dev@outlook.com</a>.</p>
          </section>
        </div>
      </Container>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { IonPage, IonContent, IonLabel, IonSegment, IonSegmentButton } from '@ionic/vue';
import Header from '@/components/layout/Header.vue';
import Container from '@/components/layout/Container.vue';
import Segment from '@/components/ui/Segment.vue';
import BackButton from '@/components/layout/BackButton.vue';

const segments = ref([
  { 
    label: 'Política de Privacidade', 
    value: 'first' 
  },
  { 
    label: 'Termos de Uso', 
    value: 'second' 
  }
]);

const selectedSegment = ref('first');
</script>

<style scoped>
ion-content {
  overflow-y: auto;
}

h1 {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-text-primary);
  margin-bottom: 1rem;
}

h2 {
  font-size: 1.2rem;
  color: var(--color-text-primary);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

p, ul {
  font-size: 1rem;
  color: var(--color-text-secondary);
  margin-bottom: 1rem;
}

ul {
  list-style-type: disc;
}

a {
  color: var(--color-primary);
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>
