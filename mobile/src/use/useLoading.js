import { ref } from 'vue';
import { loadingController } from '@ionic/vue';
import { useToast } from '@/use/useToast';

export function useLoading() {
  const isLoading = ref(false);

  const { showToast } = useToast();

  const withLoading = async (fn, errMsg = 'Ocorreu um erro ao carregar os dados. Tente novamente mais tarde.') => {
    isLoading.value = true;

    const loading = await loadingController.create({
      spinner: 'crescent',
      cssClass: 'custom-loading-spinner',
      showBackdrop: false,
      translucent: true
    });

    await loading.present();

    try {
      await fn();
    } catch (err) {
      console.error('Erro na requisição:', err);
      showToast('error', errMsg);
    } finally {
      isLoading.value = false;
      await loading.dismiss();
    }
  };

  return {
    isLoading,
    withLoading
  };
}
