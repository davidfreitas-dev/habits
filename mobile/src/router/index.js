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
      path: '/habit/:date',
      name: 'Habit',
      component: () => import('@/views/Habit.vue'),
      meta: { 
        requiresAuth: true 
      }
    },
    {
      path: '/new',
      name: 'New',
      component: () => import('@/views/New.vue'),
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
