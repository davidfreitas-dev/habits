<template>
  <ion-page>
    <Header>
      <BackButton />
    </Header>

    <ion-content :fullscreen="true">
      <Container>
        <Segment v-model="selectedSegment" :segments="segments" />

        <div v-if="selectedSegment === 'first'" class="ion-padding">
          <h1>Privacy Policy</h1>
          <section>
            <h2>General Information</h2>
            <p>This privacy policy applies to the Habits - Rastreador de Hábitos app (hereby referred to as "Application") for mobile devices that was created by David Freitas (hereby referred to as "Service Provider") as a Free service. This service is intended for use "AS IS".</p>
          </section>
          <section>
            <h2>Information Collection and Use</h2>
            <p>The Application collects information when you download and use it. This information may include:</p>
            <ul>
              <li>Your device's Internet Protocol address (e.g. IP address)</li>
              <li>The pages of the Application that you visit, the time and date of your visit, the time spent on those pages</li>
              <li>The time spent on the Application</li>
              <li>The operating system you use on your mobile device</li>
            </ul>
            <p>The Application does not gather precise information about the location of your mobile device.</p>
          </section>
          <section>
            <h2>Third-Party Access</h2>
            <p>Only aggregated, anonymized data is periodically transmitted to external services to aid the Service Provider in improving the Application and their service.</p>
            <p>The Application utilizes third-party services with their own privacy policies:</p>
            <ul>
              <li>
                <a
                  href="https://www.google.com/policies/privacy/"
                  target="_blank"
                  rel="noopener noreferrer"
                >Google Play Services</a>
              </li>
            </ul>
          </section>
          <section>
            <h2>Security</h2>
            <p>The Service Provider is concerned about safeguarding the confidentiality of your information. The Service Provider provides physical, electronic, and procedural safeguards to protect information the Service Provider processes and maintains.</p>
          </section>
          <section>
            <h2>Changes</h2>
            <p>This Privacy Policy may be updated from time to time for any reason. The Service Provider will notify you of any changes to the Privacy Policy by updating this page with the new Privacy Policy. You are advised to consult this Privacy Policy regularly for any changes, as continued use is deemed approval of all changes.</p>
          </section>
          <section>
            <h2>Contact Us</h2>
            <p>If you have any questions regarding privacy while using the Application, or have questions about the practices, please contact the Service Provider via email at <a href="mailto:davidfreitas.dev@outlook.com">davidfreitas.dev@outlook.com</a>.</p>
          </section>

          <br>
  
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
          <h1>Terms &amp; Conditions</h1>
          <section>
            <h2>Usage of the Application</h2>
            <p>These terms and conditions apply to the Habits - Rastreador de Hábitos app (hereby referred to as "Application") for mobile devices that was created by David Freitas (hereby referred to as "Service Provider") as a Free service.</p>
            <p>Upon downloading or utilizing the Application, you are automatically agreeing to the following terms:</p>
            <ul>
              <li>Unauthorized copying, modification of the Application, any part of the Application, or our trademarks is strictly prohibited.</li>
              <li>Attempts to extract the source code of the Application, translate the Application into other languages, or create derivative versions are not permitted.</li>
            </ul>
          </section>
          <section>
            <h2>Third-Party Services</h2>
            <p>Please note that the Application utilizes third-party services that have their own Terms and Conditions:</p>
            <ul>
              <li>
                <a
                  href="https://policies.google.com/terms"
                  target="_blank"
                  rel="noopener noreferrer"
                >Google Play Services</a>
              </li>
            </ul>
          </section>
          <section>
            <h2>Changes to These Terms and Conditions</h2>
            <p>The Service Provider may periodically update their Terms and Conditions. Therefore, you are advised to review this page regularly for any changes. The Service Provider will notify you of any changes by posting the new Terms and Conditions on this page.</p>
          </section>
          <section>
            <h2>Contact Us</h2>
            <p>If you have any questions or suggestions about the Terms and Conditions, please do not hesitate to contact the Service Provider at <a href="mailto:davidfreitas.dev@outlook.com">davidfreitas.dev@outlook.com</a>.</p>
          </section>

          <br>
  
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
import Header from '@/components/Header.vue';
import Container from '@/components/Container.vue';
import Segment from '@/components/Segment.vue';
import BackButton from '@/components/BackButton.vue';

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
  color: var(--font);
  margin-bottom: 1rem;
}

h2 {
  font-size: 1.2rem;
  color: var(--font);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

p, ul {
  font-size: 1rem;
  color: var(--secondary);
  margin-bottom: 1rem;
}

ul {
  list-style-type: disc;
}

a {
  color: var(--success);
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>
