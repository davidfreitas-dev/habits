import api from '@/api';
import { HABIT_ENDPOINTS } from '@/constants/endpoints';

export const HabitService = {
  create(title, weekDays) {
    return api.post(HABIT_ENDPOINTS.BASE, {
      title,
      week_days: weekDays,
    });
  },
  
  getDayInfo(date) {
    return api.get(HABIT_ENDPOINTS.DAY, {
      params: { date },
    });
  },
  
  getSummary() {
    return api.get(HABIT_ENDPOINTS.SUMMARY);
  },
  
  getStats(period = 'W') {
    return api.get(HABIT_ENDPOINTS.STATS, {
      params: { period }
    });
  },
  
  getDetails(id) {
    return api.get(HABIT_ENDPOINTS.DETAILS(id));
  },
  
  update(id, title, weekDays) {
    return api.put(HABIT_ENDPOINTS.DETAILS(id), {
      title,
      week_days: weekDays,
    });
  },
  
  toggle(habitId) {
    return api.patch(HABIT_ENDPOINTS.TOGGLE(habitId));
  },
  
  delete(id) {
    return api.delete(HABIT_ENDPOINTS.DETAILS(id));
  }
};
