import { ref, watch } from 'vue';
import { defineStore } from 'pinia';

export const useSessionStore = defineStore('session', () => {
  const session = ref(null);

  const setSession = (data) => {
    session.value = data;
  };

  const clearSession = async () => {
    sessionStorage.clear();
    session.value = null;
  };

  if (localStorage.getItem('session')) {
    session.value = JSON.parse(localStorage.getItem('session'));
  }
  
  watch(
    session,
    newSession => {
      localStorage.setItem('session', JSON.stringify(newSession));
    },
    { deep: true }
  );

  return { 
    session, 
    setSession,
    clearSession
  };
});