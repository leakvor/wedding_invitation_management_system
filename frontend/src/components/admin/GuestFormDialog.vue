<template>
  <v-dialog v-model="open" max-width="680">
    <v-card rounded="xl">
      <v-card-title>{{ form.id ? 'Edit Guest' : 'Add Guest' }}</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field v-model="form.name" label="Name" variant="outlined" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field v-model="form.phone" label="Phone" variant="outlined" />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field v-model="form.invitation_code" label="Invitation Code" variant="outlined" />
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              v-model="form.status"
              :items="['invited', 'confirmed', 'attended']"
              label="Status"
              variant="outlined"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              v-model="form.rsvp_status"
              :items="['pending', 'yes', 'no', 'coming']"
              label="RSVP"
              variant="outlined"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model.number="form.allowed_guests"
              label="Allowed Guests"
              type="number"
              min="1"
              variant="outlined"
            />
          </v-col>
          <v-col cols="12">
            <v-textarea
              v-model="form.notes"
              label="Admin Notes"
              variant="outlined"
              rows="3"
            />
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="open = false">Cancel</v-btn>
        <v-btn color="primary" @click="$emit('save', form)">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
defineEmits(['save']);

const open = defineModel('open', { type: Boolean, default: false });
const form = defineModel('form', { type: Object, required: true });
</script>
