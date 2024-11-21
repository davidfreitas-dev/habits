import { createRouter, createWebHistory } from '@ionic/vue-router';
import { useSessionStore } from '@/stores/session';

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
      path: '/forgot',
      name: 'Forgot',
      component: () => import('@/views/auth/Forgot.vue'),
    },
    {
      path: '/forgot/token',
      name: 'Token',
      component: () => import('@/views/auth/Token.vue'),
    },
    {
      path: '/forgot/reset',
      name: 'Reset',
      component: () => import('@/views/auth/Reset.vue'),
      props: route => ({ data: route.query.data })
    },
    {
      path: '/',
      redirect: '/home'
    },
    {
      path: '/home',
      name: 'Home',
      component: () => import('@/views/Home.vue'),
      meta: { 
        requiresAuth: true 
      }
    },
    {
      path: '/day/:date',
      name: 'Day',
      component: () => import('@/views/Day.vue'),
      meta: { 
        requiresAuth: true 
      }
    },
    {
      path: '/habit',
      name: 'Habit',
      component: () => import('@/views/Habit.vue'),
      meta: { 
        requiresAuth: true 
      }
    },
    {
      path: '/settings',
      name: 'Settings',
      component: () => import('@/views/Settings.vue'),
      meta: { 
        requiresAuth: true 
      }
    }
  ]
});

router.beforeEach((to, from, next) => {
  const storeSession = useSessionStore();

  const invalidSession = !storeSession.session || !storeSession.session.token;

  if (to.meta.requiresAuth && invalidSession) {
    next('/signin');
  } else {
    next();
  }
});

export default router;
