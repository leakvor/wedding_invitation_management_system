<template>
  <v-data-table
    :headers="headers"
    :items="items"
    item-value="id"
    class="elevation-1 rounded-xl"
  >
    <template #item.status="{ item }">
      <v-chip size="small" :color="statusColor(item.status)">{{ item.status }}</v-chip>
    </template>

    <template #item.money="{ item }">
      <v-text-field
        :model-value="moneyDrafts[item.id] ?? item.money"
        density="compact"
        hide-details
        variant="underlined"
        type="number"
        @update:model-value="$emit('money-change', item, $event)"
      />
    </template>

    <template #item.actions="{ item }">
      <div class="d-flex ga-2 flex-wrap">
        <v-btn size="small" variant="tonal" @click="$emit('edit', item)">Edit</v-btn>
        <v-btn size="small" color="primary" @click="$emit('save-money', item)">Save</v-btn>
        <v-btn size="small" color="secondary" variant="outlined" @click="$emit('share', item)">Link / QR</v-btn>
        <v-btn size="small" color="error" variant="text" @click="$emit('delete', item)">Delete</v-btn>
      </div>
    </template>
  </v-data-table>
</template>

<script setup>
defineEmits(['edit', 'delete', 'money-change', 'save-money', 'share']);

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
  moneyDrafts: {
    type: Object,
    default: () => ({}),
  },
});

const headers = [
  { title: 'Name', key: 'name' },
  { title: 'Code', key: 'invitation_code' },
  { title: 'Status', key: 'status' },
  { title: 'Guests', key: 'allowed_guests' },
  { title: 'Money', key: 'money' },
  { title: 'Actions', key: 'actions', sortable: false },
];

const statusColor = (status) => {
  if (status === 'attended') return 'success';
  if (status === 'confirmed') return 'secondary';
  return 'grey';
};
</script>
