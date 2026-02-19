import { defineStore } from 'pinia';
import { HabitService } from '@/services/HabitService';

export const useHabitStore = defineStore('habit', () => {
  const createHabit = async (title, weekDays) => {
    const data = await HabitService.create(title, weekDays);
    return data.data;
  };

  const getDayInfo = async (date) => {
    const data = await HabitService.getDayInfo(date);
    return data.data;
  };

  const getHabitsSummary = async () => {
    const data = await HabitService.getSummary();
    return data.data;
  };

  const getHabitStats = async (period) => {
    const data = await HabitService.getStats(period);
    return data.data;
  };

  const getHabitDetails = async (id) => {
    const data = await HabitService.getDetails(id);
    return data.data;
  };

  const updateHabit = async (id, title, weekDays) => {
    return await HabitService.update(id, title, weekDays);
  };

  const toggleHabit = async (habitId) => {
    return await HabitService.toggle(habitId);
  };

  const deleteHabit = async (id) => {
    return await HabitService.delete(id);
  };

  return {
    createHabit,
    getDayInfo,
    getHabitsSummary,
    getHabitStats,
    getHabitDetails,
    updateHabit,
    toggleHabit,
    deleteHabit,
  };
});
