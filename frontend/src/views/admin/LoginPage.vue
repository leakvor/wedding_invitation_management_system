<template>
  <v-container fluid class="fill-height auth-page">
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card rounded="xl" class="pa-6">
          <div class="text-overline mb-2">Admin Access</div>
          <h1 class="text-h4 mb-6">Wedding Dashboard Login</h1>
          <v-form @submit.prevent="submit">
            <v-text-field v-model="form.email" label="Email" variant="outlined" />
            <v-text-field v-model="form.password" label="Password" type="password" variant="outlined" />
            <v-btn block color="primary" size="large" type="submit" :loading="loading">Login</v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const form = reactive({
  email: 'admin@example.com',
  password: 'password',
});

const submit = async () => {
  loading.value = true;
  try {
    await authStore.login(form);
    router.push('/admin/dashboard');
  } finally {
    loading.value = false;
  }
};
</script>

