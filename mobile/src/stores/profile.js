import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useAuthStore } from './auth';
import { ProfileService } from '@/services/ProfileService';

export const useProfileStore = defineStore('profile', () => {
  const user = ref(null);
  const authStore = useAuthStore();

  const fetchProfile = async () => {
    if (!authStore.isAuthenticated) {
      user.value = null;
      return;
    }
    const data = await ProfileService.getProfile();
    user.value = data.data;
    return true;
  };

  const updateProfile = async (profileData) => {
    const data = await ProfileService.updateProfile(profileData);
    user.value = data.data;
    return true;
  };

  const changePassword = async (currentPassword, newPassword, confirmNewPassword) => {
    return await ProfileService.changePassword(
      currentPassword,
      newPassword,
      confirmNewPassword
    );
  };

  const deleteAccount = async () => {
    return await ProfileService.deleteAccount();
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
