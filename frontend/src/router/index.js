import { createRouter, createWebHistory } from 'vue-router';
import DashboardPage from '../views/admin/DashboardPage.vue';
import GuestListPage from '../views/admin/GuestListPage.vue';
import LoginPage from '../views/admin/LoginPage.vue';
import SettingsPage from '../views/admin/SettingsPage.vue';
import GuestInvitationPage from '../views/public/GuestInvitationPage.vue';
import PublicInvitationPage from '../views/public/PublicInvitationPage.vue';
import { useAuthStore } from '../stores/auth';

const routes = [
  { path: '/', name: 'public', component: PublicInvitationPage },
  { path: '/invite/:slug', name: 'guest-invitation', component: GuestInvitationPage },
  { path: '/admin/login', name: 'login', component: LoginPage },
  {
    path: '/admin/dashboard',
    name: 'dashboard',
    component: DashboardPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/guests',
    name: 'guests',
    component: GuestListPage,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/settings',
    name: 'settings',
    component: SettingsPage,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.token) {
    return { name: 'login' };
  }

  if (to.name === 'login' && authStore.token) {
    return { name: 'dashboard' };
  }

  return true;
});

export default router;
