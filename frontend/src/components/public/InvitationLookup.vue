<template>
  <v-card rounded="xl" class="pa-6 invitation-card">
    <div class="text-overline mb-2">Open Your Personal Invitation</div>
    <v-text-field
      v-model="keyword"
      label="Enter your name or invitation code"
      variant="outlined"
      prepend-inner-icon="mdi-account-search"
    />
    <v-btn color="primary" size="large" block @click="submitLookup">Find Invitation</v-btn>

    <div v-if="result" class="mt-6">
      <v-alert :type="result.found ? 'success' : 'error'" variant="tonal">
        {{ result.found ? `${result.guest.name} is invited.` : 'No invitation found.' }}
      </v-alert>

      <div v-if="result.found" class="mt-4">
        <p class="mb-2">Allowed guests: {{ result.guest.allowed_guests }}</p>
        <p class="mb-4">Current RSVP: {{ result.guest.rsvp_status }}</p>
        <v-btn color="secondary" block @click="openInvitation">Open My Invitation</v-btn>
      </div>
    </div>
  </v-card>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../lib/api';

const keyword = ref('');
const result = ref(null);
const router = useRouter();

const submitLookup = async () => {
  const { data } = await api.post('/public/check-invitation', {
    keyword: keyword.value,
  });

  result.value = data;
};

const openInvitation = () => {
  if (!result.value?.guest?.invitation_slug) return;
  router.push(`/invite/${result.value.guest.invitation_slug}`);
};
</script>
