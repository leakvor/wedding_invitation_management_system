<template>
  <div :class="['guest-page', isDark ? 'guest-page--dark' : 'guest-page--light']">
    <div v-if="settings.music_url" class="music-player">
      <button class="music-player__button" type="button" @click="toggleMusic">
        {{ audioPlaying ? t('pauseMusic') : t('playMusic') }}
      </button>
      <span class="music-player__title">{{ settings.music_title || t('weddingMusic') }}</span>
      <audio v-if="!isYouTubeMusic" ref="audioRef" :src="settings.music_url" loop preload="auto" />
      <iframe
        v-else-if="youtubeEmbedSrc"
        class="youtube-audio-frame"
        :src="youtubeEmbedSrc"
        allow="autoplay"
        title="Wedding music"
      />
    </div>

    <section class="guest-cover" :style="coverStyle">
      <div class="guest-cover__overlay" />
      <div class="guest-cover__ornament guest-cover__ornament--top-left" />
      <div class="guest-cover__ornament guest-cover__ornament--top-right" />
      <div class="guest-cover__ornament guest-cover__ornament--bottom-left" />
      <div class="guest-cover__ornament guest-cover__ornament--bottom-right" />
      <v-container class="guest-cover__content">
        <div class="language-switch">
          <v-btn-toggle v-model="language" mandatory divided color="primary">
            <v-btn value="en">EN</v-btn>
            <v-btn value="km">KM</v-btn>
          </v-btn-toggle>
        </div>

        <div class="cover-card" :class="{ 'cover-card--hidden': invitationOpened }">
          <div class="cover-card__crest">✦</div>
          <div class="cover-card__eyebrow">{{ localized('cover_title') || t('personalInvitation') }}</div>
          <v-avatar size="210" class="mx-auto mb-6 cover-avatar">
            <v-img :src="settings.primary_image_url || fallbackImage" cover />
          </v-avatar>
          <div class="cover-title">{{ coupleNames }}</div>
          <div class="cover-divider" />
          <div class="cover-guest-label">{{ t('guestGreeting') }}</div>
          <div class="cover-guest-name">{{ guest?.name || 'Guest' }}</div>
          <p class="cover-subtitle">{{ localized('cover_subtitle') || t('coverSubtitle') }}</p>
          <div class="cover-meta">
            <div class="cover-meta__item">
              <span class="cover-meta__label">{{ t('dateLabel') }}</span>
              <strong>{{ formatDate(settings.wedding_datetime) }}</strong>
            </div>
            <div class="cover-meta__item">
              <span class="cover-meta__label">{{ t('venueLabel') }}</span>
              <strong>{{ localized('venue_name') }}</strong>
            </div>
          </div>
          <v-btn color="primary" size="x-large" rounded="pill" class="cover-open-btn" @click="openInvitation">
            {{ t('openInvitation') }}
          </v-btn>
        </div>
      </v-container>
    </section>

    <div v-if="invitationOpened" class="invitation-body">
      <v-container class="py-10">
        <v-row class="mb-6 invitation-intro">
          <v-col cols="12" lg="8">
            <v-card rounded="xl" class="surface-card invitation-panel invitation-panel--hero pa-6">
              <div class="section-label">{{ t('personalInvitation') }}</div>
              <h1 class="invitation-title">{{ coupleNames }}</h1>
              <p class="invitation-copy invitation-copy--lead">
                {{ t('invitedFor') }} <strong>{{ guest?.name }}</strong>
              </p>
              <div class="invitation-highlights">
                <div class="invitation-highlight">
                  <span>{{ t('allowedGuests') }}</span>
                  <strong>{{ guest?.allowed_guests ?? 1 }}</strong>
                </div>
                <div class="invitation-highlight">
                  <span>{{ t('rsvpStatus') }}</span>
                  <strong>{{ formatStatus(guest?.rsvp_status) }}</strong>
                </div>
                <div class="invitation-highlight">
                  <span>{{ t('weddingMusic') }}</span>
                  <strong>{{ settings.music_title || 'Ceremony Playlist' }}</strong>
                </div>
              </div>
              <p v-if="guest?.notes" class="invitation-copy mt-4">{{ t('notes') }}: {{ guest.notes }}</p>
              <div class="d-flex ga-3 flex-wrap mt-5">
                <v-btn color="primary" size="large" @click="updateRsvp('yes')">{{ t('attending') }}</v-btn>
                <v-btn color="secondary" size="large" @click="updateRsvp('coming')">{{ t('coming') }}</v-btn>
                <v-btn variant="outlined" color="primary" size="large" @click="updateRsvp('no')">{{ t('cannotAttend') }}</v-btn>
              </div>
            </v-card>
          </v-col>
          <v-col cols="12" lg="4">
            <v-card rounded="xl" class="surface-card invitation-panel invitation-panel--detail pa-6 h-100">
              <div class="section-label">{{ t('location') }}</div>
              <h2 class="text-h5 mb-2">{{ localized('venue_name') }}</h2>
              <p class="invitation-copy">{{ localized('venue_address') }}</p>
              <div class="invitation-detail-grid">
                <div>
                  <div class="invitation-detail-grid__label">{{ t('dateLabel') }}</div>
                  <strong>{{ formatDate(settings.wedding_datetime) }}</strong>
                </div>
                <div>
                  <div class="invitation-detail-grid__label">{{ t('guestGreeting') }}</div>
                  <strong>{{ guest?.name }}</strong>
                </div>
              </div>
              <v-btn class="mt-5" color="secondary" block :href="settings.google_map_url" target="_blank">
                {{ t('openMap') }}
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <v-card rounded="xl" class="surface-card invitation-panel invitation-panel--timeline pa-6 mb-6">
          <div class="section-label">{{ t('timeline') }}</div>
          <div class="timeline-heading">{{ t('timelineSubtitle') }}</div>
          <div class="timeline-list">
            <div
              v-for="(event, index) in events"
              :key="event.id"
              :class="['timeline-item', index % 2 === 0 ? 'timeline-item--left' : 'timeline-item--right']"
            >
              <div class="timeline-item__dot" />
              <div class="timeline-item__content">
                <div class="timeline-item__title">{{ localizedEvent(event, 'title') }}</div>
                <div class="timeline-item__time">{{ formatDate(event.event_date) }}</div>
                <div class="timeline-item__desc">{{ localizedEvent(event, 'description') }}</div>
              </div>
            </div>
          </div>
        </v-card>

        <v-card rounded="xl" class="surface-card invitation-panel invitation-panel--gallery pa-6 mb-6">
          <div class="section-label">{{ t('gallery') }}</div>
          <div class="timeline-heading">{{ t('gallerySubtitle') }}</div>
          <div v-if="images.length" class="gallery-carousel">
            <v-btn
              icon="mdi-chevron-left"
              variant="tonal"
              class="gallery-carousel__nav gallery-carousel__nav--prev"
              @click="previousImage"
            />
            <div class="gallery-carousel__frame">
              <v-img :src="currentImage.url" height="420" cover class="rounded-xl" />
              <div class="gallery-carousel__caption">{{ currentImage.caption }}</div>
              <div class="gallery-carousel__counter">{{ currentImageIndex + 1 }} / {{ images.length }}</div>
            </div>
            <v-btn
              icon="mdi-chevron-right"
              variant="tonal"
              class="gallery-carousel__nav gallery-carousel__nav--next"
              @click="nextImage"
            />
          </div>
          <p v-else class="text-medium-emphasis">No gallery images yet.</p>
        </v-card>

        <v-row>
          <v-col cols="12" lg="5">
            <v-card rounded="xl" class="surface-card invitation-panel pa-6">
              <div class="section-label">{{ t('leaveWish') }}</div>
              <div class="timeline-heading">{{ t('wishSubtitle') }}</div>
              <v-text-field v-model="commentForm.author_name" :label="t('yourName')" variant="outlined" />
              <v-textarea v-model="commentForm.message" :label="t('yourMessage')" variant="outlined" rows="4" />
              <v-btn color="primary" size="large" block @click="submitComment">{{ t('sendWish') }}</v-btn>
            </v-card>
          </v-col>
          <v-col cols="12" lg="7">
            <v-card rounded="xl" class="surface-card invitation-panel pa-6">
              <div class="section-label">{{ localizedWishTitle }}</div>
              <div class="timeline-heading">{{ t('communityBlessing') }}</div>
              <div v-if="comments.length" class="comments-list">
                <div v-for="comment in comments" :key="comment.id" class="comment-card">
                  <div class="comment-card__name">{{ comment.author_name }}</div>
                  <div class="comment-card__meta">{{ comment.language === 'km' ? 'Khmer' : 'English' }}</div>
                  <div class="comment-card__body">{{ comment.message }}</div>
                </div>
              </div>
              <p v-else class="text-medium-emphasis">{{ t('noWishes') }}</p>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../lib/api';

const route = useRoute();
const settings = ref({});
const guest = ref(null);
const events = ref([]);
const images = ref([]);
const comments = ref([]);
const invitationOpened = ref(false);
const language = ref('en');
const audioPlaying = ref(false);
const audioRef = ref(null);
const currentImageIndex = ref(0);
const fallbackImage = 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486';
const commentForm = reactive({
  author_name: '',
  message: '',
});

const dictionary = {
  en: {
    guestGreeting: 'Special invitation for',
    coverSubtitle: 'We cordially invite you to celebrate our wedding ceremony with us.',
    openInvitation: 'Open Invitation',
    personalInvitation: 'Personal Invitation',
    invitedFor: 'This invitation is prepared for',
    allowedGuests: 'Guests allowed',
    rsvpStatus: 'RSVP status',
    dateLabel: 'Wedding Date',
    venueLabel: 'Venue',
    notes: 'Note',
    attending: 'Attending',
    coming: 'Coming',
    cannotAttend: 'Cannot Attend',
    location: 'Location',
    openMap: 'Open Map',
    timeline: 'Event Timeline',
    timelineSubtitle: 'A graceful flow of moments prepared for our celebration.',
    gallery: 'Photo Gallery',
    gallerySubtitle: 'Memories, colors, and details from our special story.',
    leaveWish: 'Leave a Wish',
    wishSubtitle: 'Share your blessing, prayer, or a short message for the couple.',
    communityBlessing: 'Messages from family and friends who celebrate with us.',
    yourName: 'Your Name',
    yourMessage: 'Your Message / Wish',
    sendWish: 'Send Wish',
    noWishes: 'No wishes yet. Be the first guest to leave one.',
    playMusic: 'Play Music',
    pauseMusic: 'Pause Music',
    weddingMusic: 'Wedding Music',
  },
  km: {
    guestGreeting: 'Special invitation for',
    coverSubtitle: 'We warmly invite you to join our wedding celebration.',
    openInvitation: 'Open Invitation',
    personalInvitation: 'Personal Invitation',
    invitedFor: 'This invitation is prepared for',
    allowedGuests: 'Guests allowed',
    rsvpStatus: 'RSVP status',
    dateLabel: 'Wedding Date',
    venueLabel: 'Venue',
    notes: 'Note',
    attending: 'Attending',
    coming: 'Coming',
    cannotAttend: 'Cannot Attend',
    location: 'Location',
    openMap: 'Open Map',
    timeline: 'Event Timeline',
    timelineSubtitle: 'A graceful flow of moments prepared for our celebration.',
    gallery: 'Photo Gallery',
    gallerySubtitle: 'Memories, colors, and details from our special story.',
    leaveWish: 'Leave a Wish',
    wishSubtitle: 'Share your blessing, prayer, or a short message for the couple.',
    communityBlessing: 'Messages from family and friends who celebrate with us.',
    yourName: 'Your Name',
    yourMessage: 'Your Message / Wish',
    sendWish: 'Send Wish',
    noWishes: 'No wishes yet. Be the first guest to leave one.',
    playMusic: 'Play Music',
    pauseMusic: 'Pause Music',
    weddingMusic: 'Wedding Music',
  },
};

const isDark = computed(() => (settings.value.background_mode ?? 'dark') === 'dark');
const coupleNames = computed(() => `${settings.value.bride_name || ''} & ${settings.value.groom_name || ''}`.trim());
const localizedWishTitle = computed(() => localized(language.value === 'km' ? 'wishes_title_km' : 'wishes_title_en'));
const isYouTubeMusic = computed(() => /youtu\.be|youtube\.com/.test(settings.value.music_url || ''));
const currentImage = computed(() => images.value[currentImageIndex.value] || {});
const coverStyle = computed(() => ({
  backgroundImage: `linear-gradient(${isDark.value ? 'rgba(10, 33, 25, 0.72), rgba(10, 33, 25, 0.84)' : 'rgba(255,255,255,0.3), rgba(255,255,255,0.68)'}), url('${settings.value.cover_image_url || fallbackImage}')`,
}));
const youtubeEmbedSrc = computed(() => {
  if (!isYouTubeMusic.value || !audioPlaying.value) return '';

  const url = settings.value.music_url || '';
  const match = url.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
  const videoId = match?.[1];

  if (!videoId) return '';

  return `https://www.youtube.com/embed/${videoId}?autoplay=1&loop=1&playlist=${videoId}`;
});

const t = (key) => dictionary[language.value][key];

const localized = (key) => {
  const khmerKey = `${key}_km`;
  if (language.value === 'km' && settings.value[khmerKey]) {
    return settings.value[khmerKey];
  }

  return settings.value[key] || settings.value[khmerKey] || '';
};

const localizedEvent = (event, key) => {
  const khmerKey = `${key}_km`;
  if (language.value === 'km' && event[khmerKey]) {
    return event[khmerKey];
  }

  return event[key] || event[khmerKey] || '';
};

const formatDate = (value) => {
  if (!value) return '';

  return new Intl.DateTimeFormat(language.value === 'km' ? 'km-KH' : 'en-US', {
    dateStyle: 'full',
    timeStyle: 'short',
  }).format(new Date(value));
};

const formatStatus = (status) => {
  const labels = {
    en: { pending: 'Pending', yes: 'Yes', no: 'No', coming: 'Coming' },
    km: { pending: 'Pending', yes: 'Yes', no: 'No', coming: 'Coming' },
  };

  return labels[language.value][status] || status || '-';
};

const loadInvitation = async () => {
  const { data } = await api.get(`/public/invitations/${route.params.slug}`);
  guest.value = data.guest;
  settings.value = data.settings;
  events.value = data.events;
  images.value = data.images;
  comments.value = data.comments || [];
  currentImageIndex.value = 0;
};

const updateRsvp = async (status) => {
  if (!guest.value) return;

  const { data } = await api.put(`/public/rsvp/${guest.value.id}`, {
    rsvp_status: status,
  });

  guest.value = {
    ...guest.value,
    ...data,
  };
};

const submitComment = async () => {
  const { data } = await api.post(`/public/invitations/${route.params.slug}/comments`, {
    ...commentForm,
    language: language.value,
  });

  comments.value = [data, ...comments.value];
  commentForm.author_name = '';
  commentForm.message = '';
};

const playMusic = async () => {
  if (isYouTubeMusic.value) {
    audioPlaying.value = true;
    return;
  }

  if (!audioRef.value) return;

  try {
    await audioRef.value.play();
    audioPlaying.value = true;
  } catch {
    audioPlaying.value = false;
  }
};

const pauseMusic = () => {
  if (!isYouTubeMusic.value && audioRef.value) {
    audioRef.value.pause();
  }
  audioPlaying.value = false;
};

const toggleMusic = () => {
  if (audioPlaying.value) {
    pauseMusic();
    return;
  }

  playMusic();
};

const previousImage = () => {
  if (!images.value.length) return;
  currentImageIndex.value = (currentImageIndex.value - 1 + images.value.length) % images.value.length;
};

const nextImage = () => {
  if (!images.value.length) return;
  currentImageIndex.value = (currentImageIndex.value + 1) % images.value.length;
};

const openInvitation = async () => {
  invitationOpened.value = true;
  await playMusic();
};

onMounted(loadInvitation);
</script>
