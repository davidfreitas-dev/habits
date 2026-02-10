import { defineStore } from 'pinia';
import { useToast } from '@/use/useToast';
import axios from '@/api/axios';

export const useHabitStore = defineStore('habit', () => {
  const { showToast } = useToast();

  const createHabit = async (title, weekDays) => {
    try {
      const response = await axios.post('/habits', {
        title,
        week_days: weekDays,
      });
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao criar hábito.');
      throw error;
    }
  };

  const getDayInfo = async (date) => {
    try {
      const response = await axios.get('/habits/day', {
        params: {
          date: date,
        },
      });
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao carregar hábitos do dia.');
      throw error;
    }
  };

  const getHabitsSummary = async () => {
    try {
      const response = await axios.get('/habits/summary');
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao carregar resumo de hábitos.');
      throw error;
    }
  };

  const getHabitDetails = async (id) => {
    try {
      const response = await axios.get('/habits/' + id);
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao carregar detalhes do hábito.');
      throw error;
    }
  };

  const updateHabit = async (id, title, weekDays) => {
    try {
      const response = await axios.put('/habits/' + id, {
        title,
        week_days: weekDays,
      });
      showToast('success', response.message || 'Hábito atualizado com sucesso!');
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao atualizar hábito.');
      throw error;
    }
  };

  const toggleHabit = async (habitId) => {
    try {
      const response = await axios.patch(`/habits/${habitId}/toggle`);
      showToast('success', response.message || 'Status dos hábito atualizado!');
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao atualizar hábito.');
      throw error;
    }
  };

  const deleteHabit = async (id) => {
    try {
      const response = await axios.delete('/habits/' + id);
      showToast('success', response.message || 'Hábito excluído com sucesso!');
      return response.data;
    } catch (error) {
      showToast('error', error.response?.data?.message || 'Erro ao excluir hábito.');
      throw error;
    }
  };

  return {
    createHabit,
    getDayInfo,
    getHabitsSummary,
    getHabitDetails,
    updateHabit,
    toggleHabit,
    deleteHabit,
  };
});
