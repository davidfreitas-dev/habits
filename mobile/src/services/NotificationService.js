import { LocalNotifications } from '@capacitor/local-notifications';

const generateNotificationId = (habitId, weekday) => {
  return parseInt(`${habitId}${weekday}`);
};

export const NotificationService = {
  async scheduleHabitNotifications(habit) {
    await this.cancelHabitNotifications(habit.id);

    if (!habit.reminder_time || !habit.week_days || habit.week_days.length === 0) {
      return;
    }

    const [hour, minute] = habit.reminder_time.split(':').map(Number);
    const notifications = [];

    for (const weekDay of habit.week_days) {
      const notificationId = generateNotificationId(habit.id, weekDay);
      
      notifications.push({
        id: notificationId,
        title: 'Hábito Pendente!',
        body: `Não se esqueça do seu hábito: "${habit.title}"`,
        schedule: {
          on: {
            weekday: weekDay === 0 ? 7 : weekDay,
            hour,
            minute,
          },
          repeats: true,
        },
        smallIcon: 'res://icon',
        sound: 'default',
      });
    }

    if (notifications.length > 0) {
      await LocalNotifications.schedule({ notifications });
    }
  },

  async cancelHabitNotifications(habitId) {
    const notificationsToCancel = [];
    for (let i = 0; i <= 6; i++) {
      notificationsToCancel.push({ id: generateNotificationId(habitId, i) });
    }
    
    await LocalNotifications.cancel({ notifications: notificationsToCancel });
  },

  async rescheduleAllNotifications(habits) {
    const pending = await LocalNotifications.getPending();
    if (pending.notifications.length > 0) {
      await LocalNotifications.cancel(pending);
    }

    for (const habit of habits) {
      await this.scheduleHabitNotifications(habit);
    }
  }
};
