import { ref } from 'vue';
import { loadingController } from '@ionic/vue';

export function useLoading() {
  const isLoading = ref(false);

  const withLoading = async (fn) => {
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
