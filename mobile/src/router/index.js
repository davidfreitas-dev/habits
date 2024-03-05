import { createRouter, createWebHistory } from '@ionic/vue-router';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/signup',
      name: 'Signup',
      component: () => import('@/views/auth/Signup.vue'),
    },
    {
      path: '/signin',
      name: 'Signin',
      component: () => import('@/views/auth/Signin.vue'),
    },
    {
      path: '/',
      redirect: '/home'
    },
    {
      path: '/home',
      name: 'Home',
      component: () => import('@/views/Home.vue'),
    },
    {
      path: '/habit/:date',
      name: 'Habit',
      component: () => import('@/views/Habit.vue'),
    },
    {
      path: '/new',
      name: 'New',
      component: () => import('@/views/New.vue'),
    }
  ]
});

export default router;
