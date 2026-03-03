import { defineStore } from 'pinia';
import { HabitService } from '@/services/HabitService';
import { NotificationService } from '@/services/NotificationService';

export const useHabitStore = defineStore('habit', () => {
  const createHabit = async (title, weekDays, reminderTime) => {
    const response = await HabitService.create(title, weekDays, reminderTime);
    
    const data = response.data;
    
    if (data.id && reminderTime) {
      await NotificationService.scheduleHabitNotifications({
        id: data.id,
        title: data.title,
        week_days: weekDays,
        reminder_time: reminderTime,
      });
    }

    return response.data;
  };

  const getDayInfo = async (date) => {
    const response = await HabitService.getDayInfo(date);
    return response.data;
  };

  const getHabitsSummary = async (date = null) => {
    const response = await HabitService.getSummary(date);
    return response.data;
  };

  const fetchAllHabits = async () => {
    const response = await HabitService.getAllHabits();
    return response.data;
  };

  const getHabitStats = async (period, date = null) => {
    const response = await HabitService.getStats(period, date);
    return response.data;
  };

  const getHabitDetails = async (id) => {
    const response = await HabitService.getDetails(id);
    return response.data;
  };

  const updateHabit = async (id, title, weekDays, reminder_time) => {
    const response = await HabitService.update(id, title, weekDays, reminder_time);
    
    if (id) {
      if (reminder_time) {
        await NotificationService.scheduleHabitNotifications({
          id: id,
          title: title,
          week_days: weekDays,
          reminder_time: reminder_time,
        });
      } else {
        await NotificationService.cancelHabitNotifications(id);
      }
    }

    return response.data;
  };

  const toggleHabit = async (habitId, date = null) => {
    const response = await HabitService.toggle(habitId, date);
    return response.data;
  };

  const deleteHabit = async (id) => {
    await NotificationService.cancelHabitNotifications(id);
    return await HabitService.delete(id);
  };

  return {
    createHabit,
    getDayInfo,
    getHabitsSummary,
    fetchAllHabits,
    getHabitStats,
    getHabitDetails,
    updateHabit,
    toggleHabit,
    deleteHabit,
  };
});
