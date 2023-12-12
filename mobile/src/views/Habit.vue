<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar class="ion-safe-area-top">
        <BackButton />
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <Loading v-if="isLoading" />
      
      <div
        v-else
        id="container"
      >        
        <Breadcrumb
          :week-day="dayOfWeek"
          :date="dayAndMonth"
        />

        <ProgressBar :progress="progressPercentage" />

        <Checkbox
          v-for="habit in dayInfo.possibleHabits"
          :key="habit.id"
          :label="habit.data.title"
          :is-checked="isHabitChecked(habit.id)"
          :is-disabled="isDateInPast"
          @handle-checkbox-change="handleToggleHabit(habit.id)"
        />

        <div
          v-if="isDateInPast"
          class="message"
        >
          Você não pode editar hábitos de uma data passada.
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { IonPage, IonHeader, IonToolbar, IonContent } from '@ionic/vue';
import { FirebaseFirestore } from '@capacitor-firebase/firestore';
import BackButton from '@/components/BackButton.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
import ProgressBar from '@/components/ProgressBar.vue';
import Checkbox from '@/components/Checkbox.vue';
import Loading from '@/components/Loading.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

const route = useRoute();
const isLoading = ref(true);
const date = ref(route.params.date);
const parsedDate = ref(dayjs(date.value).startOf('day'));
const weekDay = ref(parsedDate.value.day());
const dayOfWeek = ref(parsedDate.value.format('dddd'));
const dayAndMonth = ref(parsedDate.value.format('DD/MM'));
const isDateInPast = ref(dayjs(date.value).endOf('day').isBefore(new Date));
const day = ref(null);
const dayInfo = ref({
  possibleHabits: [],
  completedHabits: []
});

const getPossibleHabits = async () => {
  const { snapshots } = await FirebaseFirestore.getCollection({
    reference: 'habits',
    compositeFilter: {
      type: 'and',
      queryConstraints: [
        {
          type: 'where',
          fieldPath: 'created_at',
          opStr: '<=',
          value: parsedDate.value.toDate(),
        },
        {
          type: 'where',
          fieldPath: 'week_days',
          opStr: 'array-contains',
          value: weekDay.value,
        },
      ],
    },
  });

  dayInfo.value.possibleHabits = snapshots;
};

const getCompletedHabits = async () => {
  day.value = await getDay();

  if (!day.value) return;

  await FirebaseFirestore.addCollectionSnapshotListener(
    {
      reference: 'day_habits',
      compositeFilter: {
        type: 'and',
        queryConstraints: [
          {
            type: 'where',
            fieldPath: 'day_id',
            opStr: '==',
            value: day.value.id,
          }
        ],
      },
    },
    (event, error) => {
      if (error) {
        console.error(error);
      } else {
        dayInfo.value.completedHabits = event.snapshots;
      }
    }
  );
};

const getDay = async () => {
  const date = parsedDate.value.toDate();
  const dayAfter = dayjs(date.value).add(1, 'day').startOf('day').toDate();  
  const { snapshots } = await FirebaseFirestore.getCollection({
    reference: 'days',
    compositeFilter: {
      type: 'and',
      queryConstraints: [
        {
          type: 'where',
          fieldPath: 'date',
          opStr: '>=',
          value: date,
        },
        {
          type: 'where',
          fieldPath: 'date',
          opStr: '<',
          value: dayAfter,
        }
      ],
    },
  });

  return snapshots[0];
};

onMounted(async () => {
  await getPossibleHabits();
  await getCompletedHabits();  
  isLoading.value = false;
});

const getDayHabit = async (dayid, habitid) => {
  const { snapshots } = await FirebaseFirestore.getCollection({
    reference: 'day_habits',
    compositeFilter: {
      type: 'and',
      queryConstraints: [
        {
          type: 'where',
          fieldPath: 'day_id',
          opStr: '==',
          value: dayid,
        },
        {
          type: 'where',
          fieldPath: 'habit_id',
          opStr: '==',
          value: habitid,
        }
      ],
    },
  });

  return snapshots[0];
};

const handleToggleHabit = async (habitid) => {
  if (!day.value) {
    const today = dayjs().startOf('day').toDate();

    await FirebaseFirestore.addDocument({
      reference: 'days',
      data: { 
        date: today,
      },
    });

    await getCompletedHabits();
  }

  const dayHabit = await getDayHabit(day.value.id, habitid);

  if (dayHabit) {
    await FirebaseFirestore.deleteDocument({
      reference: `day_habits/${dayHabit.id}`,
    });
  } else {
    await FirebaseFirestore.addDocument({
      reference: 'day_habits',
      data: { 
        day_id: day.value.id, 
        habit_id: habitid
      },
    });
  }
};

const isHabitChecked = (habitid) => {
  return dayInfo.value.completedHabits.some(habit => habit.data.habit_id === habitid);
};

const progressPercentage = computed(() => {
  return dayInfo.value.completedHabits.length
    ? Math.round((dayInfo.value.completedHabits.length / dayInfo.value.possibleHabits.length) * 100)
    : 0;
});
</script>

<style>
div.message {
  font-size: .85rem;
  text-align: center;
  color: var(--font);
  margin-top: 2.5rem;
}
</style>