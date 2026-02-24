import { defineStore } from 'pinia';
import { HabitService } from '@/services/HabitService';
import { NotificationService } from '@/services/NotificationService';

export const useHabitStore = defineStore('habit', () => {
  const createHabit = async (title, weekDays, reminder_time) => {
    const data = await HabitService.create(title, weekDays, reminder_time);
    
    // Schedule notifications for the new habit
    if (data.id && reminder_time) {
      await NotificationService.scheduleHabitNotifications({
        id: data.id,
        title: data.title, // Use title from response for consistency
        week_days: weekDays,
        reminder_time: reminder_time,
      });
    }

    return data.data;
  };

  const getDayInfo = async (date) => {
    const data = await HabitService.getDayInfo(date);
    return data.data;
  };

  const getHabitsSummary = async (date = null) => {
    const data = await HabitService.getSummary(date);
    return data.data;
  };

  const fetchAllHabits = async () => {
    const data = await HabitService.getAllHabits();
    return data.data;
  };

  const getHabitStats = async (period, date = null) => {
    const data = await HabitService.getStats(period, date);
    return data.data;
  };

  const getHabitDetails = async (id) => {
    const data = await HabitService.getDetails(id);
    return data.data;
  };

  const updateHabit = async (id, title, weekDays, reminder_time) => {
    const response = await HabitService.update(id, title, weekDays, reminder_time);
    
    // Re-schedule notifications for the updated habit
    if (id) {
      if (reminder_time) {
        await NotificationService.scheduleHabitNotifications({
          id: id,
          title: title,
          week_days: weekDays,
          reminder_time: reminder_time,
        });
      } else {
        // If reminder_time is null, cancel any existing notifications for this habit
        await NotificationService.cancelHabitNotifications(id);
      }
    }

    return response;
  };

  const toggleHabit = async (habitId, date = null) => {
    return await HabitService.toggle(habitId, date);
  };

  const deleteHabit = async (id) => {
    // Cancel all notifications for this habit before deleting
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
