import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useProfileStore } from './profile';
import { alertController } from '@ionic/vue';
import { useToast } from '@/use/useToast';
import axios from '@/api/axios';
import router from '@/router';

const STORAGE_KEYS = {
  ACCESS_TOKEN: 'access_token',
  REFRESH_TOKEN: 'refresh_token',
  FORGOT_EMAIL: 'forgot_password_email',
  RESET_EMAIL: 'reset_code_email',
  RESET_CODE: 'reset_code_value',
};

export const useAuthStore = defineStore('auth', () => {
  const { showToast } = useToast();

  const accessToken = ref(localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN));
  const refreshToken = ref(localStorage.getItem(STORAGE_KEYS.REFRESH_TOKEN));
  const isAlertShowing = ref(false);

  const isAuthenticated = computed(() => !!accessToken.value && !!refreshToken.value);

  const setTokens = (access, refresh) => {
    accessToken.value = access;
    refreshToken.value = refresh;

    if (access) {
      localStorage.setItem(STORAGE_KEYS.ACCESS_TOKEN, access);
    } else {
      localStorage.removeItem(STORAGE_KEYS.ACCESS_TOKEN);
    }

    if (refresh) {
      localStorage.setItem(STORAGE_KEYS.REFRESH_TOKEN, refresh);
    } else {
      localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
    }
  };

  const clearTokens = () => {
    setTokens(null, null);
    useProfileStore().clearProfile();
  };

  const logout = async () => {
    try {
      if (isAuthenticated.value) {
        await axios.post('/auth/logout');
        showToast('success', 'Sessão finalizada com sucesso!');
      }
    } catch (error) {
      console.error('Erro ao fazer logout:', error);
      showToast('error', error.response?.data?.message || 'Erro ao finalizar sessão.');
    } finally {
      clearTokens();
      router.push('/signin');
    }
  };

  const handleSessionExpired = async () => {
    if (isAlertShowing.value) return;

    isAlertShowing.value = true;
    clearTokens();

    const alert = await alertController.create({
      header: 'Sessão Expirada',
      message: 'Sua sessão expirou. Por favor, faça login novamente.',
      cssClass: 'alert-box',
      buttons: [
        {
          text: 'OK',
          handler: async () => {
            isAlertShowing.value = false;
            await router.push('/signin');
          },
        },
      ],
    });

    await alert.present();
    
    const { role } = await alert.onDidDismiss();
    if (role === 'backdrop' || role === 'cancel') {
      isAlertShowing.value = false;
      if (router.currentRoute.value.path !== '/signin') {
        await router.push('/signin');
      }
    }
  };

  const login = async (credentials) => {
    try {
      const response = await axios.post('/auth/login', credentials);
      
      if (response.data?.access_token && response.data?.refresh_token) {
        setTokens(response.data.access_token, response.data.refresh_token);
        await useProfileStore().fetchProfile();
        showToast('success', 'Login realizado com sucesso!');
        router.push('/');
        return true;
      }

      return false;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao fazer login.');
      throw error;
    }
  };

  const register = async (userData) => {
    try {
      const response = await axios.post('/auth/register', userData);
      
      if (response.data?.access_token && response.data?.refresh_token) {
        setTokens(response.data.access_token, response.data.refresh_token);
        await useProfileStore().fetchProfile();
        showToast('success', 'Conta criada com sucesso!');
        router.push('/');
        return true;
      }
      
      return false;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao criar conta.');
      throw error;
    }
  };

  const refreshAccessToken = async () => {
    if (!refreshToken.value) {
      clearTokens();
      router.push('/signin');
      return false;
    }

    try {
      const response = await axios.post('/auth/refresh', { 
        refresh_token: refreshToken.value 
      });

      if (response.data?.access_token && response.data?.refresh_token) {
        setTokens(response.data.access_token, response.data.refresh_token);
        await useProfileStore().fetchProfile();
        return true;
      }

      clearTokens();
      router.push('/signin');
      return false;
    } catch (error) {
      console.error('Failed to refresh token:', error);
      clearTokens();
      router.push('/signin');
      return false;
    }
  };

  const forgotPassword = async (email) => {
    try {
      const response = await axios.post('/auth/forgot-password', { email });
      localStorage.setItem(STORAGE_KEYS.FORGOT_EMAIL, email);
      showToast('success', response.message || 'E-mail de recuperação enviado!');
      router.push('/forgot/token');
      return true;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao solicitar recuperação de senha.');
      throw error;
    }
  };

  const validateResetCode = async (code) => {
    const email = localStorage.getItem(STORAGE_KEYS.FORGOT_EMAIL);
    
    if (!email) {
      showToast('error', 'E-mail de recuperação não encontrado. Por favor, tente novamente.');
      router.push('/forgot');
      return false;
    }

    try {
      const response = await axios.post('/auth/validate-reset-code', { email, code });
      showToast('success', response.message || 'Código válido!');
      localStorage.setItem(STORAGE_KEYS.RESET_EMAIL, email);
      localStorage.setItem(STORAGE_KEYS.RESET_CODE, code);
      router.push({ name: 'Reset' });
      return true;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Código inválido.');
      throw error;
    }
  };

  const resetPassword = async (password, passwordConfirm) => {
    const email = localStorage.getItem(STORAGE_KEYS.RESET_EMAIL);
    const code = localStorage.getItem(STORAGE_KEYS.RESET_CODE);
    
    if (!email || !code) {
      showToast('error', 'Informações de recuperação incompletas. Por favor, tente novamente.');
      router.push('/forgot');
      return false;
    }

    try {
      const response = await axios.post('/auth/reset-password', { 
        email, 
        code, 
        password, 
        password_confirm: passwordConfirm 
      });
      
      showToast('success', response.message || 'Senha redefinida com sucesso!');
      
      localStorage.removeItem(STORAGE_KEYS.FORGOT_EMAIL);
      localStorage.removeItem(STORAGE_KEYS.RESET_EMAIL);
      localStorage.removeItem(STORAGE_KEYS.RESET_CODE);
      
      router.push('/signin');
      return true;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao redefinir senha.');
      throw error;
    }
  };

  return {
    accessToken,
    refreshToken,
    isAuthenticated,
    setTokens,
    clearTokens,
    login,
    register,
    logout,
    handleSessionExpired,
    forgotPassword,
    validateResetCode,
    resetPassword,
    refreshAccessToken,
  };
});