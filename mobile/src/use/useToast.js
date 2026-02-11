import { toastController } from '@ionic/vue';
import { alertCircleOutline } from 'ionicons/icons';

export function useToast() {
  const showToast = async (status, message) => {
    const color = status === 'error' ? 'danger' : 'success';

    const toast = await toastController.create({
      message: message,
      color: color,
      duration: 2500,
      icon: alertCircleOutline,
    });

    await toast.present();
  };

  return {
    showToast,
  };
}
