import { ref, computed } from 'vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

export function useParsedDate(dateInput) {
  const parsedDate = ref(dayjs(dateInput).startOf('day'));
  const dayOfWeek = computed(() => parsedDate.value.format('dddd'));
  const dayAndMonth = computed(() => parsedDate.value.format('DD/MM'));
  const isDateInPast = computed(() => dayjs(dateInput).endOf('day').isBefore(new Date()));

  return {
    parsedDate,
    dayOfWeek,
    dayAndMonth,
    isDateInPast
  };
}
