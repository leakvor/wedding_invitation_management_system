<template>
  <AdminShell title="Guest Management">
    <v-card rounded="xl" class="pa-4 mb-4">
      <div class="d-flex flex-wrap ga-4 align-center">
        <GuestSearchBar v-model="search" />
        <v-select
          v-model="status"
          :items="['', 'invited', 'confirmed', 'attended']"
          label="Filter status"
          variant="outlined"
          style="max-width: 220px"
          hide-details
        />
        <v-btn color="primary" @click="openCreateDialog">Add Guest</v-btn>
        <v-btn variant="outlined" @click="exportGuests">Export CSV</v-btn>
      </div>
    </v-card>

    <GuestTable
      :items="guests"
      :money-drafts="moneyDrafts"
      @edit="openEditDialog"
      @delete="deleteGuest"
      @money-change="updateMoneyDraft"
      @save-money="saveMoney"
      @share="openShareDialog"
    />

    <GuestFormDialog
      v-model:open="dialog"
      v-model:form="form"
      @save="saveGuest"
    />

    <v-dialog v-model="shareDialog" max-width="520">
      <v-card rounded="xl" class="pa-4">
        <v-card-title>Invitation Link & QR</v-card-title>
        <v-card-text>
          <p class="mb-3">{{ shareData.guestName }}</p>
          <v-text-field :model-value="shareData.url" label="Share URL" readonly variant="outlined" />
          <div class="d-flex justify-center my-4">
            <v-img v-if="shareData.qrUrl" :src="shareData.qrUrl" max-width="220" />
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="shareDialog = false">Close</v-btn>
          <v-btn color="primary" @click="copyShareUrl">Copy Link</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AdminShell>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import AdminShell from '../../components/admin/AdminShell.vue';
import GuestFormDialog from '../../components/admin/GuestFormDialog.vue';
import GuestSearchBar from '../../components/admin/GuestSearchBar.vue';
import GuestTable from '../../components/admin/GuestTable.vue';
import api from '../../lib/api';

const guests = ref([]);
const search = ref('');
const status = ref('');
const dialog = ref(false);
const shareDialog = ref(false);
const moneyDrafts = reactive({});
const shareData = reactive({
  url: '',
  qrUrl: '',
  guestName: '',
});
const form = reactive(defaultForm());

function defaultForm() {
  return {
    id: null,
    name: '',
    phone: '',
    invitation_code: '',
    status: 'invited',
    rsvp_status: 'pending',
    allowed_guests: 1,
    notes: '',
  };
}

const fetchGuests = async () => {
  const { data } = await api.get('/guests', {
    params: { search: search.value, status: status.value },
  });

  guests.value = data.data;
};

const openCreateDialog = () => {
  Object.assign(form, defaultForm());
  dialog.value = true;
};

const openEditDialog = (guest) => {
  Object.assign(form, { ...guest });
  dialog.value = true;
};

const saveGuest = async (payload) => {
  if (payload.id) {
    await api.put(`/guests/${payload.id}`, payload);
  } else {
    await api.post('/guests', payload);
  }

  dialog.value = false;
  await fetchGuests();
};

const deleteGuest = async (guest) => {
  await api.delete(`/guests/${guest.id}`);
  await fetchGuests();
};

const updateMoneyDraft = (guest, amount) => {
  moneyDrafts[guest.id] = amount;
};

const saveMoney = async (guest) => {
  await api.post(`/guests/${guest.id}/money`, {
    amount: Number(moneyDrafts[guest.id] ?? guest.money ?? 0),
  });

  await fetchGuests();
};

const openShareDialog = async (guest) => {
  const { data } = await api.get(`/guests/${guest.id}/share`);
  shareData.url = data.url;
  shareData.qrUrl = data.qr_url;
  shareData.guestName = guest.name;
  shareDialog.value = true;
};

const copyShareUrl = async () => {
  await navigator.clipboard.writeText(shareData.url);
};

const exportGuests = async () => {
  const response = await api.get('/guests/export', {
    responseType: 'blob',
  });

  const blob = new Blob([response.data], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'guests.csv';
  link.click();
  window.URL.revokeObjectURL(url);
};

watch([search, status], fetchGuests);
onMounted(fetchGuests);
</script>
