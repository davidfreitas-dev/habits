import { createRouter, createWebHistory } from '@ionic/vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
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
    meta: { requiresAuth: true }
  },
  {
    path: '/day/:date',
    name: 'Day',
    component: () => import('@/views/Day.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/habit/:id?',
    name: 'Habit',
    component: () => import('@/views/Habit.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('@/views/Settings.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/views/Profile.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/password-change',
    name: 'PasswordChange',
    component: () => import('@/views/PasswordChange.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/delete-account',
    name: 'DeleteAccount',
    component: () => import('@/views/DeleteAccount.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('@/views/About.vue')
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  if (!to.meta.requiresAuth) {
    return next();
  }

  if (authStore.isAuthenticated) {
    return next();
  }

  const refreshed = await authStore.refreshAccessToken();
  
  if (refreshed) {
    next();
  } else {
    next('/signin');
  }
});

export default router;