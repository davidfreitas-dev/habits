import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useAuthStore } from './auth';
import { useToast } from '@/use/useToast';
import axios from '@/api/axios';

export const useProfileStore = defineStore('profile', () => {
  const user = ref(null);
  const authStore = useAuthStore();
  const { showToast } = useToast();

  const fetchProfile = async () => {
    if (!authStore.isAuthenticated) {
      user.value = null;
      return;
    }
    try {
      const response = await axios.get('/profile');
      user.value = response.data;
      return true;
    } catch (error) {
      console.error('Erro ao buscar perfil:', error);
      showToast('error', error.response?.data?.message || 'Erro ao carregar dados do perfil.');
      user.value = null;
      authStore.clearTokens();
      throw error;
    }
  };

  const updateProfile = async (profileData) => {
    try {
      const response = await axios.put('/profile', profileData);
      user.value = response.data;
      showToast('success', response.data?.message || 'Perfil atualizado com sucesso!');
      return true;
    } catch (error) {
      console.error('Erro ao atualizar perfil:', error);
      showToast('error', error.response?.data?.message || 'Erro ao atualizar dados do perfil.');
      throw error;
    }
  };

  const clearProfile = () => {
    user.value = null;
  };

  return {
    user,
    fetchProfile,
    updateProfile,
    clearProfile,
  };
}, {
  persist: true,
});