import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useAuthStore } from './auth';
import axios from '@/api/axios';

export const useProfileStore = defineStore('profile', () => {
  const user = ref(null);
  const authStore = useAuthStore();

  const fetchProfile = async () => {
    if (!authStore.isAuthenticated) {
      user.value = null;
      return;
    }
    const response = await axios.get('/profile');
    user.value = response.data;
    return true;
  };

  const updateProfile = async (profileData) => {
    const response = await axios.put('/profile', profileData);
    user.value = response.data;
    return true;
  };

  const changePassword = async (currentPassword, newPassword, confirmNewPassword) => {
    const response = await axios.patch('/profile/change-password', {
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirm: confirmNewPassword,
    });
    return response;
  };

  const deleteAccount = async () => {
    const response = await axios.delete('/profile');
    return response;
  };

  const clearProfile = () => {
    user.value = null;
  };

  return {
    user,
    fetchProfile,
    updateProfile,
    changePassword,
    deleteAccount,
    clearProfile,
  };
}, {
  persist: true,
});
