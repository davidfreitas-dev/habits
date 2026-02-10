import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useProfileStore } from './profile';
import axios from '@/api/axios';

const STORAGE_KEYS = {
  ACCESS_TOKEN: 'access_token',
  REFRESH_TOKEN: 'refresh_token',
  FORGOT_EMAIL: 'forgot_password_email',
  RESET_EMAIL: 'reset_code_email',
  RESET_CODE: 'reset_code_value',
};

export const useAuthStore = defineStore('auth', () => {
  const accessToken = ref(localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN));
  const refreshToken = ref(localStorage.getItem(STORAGE_KEYS.REFRESH_TOKEN));
  const sessionExpired = ref(false);

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
      }

      return true;
    } finally {
      clearTokens();
    }
  };

  const handleSessionExpired = () => {
    sessionExpired.value = true;
    clearTokens();
  };

  const login = async (credentials) => {
    const response = await axios.post('/auth/login', credentials);
    
    if (response.data?.access_token && response.data?.refresh_token) {
      setTokens(response.data.access_token, response.data.refresh_token);
      await useProfileStore().fetchProfile();
      return true;
    }

    return false;
  };

  const register = async (userData) => {
    const response = await axios.post('/auth/register', userData);
    
    if (response.data?.access_token && response.data?.refresh_token) {
      setTokens(response.data.access_token, response.data.refresh_token);
      await useProfileStore().fetchProfile();
      return true;
    }

    return false;
  };

  const refreshAccessToken = async () => {
    if (!refreshToken.value) {
      clearTokens();
      return false;
    }

    const response = await axios.post('/auth/refresh', { 
      refresh_token: refreshToken.value 
    });

    if (response.data?.access_token && response.data?.refresh_token) {
      setTokens(response.data.access_token, response.data.refresh_token);
      await useProfileStore().fetchProfile();
      return true;
    }

    clearTokens();
    return false;
  };

  const forgotPassword = async (email) => {
    const response = await axios.post('/auth/forgot-password', { email });
    localStorage.setItem(STORAGE_KEYS.FORGOT_EMAIL, email);
    return response;
  };

  const validateResetCode = async (code) => {
    const email = localStorage.getItem(STORAGE_KEYS.FORGOT_EMAIL);
    
    if (!email) {
      throw new Error('E-mail de recuperação não encontrado.');
    }

    const response = await axios.post('/auth/validate-reset-code', { email, code });
    
    localStorage.setItem(STORAGE_KEYS.RESET_EMAIL, email);
    localStorage.setItem(STORAGE_KEYS.RESET_CODE, code);

    return response;
  };

  const resetPassword = async (password, passwordConfirm) => {
    const email = localStorage.getItem(STORAGE_KEYS.RESET_EMAIL);
    const code = localStorage.getItem(STORAGE_KEYS.RESET_CODE);
    
    if (!email || !code) {
      throw new Error('Informações de recuperação incompletas.');
    }

    const response = await axios.post('/auth/reset-password', { 
      email, 
      code, 
      password, 
      password_confirm: passwordConfirm 
    });
    
    localStorage.removeItem(STORAGE_KEYS.FORGOT_EMAIL);
    localStorage.removeItem(STORAGE_KEYS.RESET_EMAIL);
    localStorage.removeItem(STORAGE_KEYS.RESET_CODE);
    
    return response;
  };

  return {
    accessToken,
    refreshToken,
    isAuthenticated,
    sessionExpired,
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
