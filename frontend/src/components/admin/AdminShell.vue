<template>
  <v-layout class="admin-shell">
    <v-navigation-drawer permanent color="#123528" theme="dark">
      <div class="pa-5 text-white">
        <div class="text-overline">Wedding Admin</div>
        <div class="text-h6">Invitation Manager</div>
      </div>
      <v-list nav bg-color="transparent">
        <v-list-item title="Dashboard" to="/admin/dashboard" prepend-icon="mdi-view-dashboard" />
        <v-list-item title="Guests" to="/admin/guests" prepend-icon="mdi-account-group" />
        <v-list-item title="Settings" to="/admin/settings" prepend-icon="mdi-cog" />
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container class="py-8">
        <div class="d-flex justify-space-between align-center mb-6">
          <div>
            <div class="text-overline">Wedding Admin</div>
            <h1 class="text-h4">{{ title }}</h1>
          </div>
          <v-btn variant="outlined" @click="handleLogout">Logout</v-btn>
        </div>

        <slot />
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

defineProps({
  title: {
    type: String,
    required: true,
  },
});

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/admin/login');
};
</script>
