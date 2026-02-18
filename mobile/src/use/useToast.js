import { toastController } from '@ionic/vue';
import { alertCircleOutline, checkmarkCircleOutline, informationCircleOutline } from 'ionicons/icons';

const TOAST_CONFIG = {
  success: {
    color: 'success',
    icon: checkmarkCircleOutline,
  },
  danger: {
    color: 'danger',
    icon: alertCircleOutline,
  },
  info: {
    color: 'light',
    icon: informationCircleOutline,
  },
};

export function useToast() {
  const showToast = async (status, message) => {
    const config = TOAST_CONFIG[status] ?? TOAST_CONFIG.info;

    const toast = await toastController.create({
      message,
      color: config.color,
      icon: config.icon,
      duration: 2500,
    });

    await toast.present();
  };

  return {
    showToast,
  };
}