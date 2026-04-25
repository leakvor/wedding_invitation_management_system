<template>
  <AdminShell title="Invitation Settings">
    <div class="settings-page">
      <v-row class="mb-4">
        <v-col cols="12" lg="8">
          <v-card rounded="xl" class="settings-hero pa-6">
            <div class="d-flex flex-wrap justify-space-between align-center ga-4">
              <div>
                <div class="text-overline">Wedding Website Control</div>
                <h2 class="text-h5 mb-2">Edit your invitation content in one place</h2>
                <p class="text-medium-emphasis mb-0">
                  Update cover text, bilingual content, media, event schedule, and gallery without digging through repeated fields.
                </p>
              </div>

              <div class="d-flex flex-wrap ga-3 align-center">
                <v-chip color="primary" variant="tonal">{{ activeLanguageLabel }}</v-chip>
                <v-select
                  v-model="form.background_mode"
                  :items="backgroundOptions"
                  label="Background"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  class="settings-toolbar-select"
                />
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" lg="4">
          <v-card rounded="xl" class="settings-summary pa-6">
            <div class="text-overline mb-3">Quick Summary</div>
            <div class="settings-summary__item">
              <span>Timeline events</span>
              <strong>{{ events.length }}</strong>
            </div>
            <div class="settings-summary__item">
              <span>Gallery images</span>
              <strong>{{ images.length }}</strong>
            </div>
            <div class="settings-summary__item">
              <span>Public theme</span>
              <strong>{{ form.background_mode }}</strong>
            </div>
            <v-btn color="primary" block size="large" class="mt-4" @click="save">Save Settings</v-btn>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" xl="8">
          <v-card rounded="xl" class="settings-card pa-6 mb-4">
            <div class="settings-card__header">
              <div>
                <div class="text-overline">Language Content</div>
                <h3 class="text-h6">Invitation Text</h3>
              </div>
              <v-tabs v-model="languageTab" color="primary" density="comfortable">
                <v-tab value="en">English</v-tab>
                <v-tab value="km">Khmer</v-tab>
              </v-tabs>
            </div>

            <v-row class="mt-1">
              <v-col cols="12" md="6">
                <v-text-field v-model="form.bride_name" label="Bride Name" variant="outlined" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.groom_name" label="Groom Name" variant="outlined" />
              </v-col>
            </v-row>

            <v-window v-model="languageTab">
              <v-window-item value="en">
                <v-row>
                  <v-col cols="12">
                    <v-textarea v-model="form.hero_message" label="Hero Message" variant="outlined" rows="3" />
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="form.cover_title_en" label="Hero Heading" variant="outlined" />
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="form.closing_message_en" label="Closing Message" variant="outlined" rows="3" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="form.venue_name" label="Venue Name" variant="outlined" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="form.venue_address" label="Venue Address" variant="outlined" />
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="form.cover_subtitle_en" label="Cover Subtitle" variant="outlined" rows="3" />
                  </v-col>
                </v-row>
              </v-window-item>

              <v-window-item value="km">
                <v-row>
                  <v-col cols="12">
                    <v-textarea v-model="form.hero_message_km" label="Hero Message Khmer" variant="outlined" rows="3" />
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="form.cover_title_km" label="Hero Heading Khmer" variant="outlined" />
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="form.closing_message_km" label="Closing Message Khmer" variant="outlined" rows="3" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="form.venue_name_km" label="Venue Name Khmer" variant="outlined" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="form.venue_address_km" label="Venue Address Khmer" variant="outlined" />
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="form.cover_subtitle_km" label="Cover Subtitle Khmer" variant="outlined" rows="3" />
                  </v-col>
                </v-row>
              </v-window-item>
            </v-window>

            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.google_map_url" label="Google Map URL" variant="outlined" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.calendar_url" label="Calendar URL" variant="outlined" />
              </v-col>
            </v-row>
          </v-card>

          <v-card rounded="xl" class="settings-card pa-6 mb-4">
            <div class="settings-card__header">
              <div>
                <div class="text-overline">Timeline</div>
                <h3 class="text-h6">Event Schedule</h3>
              </div>
              <v-btn color="primary" variant="tonal" prepend-icon="mdi-plus" @click="addEvent">Add Event</v-btn>
            </div>

            <div v-if="events.length" class="settings-list">
              <v-card
                v-for="(event, index) in events"
                :key="`event-${index}`"
                rounded="xl"
                variant="outlined"
                class="settings-list__item pa-4"
              >
                <div class="d-flex justify-space-between align-center mb-4">
                  <div class="font-weight-medium">Event {{ index + 1 }}</div>
                  <v-btn icon="mdi-delete" color="error" variant="text" @click="removeEvent(index)" />
                </div>
                <v-row>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="event.title" label="Title" variant="outlined" />
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="event.title_km" label="Title Khmer" variant="outlined" />
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="event.event_date" label="Date & Time" variant="outlined" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-textarea v-model="event.description" label="Description" variant="outlined" rows="2" />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-textarea v-model="event.description_km" label="Description Khmer" variant="outlined" rows="2" />
                  </v-col>
                </v-row>
              </v-card>
            </div>

            <v-alert v-else type="info" variant="tonal" class="mt-4">
              No events yet. Add your first ceremony timeline item.
            </v-alert>
          </v-card>

          <v-card rounded="xl" class="settings-card pa-6">
            <div class="settings-card__header">
              <div>
                <div class="text-overline">Gallery</div>
                <h3 class="text-h6">Photo Collection</h3>
              </div>
              <v-btn color="primary" variant="tonal" prepend-icon="mdi-plus" @click="addImage">Add Image</v-btn>
            </div>

            <div v-if="images.length" class="settings-gallery-grid">
              <v-card
                v-for="(image, index) in images"
                :key="`image-${index}`"
                rounded="xl"
                variant="outlined"
                class="settings-gallery-card pa-4"
              >
                <div class="d-flex justify-space-between align-center mb-4">
                  <div class="font-weight-medium">Image {{ index + 1 }}</div>
                  <v-btn icon="mdi-delete" color="error" variant="text" @click="removeImage(index)" />
                </div>

                <v-img
                  v-if="image.url"
                  :src="image.url"
                  height="180"
                  cover
                  class="rounded-lg mb-4"
                />
                <div v-else class="settings-gallery-card__placeholder mb-4">No image uploaded</div>

                <v-file-input
                  label="Upload Image"
                  accept="image/*"
                  prepend-icon="mdi-upload"
                  variant="outlined"
                  @update:model-value="uploadGalleryImage($event, index)"
                />
                <v-text-field v-model="image.url" label="Image URL" variant="outlined" class="mt-3" />
                <v-text-field v-model="image.caption" label="Caption" variant="outlined" class="mt-3" />
              </v-card>
            </div>

            <v-alert v-else type="info" variant="tonal" class="mt-4">
              No gallery images yet. Upload the first wedding photo.
            </v-alert>
          </v-card>
        </v-col>

        <v-col cols="12" xl="4">
          <v-card rounded="xl" class="settings-card pa-6 mb-4">
            <div class="settings-card__header">
              <div>
                <div class="text-overline">Media</div>
                <h3 class="text-h6">Cover & Couple Photos</h3>
              </div>
            </div>

            <div class="settings-media-stack">
              <div class="settings-media-block">
                <div class="text-subtitle-2 mb-2">Cover Background</div>
                <v-img v-if="form.cover_image_url" :src="form.cover_image_url" height="180" cover class="rounded-lg mb-3" />
                <v-file-input
                  label="Upload Cover Background"
                  accept="image/*"
                  prepend-icon="mdi-image"
                  variant="outlined"
                  @update:model-value="uploadSettingImage($event, 'cover', 'cover_image_url')"
                />
              </div>

              <div class="settings-media-block">
                <div class="text-subtitle-2 mb-2">Main Couple Image</div>
                <v-img v-if="form.primary_image_url" :src="form.primary_image_url" height="180" cover class="rounded-lg mb-3" />
                <v-file-input
                  label="Upload Main Couple Image"
                  accept="image/*"
                  prepend-icon="mdi-account-box"
                  variant="outlined"
                  @update:model-value="uploadSettingImage($event, 'profile', 'primary_image_url')"
                />
              </div>

              <div class="settings-media-block">
                <div class="text-subtitle-2 mb-2">Map Preview Image</div>
                <v-img v-if="form.map_image_url" :src="form.map_image_url" height="180" cover class="rounded-lg mb-3" />
                <v-file-input
                  label="Upload Map Preview"
                  accept="image/*"
                  prepend-icon="mdi-map"
                  variant="outlined"
                  @update:model-value="uploadSettingImage($event, 'cover', 'map_image_url')"
                />
              </div>
            </div>
          </v-card>

          <v-card rounded="xl" class="settings-card pa-6 mb-4">
            <div class="settings-card__header">
              <div>
                <div class="text-overline">Music</div>
                <h3 class="text-h6">Wedding Audio</h3>
              </div>
            </div>

            <v-file-input
              label="Upload Wedding Song"
              accept="audio/*"
              prepend-icon="mdi-music"
              variant="outlined"
              @update:model-value="uploadSettingFile($event, 'audio', 'music_url')"
            />
            <v-text-field v-model="form.music_title" label="Song Title" variant="outlined" class="mt-3" />
            <v-text-field v-model="form.music_url" label="Audio URL" variant="outlined" class="mt-3" />
            <div class="text-caption text-medium-emphasis mt-2">
              Best result: upload an audio file. YouTube page links are less reliable than direct mp3/wav/ogg files.
            </div>
            <audio v-if="form.music_url" :src="form.music_url" controls class="settings-audio-preview mt-4" />
          </v-card>

          <div class="settings-actions">
            <v-btn color="primary" size="large" block @click="save">Save All Changes</v-btn>
          </div>
        </v-col>
      </v-row>
    </div>
  </AdminShell>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import AdminShell from '../../components/admin/AdminShell.vue';
import api from '../../lib/api';

const languageTab = ref('en');
const backgroundOptions = ['dark', 'white'];
const form = reactive({
  bride_name: '',
  groom_name: '',
  hero_message: '',
  hero_message_km: '',
  venue_name: '',
  venue_name_km: '',
  venue_address: '',
  venue_address_km: '',
  google_map_url: '',
  cover_title_en: '',
  cover_title_km: '',
  cover_subtitle_en: '',
  cover_subtitle_km: '',
  closing_message_en: '',
  closing_message_km: '',
  cover_image_url: '',
  primary_image_url: '',
  map_image_url: '',
  music_url: '',
  music_title: '',
  calendar_url: '',
  background_mode: 'dark',
});
const events = ref([]);
const images = ref([]);

const activeLanguageLabel = computed(() => (languageTab.value === 'en' ? 'Editing English' : 'Editing Khmer'));

onMounted(async () => {
  const { data } = await api.get('/settings');
  Object.assign(form, data.settings);
  events.value = data.events ?? [];
  images.value = data.images ?? [];
});

const save = async () => {
  await api.put('/settings', {
    settings: form,
    events: events.value,
    images: images.value,
  });
};

const addEvent = () => {
  events.value.push({
    title: '',
    title_km: '',
    event_date: '',
    description: '',
    description_km: '',
  });
};

const removeEvent = (index) => {
  events.value.splice(index, 1);
};

const addImage = () => {
  images.value.push({
    url: '',
    caption: '',
  });
};

const removeImage = (index) => {
  images.value.splice(index, 1);
};

const uploadFile = async (file, folder) => {
  if (!file) return null;

  const selected = Array.isArray(file) ? file[0] : file;
  if (!selected) return null;

  const formData = new FormData();
  formData.append('file', selected);
  formData.append('folder', folder);

  const { data } = await api.post('/settings/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });

  return data.url;
};

const uploadSettingImage = async (file, folder, key) => {
  const url = await uploadFile(file, folder);
  if (url) {
    form[key] = url;
  }
};

const uploadSettingFile = async (file, folder, key) => {
  const url = await uploadFile(file, folder);
  if (url) {
    form[key] = url;
  }
};

const uploadGalleryImage = async (file, index) => {
  const url = await uploadFile(file, 'gallery');
  if (url) {
    images.value[index].url = url;
  }
};
</script>
