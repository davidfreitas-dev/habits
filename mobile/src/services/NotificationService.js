import { LocalNotifications } from '@capacitor/local-notifications';

const generateNotificationId = (habitId, weekday) => {
  return parseInt(`${habitId}${weekday}`);
};

const titles = [
  'Ei, psiu! ✨',
  'Bora focar? 💪',
  'Hora do show! 🚀',
  'Olha só quem chegou... 👀',
  'Momento Habitus! 🌿'
];

const messages = [
  'Que tal dar aquele check em "{title}" agora?',
  'O seu eu do futuro vai te agradecer por fazer "{title}"!',
  'Nada de preguiça! Vamos de "{title}"? 🔥',
  'Passando para te lembrar do seu compromisso: "{title}".',
  'Um passo de cada vez! Hora de "{title}".'
];

const getRandomItem = (array) => array[Math.floor(Math.random() * array.length)];

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
      const title = getRandomItem(titles);
      const body = getRandomItem(messages).replace('{title}', habit.title);
      
      notifications.push({
        id: notificationId,
        title,
        body,
        schedule: {
          on: {
            weekday: weekDay === 0 ? 7 : weekDay,
            hour,
            minute,
          },
          repeats: true,
        },
        largeIcon: 'ic_stat_habitus',
        smallIcon: 'ic_stat_habitus',
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

