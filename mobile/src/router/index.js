import { createRouter, createWebHistory } from '@ionic/vue-router';
import Home from '../views/Home.vue';
import Habit from '../views/Habit.vue';
import New from '../views/New.vue';

const routes = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'Home',
    component: Home
  },
  {
    path: '/habit/:date',
    name: 'Habit',
    component: Habit
  },
  {
    path: '/new',
    name: 'New',
    component: New
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

export default router;
