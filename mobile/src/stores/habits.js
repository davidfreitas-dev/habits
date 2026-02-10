import { defineStore } from 'pinia';
import axios from '@/api/axios';

export const useHabitStore = defineStore('habit', () => {
  const createHabit = async (title, weekDays) => {
    const response = await axios.post('/habits', {
      title,
      week_days: weekDays,
    });
    return response.data;
  };

  const getDayInfo = async (date) => {
    const response = await axios.get('/habits/day', {
      params: {
        date: date,
      },
    });
    return response.data;
  };

  const getHabitsSummary = async () => {
    const response = await axios.get('/habits/summary');
    return response.data;
  };

  const getHabitDetails = async (id) => {
    const response = await axios.get('/habits/' + id);
    return response.data;
  };

  const updateHabit = async (id, title, weekDays) => {
    const response = await axios.put('/habits/' + id, {
      title,
      week_days: weekDays,
    });
    return response;
  };

  const toggleHabit = async (habitId) => {
    const response = await axios.patch(`/habits/${habitId}/toggle`);
    return response;
  };

  const deleteHabit = async (id) => {
    const response = await axios.delete('/habits/' + id);
    return response;
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
