<template>
  <div class="public-landing">
    <section class="landing-hero" :style="heroStyle">
      <div class="landing-hero__overlay" />
      <v-container class="landing-hero__content">
        <div class="landing-shell">
          <v-avatar size="180" class="mx-auto mb-6">
            <v-img :src="settings.primary_image_url || fallbackImage" cover />
          </v-avatar>
          <div class="landing-script">{{ settings.bride_name }} & {{ settings.groom_name }}</div>
          <p class="landing-copy">{{ settings.hero_message }}</p>
          <p class="landing-copy">{{ formattedWeddingDate }}</p>
          <InvitationLookup />
        </div>
      </v-container>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import InvitationLookup from '../../components/public/InvitationLookup.vue';
import api from '../../lib/api';

const settings = ref({});
const fallbackImage = 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486';

const formattedWeddingDate = computed(() => settings.value.wedding_datetime ?? '');
const heroStyle = computed(() => ({
  backgroundImage: `linear-gradient(rgba(10, 33, 25, 0.74), rgba(10, 33, 25, 0.85)), url('${settings.value.cover_image_url || fallbackImage}')`,
}));

onMounted(async () => {
  const { data } = await api.get('/public/content');
  settings.value = data.settings;
});
</script>
