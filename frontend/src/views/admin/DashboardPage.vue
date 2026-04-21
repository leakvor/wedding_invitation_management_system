<template>
  <AdminShell title="Dashboard">
    <v-row>
      <v-col v-for="card in cards" :key="card.label" cols="12" md="4">
        <v-card rounded="xl" class="pa-6 stat-card">
          <div class="text-overline">{{ card.label }}</div>
          <div class="text-h3 mt-2">{{ card.value }}</div>
        </v-card>
      </v-col>
    </v-row>

    <v-card rounded="xl" class="mt-6 pa-6">
      <h2 class="text-h5 mb-4">Recent Gift Updates</h2>
      <v-table>
        <thead>
          <tr>
            <th>Guest</th>
            <th>Previous</th>
            <th>New</th>
            <th>Updated At</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in stats.recent_money_logs || []" :key="log.id">
            <td>{{ log.guest?.name }}</td>
            <td>{{ log.previous_amount }}</td>
            <td>{{ log.new_amount }}</td>
            <td>{{ log.created_at }}</td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </AdminShell>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue';
import AdminShell from '../../components/admin/AdminShell.vue';
import api from '../../lib/api';

const stats = reactive({});

const cards = computed(() => [
  { label: 'Total Invited', value: stats.total_invited ?? 0 },
  { label: 'Total Attended', value: stats.total_attended ?? 0 },
  { label: 'Total Money', value: stats.total_money ?? 0 },
]);

onMounted(async () => {
  const { data } = await api.get('/stats');
  Object.assign(stats, data);
});
</script>

