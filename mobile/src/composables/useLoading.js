import { ref } from 'vue';
import { loadingController } from '@ionic/vue';
import { useToast } from '@/composables/useToast';

export function useLoading() {
  const { showToast } = useToast();
  
  const isLoading = ref(false);

  const withLoading = async (fn, errorMessage) => {
    isLoading.value = true;

    const loading = await loadingController.create({
      spinner: 'crescent',
      cssClass: 'custom-loading-spinner',
      showBackdrop: true,
      translucent: true
    });

    await loading.present();

    try {
      await fn();
    } catch (err) {
      console.error('Erro na requisição:', err);
      const apiErrorMessage = err.response?.data?.message || err.message;
      showToast('error', apiErrorMessage || errorMessage);
      throw err;
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
