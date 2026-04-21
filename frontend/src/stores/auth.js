import { defineStore } from 'pinia';
import api from '../lib/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('auth_token'),
    user: JSON.parse(localStorage.getItem('auth_user') || 'null'),
  }),
  actions: {
    async login(payload) {
      const { data } = await api.post('/login', payload);
      this.token = data.token;
      this.user = data.user;
      localStorage.setItem('auth_token', data.token);
      localStorage.setItem('auth_user', JSON.stringify(data.user));
    },
    async logout() {
      if (this.token) {
        await api.post('/logout');
      }

      this.token = null;
      this.user = null;
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
    },
  },
});

