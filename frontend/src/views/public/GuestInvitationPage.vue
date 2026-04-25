<template>
  <div :class="['plan-guest-page', isDark ? 'plan-guest-page--dark' : 'plan-guest-page--light']">
    <audio v-if="settings.music_url && !isYouTubeMusic" ref="audioRef" :src="settings.music_url" loop preload="auto" playsinline />
    <iframe
      v-else-if="settings.music_url && isYouTubeMusic && youtubeEmbedSrc"
      class="youtube-audio-frame"
      :src="youtubeEmbedSrc"
      allow="autoplay"
      title="Wedding music"
    />

    <div class="plan-guest-controls plan-guest-controls--left">
      <button type="button" class="floating-toggle" @click="toggleLanguage">
        <span :class="{ 'is-active': language === 'km' }">ខ្មែរ</span>
        <span class="floating-toggle__switch">
          <span :class="['floating-toggle__thumb', language === 'en' ? 'floating-toggle__thumb--right' : '']" />
        </span>
        <span :class="{ 'is-active': language === 'en' }">EN</span>
      </button>
    </div>

    <div v-if="settings.music_url" class="plan-guest-controls plan-guest-controls--right">
      <button type="button" class="floating-icon-button" :aria-label="t('toggleMusic')" @click="toggleMusic">
        <v-icon :icon="audioPlaying ? 'mdi-volume-high' : 'mdi-volume-off'" size="20" />
      </button>
    </div>

    <nav v-if="invitationOpened" class="floating-nav">
      <button
        v-for="item in navItems"
        :key="item.id"
        type="button"
        :class="['floating-nav__item', activeSection === item.id ? 'floating-nav__item--active' : '']"
        @click="scrollToSection(item.id)"
      >
        <v-icon :icon="item.icon" size="18" />
        <span>{{ t(item.label) }}</span>
      </button>
    </nav>

    <button v-if="!invitationOpened" type="button" class="floating-scroll" @click="openInvitation">
      <v-icon icon="mdi-chevron-double-down" size="22" />
    </button>

    <section id="home" :ref="sectionRefs.home" class="hero-section" :style="heroStyle">
      <div class="hero-section__overlay" />
      <div class="hero-section__pattern" />

      <div class="hero-section__content">
        <div class="hero-title-block">
          <div class="hero-title-block__eyebrow">{{ localized('cover_title') || t('weddingCeremony') }}</div>
          <h1 class="hero-title-block__title">{{ coupleNames }}</h1>
          <div class="hero-title-block__divider" />
          <p class="hero-title-block__invite-label">{{ t('respectfullyInvited') }}</p>
          <div class="guest-name-frame">
            <span>{{ guest?.name || t('guest') }}</span>
          </div>
          <p class="hero-title-block__date">{{ formatDate(settings.wedding_datetime) }}</p>

          <button type="button" class="open-envelope-button" @click="openInvitation">
            {{ invitationOpened ? t('scrollDown') : t('openInvitation') }}
          </button>
        </div>
      </div>
    </section>

    <div v-if="invitationOpened" class="plan-guest-body">
      <section id="invitation" :ref="sectionRefs.invitation" class="section-card section-card--message">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('invitationIntro') }}</div>
          <h2 class="section-heading__title">{{ t('honorablyInvite') }}</h2>
        </div>

        <p class="section-copy">
          {{ localized('hero_message') || t('defaultInvitationMessage') }}
        </p>

        <div class="ornament-divider" />
      </section>

      <section class="countdown-section section-card">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('countdownLabel') }}</div>
          <h2 class="section-heading__title">{{ t('saveTheDate') }}</h2>
        </div>

        <p class="countdown-section__date">{{ formatDate(settings.wedding_datetime) }}</p>

        <div class="countdown-grid">
          <div v-for="item in countdownItems" :key="item.label" class="countdown-grid__item">
            <strong>{{ item.value }}</strong>
            <span>{{ item.label }}</span>
          </div>
        </div>

        <a
          v-if="settings.calendar_url"
          :href="settings.calendar_url"
          target="_blank"
          rel="noreferrer"
          class="texture-button"
        >
          {{ t('saveCalendar') }}
        </a>
      </section>

      <section id="schedule" :ref="sectionRefs.schedule" class="section-card">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('eventFlow') }}</div>
          <h2 class="section-heading__title">{{ t('timeline') }}</h2>
        </div>

        <div class="ceremony-timeline">
          <div
            v-for="(event, index) in events"
            :key="event.id ?? `${event.title}-${index}`"
            :class="['ceremony-timeline__item', index % 2 === 0 ? 'ceremony-timeline__item--left' : 'ceremony-timeline__item--right']"
          >
            <div class="ceremony-timeline__dot" />
            <div class="ceremony-timeline__content">
              <div class="ceremony-timeline__time">{{ formatDate(event.event_date) }}</div>
              <div class="ceremony-timeline__title">{{ localizedEvent(event, 'title') }}</div>
              <div class="ceremony-timeline__desc">{{ localizedEvent(event, 'description') }}</div>
            </div>
          </div>
        </div>
      </section>

      <section id="location" :ref="sectionRefs.location" class="section-card">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('eventPlace') }}</div>
          <h2 class="section-heading__title">{{ t('location') }}</h2>
        </div>

        <p class="section-copy section-copy--center">{{ localized('venue_address') }}</p>

        <div v-if="settings.map_image_url || settings.primary_image_url" class="location-map-preview">
          <img :src="settings.map_image_url || settings.primary_image_url" :alt="localized('venue_name') || 'Map'" />
        </div>

        <a
          v-if="settings.google_map_url"
          :href="settings.google_map_url"
          target="_blank"
          rel="noreferrer"
          class="texture-button"
        >
          {{ t('openMap') }}
        </a>
      </section>

      <section id="gallery" :ref="sectionRefs.gallery" class="section-card">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('memories') }}</div>
          <h2 class="section-heading__title">{{ t('gallery') }}</h2>
        </div>

        <div v-if="images.length" class="photo-grid">
          <button
            v-for="(image, index) in images"
            :key="image.id ?? `${image.url}-${index}`"
            type="button"
            class="photo-grid__item"
            @click="openGallery(index)"
          >
            <img :src="image.url" :alt="image.caption || `Photo ${index + 1}`" />
            <span v-if="image.caption" class="photo-grid__caption">{{ image.caption }}</span>
          </button>
        </div>
      </section>

      <section class="section-card section-card--closing">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('gratitude') }}</div>
          <h2 class="section-heading__title">{{ t('thankYou') }}</h2>
        </div>

        <p class="section-copy">
          {{ localized('closing_message') || t('defaultClosingMessage') }}
        </p>
      </section>

      <section id="messages" :ref="sectionRefs.messages" class="section-card">
        <div class="section-heading">
          <div class="section-heading__eyebrow">{{ t('blessings') }}</div>
          <h2 class="section-heading__title">{{ t('messages') }}</h2>
        </div>

        <div class="wishes-layout">
          <form class="wish-form" @submit.prevent="submitComment">
            <h3>{{ t('confirmAttendance') }}</h3>

            <div class="wish-form__rsvp">
              <button
                type="button"
                :class="['wish-form__rsvp-button', guest?.rsvp_status === 'yes' ? 'wish-form__rsvp-button--active' : '']"
                @click="updateRsvp('yes')"
              >
                {{ t('attending') }}
              </button>
              <button
                type="button"
                :class="['wish-form__rsvp-button', guest?.rsvp_status === 'no' ? 'wish-form__rsvp-button--active is-no' : '']"
                @click="updateRsvp('no')"
              >
                {{ t('cannotAttend') }}
              </button>
            </div>

            <textarea v-model="commentForm.message" :placeholder="t('yourMessage')" rows="5" />

            <button type="submit" class="texture-button texture-button--solid">
              {{ t('sendWish') }}
            </button>
          </form>

          <div class="wish-wall">
            <div v-if="comments.length" class="wish-wall__list">
              <article v-for="comment in comments" :key="comment.id" class="wish-wall__item">
                <div class="wish-wall__name">{{ comment.author_name }}</div>
                <div class="wish-wall__message">"{{ comment.message }}"</div>
                <div class="wish-wall__meta">{{ formatCommentDate(comment.created_at) }}</div>
              </article>
            </div>
            <p v-else class="section-copy section-copy--center">{{ t('noWishes') }}</p>
          </div>
        </div>
      </section>
    </div>

    <v-dialog v-model="galleryDialog" max-width="920">
      <v-card rounded="xl" class="gallery-dialog">
        <v-img :src="currentImage.url" height="640" cover />
        <div class="gallery-dialog__actions">
          <v-btn icon="mdi-chevron-left" variant="tonal" @click="previousImage" />
          <div class="gallery-dialog__caption">{{ currentImage.caption || `${currentImageIndex + 1} / ${images.length}` }}</div>
          <v-btn icon="mdi-chevron-right" variant="tonal" @click="nextImage" />
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../lib/api';

const route = useRoute();
const settings = ref({});
const guest = ref(null);
const events = ref([]);
const images = ref([]);
const comments = ref([]);
const invitationOpened = ref(false);
const language = ref('km');
const audioPlaying = ref(false);
const audioRef = ref(null);
const currentImageIndex = ref(0);
const galleryDialog = ref(false);
const activeSection = ref('home');
const countdownNow = ref(Date.now());
const fallbackImage = 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486';
const sectionRefs = {
  home: ref(null),
  invitation: ref(null),
  schedule: ref(null),
  location: ref(null),
  gallery: ref(null),
  messages: ref(null),
};
const commentForm = reactive({
  author_name: '',
  message: '',
});
let countdownTimer = null;
let observer = null;

const dictionary = {
  en: {
    weddingCeremony: 'Wedding Ceremony',
    respectfullyInvited: 'Respectfully invited',
    guest: 'Guest',
    openInvitation: 'Open Invitation',
    scrollDown: 'Scroll Down',
    invitationIntro: 'Invitation',
    honorablyInvite: 'We honorably invite you',
    defaultInvitationMessage:
      'We would be honored by your presence as witness and blessing on our wedding day. Your presence will make this celebration more meaningful for our family.',
    countdownLabel: 'Countdown',
    saveTheDate: 'Save The Date',
    days: 'Days',
    hours: 'Hours',
    minutes: 'Minutes',
    seconds: 'Seconds',
    saveCalendar: 'Save to Calendar',
    eventFlow: 'Event Flow',
    timeline: 'Event Timeline',
    eventPlace: 'Event Place',
    location: 'Location',
    openMap: 'Open in Google Map',
    memories: 'Memories',
    gallery: 'Photo Gallery',
    gratitude: 'Gratitude',
    thankYou: 'Thank You',
    defaultClosingMessage:
      'Thank you for your time, support, and prayers. We apologize if we are unable to deliver this invitation in person.',
    blessings: 'Blessings',
    messages: 'Messages & Wishes',
    confirmAttendance: 'Confirm your attendance',
    attending: 'Attending',
    cannotAttend: 'Not Attending',
    yourMessage: 'Write your wish for the couple',
    sendWish: 'Send Message',
    noWishes: 'No messages yet.',
    toggleMusic: 'Toggle music',
    home: 'Home',
    schedule: 'Schedule',
    galleryNav: 'Photos',
    messagesNav: 'Messages',
  },
  km: {
    weddingCeremony: 'សិរីមង្គលអាពាហ៍ពិពាហ៍',
    respectfullyInvited: 'សូមគោរពអញ្ជើញ',
    guest: 'ភ្ញៀវកិត្តិយស',
    openInvitation: 'បើកលិខិតអញ្ជើញ',
    scrollDown: 'សូមអូសចុះក្រោម',
    invitationIntro: 'សេចក្តីអញ្ជើញ',
    honorablyInvite: 'យើងខ្ញុំមានកិត្តិយសសូមគោរពអញ្ជើញ',
    defaultInvitationMessage:
      'សូមអញ្ជើញចូលរួមជាភ្ញៀវកិត្តិយស ដើម្បីប្រសិទ្ធិពរជ័យសិរីសួស្តី ជ័យមង្គល ក្នុងពិធីអាពាហ៍ពិពាហ៍របស់យើងខ្ញុំទាំងពីរ។',
    countdownLabel: 'ថ្ងៃរាប់ថយក្រោយ',
    saveTheDate: 'សូមរក្សាទុកថ្ងៃពិសេសនេះ',
    days: 'ថ្ងៃ',
    hours: 'ម៉ោង',
    minutes: 'នាទី',
    seconds: 'វិនាទី',
    saveCalendar: 'កត់ចូលក្នុងប្រតិទិន',
    eventFlow: 'កម្មវិធី',
    timeline: 'របៀបវារៈកម្មវិធី',
    eventPlace: 'ទីតាំង',
    location: 'ទីតាំងកម្មវិធី',
    openMap: 'មើលក្នុង Google Map',
    memories: 'កម្រងចងចាំ',
    gallery: 'កម្រងរូបភាព',
    gratitude: 'អំណរគុណ',
    thankYou: 'សូមអរគុណ និងសូមអភ័យទោស',
    defaultClosingMessage:
      'យើងខ្ញុំទាំងពីរ សូមថ្លែងអំណរគុណយ៉ាងជ្រាលជ្រៅចំពោះវត្តមាន និងសេចក្តីជូនពររបស់លោកអ្នក។ សូមអភ័យទោស ប្រសិនបើយើងខ្ញុំមិនបានជូនលិខិតអញ្ជើញដោយផ្ទាល់។',
    blessings: 'សារជូនពរ',
    messages: 'សារជូនពរ',
    confirmAttendance: 'បញ្ជាក់ពីវត្តមានអ្នក',
    attending: '✓ ចូលរួម',
    cannotAttend: '✗ បដិសេធ',
    yourMessage: 'សារជូនពរ',
    sendWish: 'ផ្ញើសារ',
    noWishes: 'មិនទាន់មានសារជូនពរ។',
    toggleMusic: 'បើកបិទតន្ត្រី',
    home: 'ដើម',
    schedule: 'កម្មវិធី',
    galleryNav: 'រូបភាព',
    messagesNav: 'សារជូនពរ',
  },
};

const navItems = [
  { id: 'home', icon: 'mdi-home-outline', label: 'home' },
  { id: 'schedule', icon: 'mdi-calendar-month-outline', label: 'schedule' },
  { id: 'location', icon: 'mdi-map-marker-outline', label: 'location' },
  { id: 'gallery', icon: 'mdi-image-multiple-outline', label: 'galleryNav' },
  { id: 'messages', icon: 'mdi-message-processing-outline', label: 'messagesNav' },
];

const isDark = computed(() => (settings.value.background_mode ?? 'dark') === 'dark');
const coupleNames = computed(() => `${settings.value.bride_name || ''} ${language.value === 'km' ? 'និង' : '&'} ${settings.value.groom_name || ''}`.trim());
const isYouTubeMusic = computed(() => /youtu\.be|youtube\.com/.test(settings.value.music_url || ''));
const currentImage = computed(() => images.value[currentImageIndex.value] || {});
const heroStyle = computed(() => ({
  backgroundImage: `linear-gradient(${isDark.value ? 'rgba(6, 24, 18, 0.42), rgba(6, 24, 18, 0.72)' : 'rgba(255,248,239,0.28), rgba(255,248,239,0.6)'}), url('${settings.value.cover_image_url || fallbackImage}')`,
}));
const weddingDate = computed(() => {
  if (!settings.value.wedding_datetime) return null;
  const date = new Date(settings.value.wedding_datetime);
  return Number.isNaN(date.getTime()) ? null : date;
});
const countdownValues = computed(() => {
  if (!weddingDate.value) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0 };
  }

  const difference = Math.max(0, weddingDate.value.getTime() - countdownNow.value);
  const days = Math.floor(difference / 86400000);
  const hours = Math.floor((difference % 86400000) / 3600000);
  const minutes = Math.floor((difference % 3600000) / 60000);
  const seconds = Math.floor((difference % 60000) / 1000);

  return { days, hours, minutes, seconds };
});
const countdownItems = computed(() => [
  { label: t('days'), value: countdownValues.value.days },
  { label: t('hours'), value: countdownValues.value.hours },
  { label: t('minutes'), value: countdownValues.value.minutes },
  { label: t('seconds'), value: countdownValues.value.seconds },
]);
const youtubeEmbedSrc = computed(() => {
  if (!isYouTubeMusic.value || !audioPlaying.value) return '';

  const url = settings.value.music_url || '';
  const match = url.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
  const videoId = match?.[1];

  if (!videoId) return '';

  return `https://www.youtube.com/embed/${videoId}?autoplay=1&loop=1&playlist=${videoId}`;
});

const t = (key) => dictionary[language.value][key] || key;

const toggleLanguage = () => {
  language.value = language.value === 'km' ? 'en' : 'km';
};

const localized = (key) => {
  const englishKey = key.endsWith('_en') ? key : `${key}_en`;
  const khmerKey = key.endsWith('_km') ? key : `${key}_km`;

  if (language.value === 'km') {
    return settings.value[khmerKey] || settings.value[key] || settings.value[englishKey] || '';
  }

  return settings.value[englishKey] || settings.value[key] || settings.value[khmerKey] || '';
};

const localizedEvent = (event, key) => {
  const khmerKey = `${key}_km`;
  if (language.value === 'km') {
    return event[khmerKey] || event[key] || '';
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

const formatCommentDate = (value) => {
  if (!value) return '';

  return new Intl.DateTimeFormat(language.value === 'km' ? 'km-KH' : 'en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));
};

const loadInvitation = async () => {
  const { data } = await api.get(`/public/invitations/${route.params.slug}`);
  guest.value = data.guest;
  settings.value = data.settings;
  events.value = data.events || [];
  images.value = data.images || [];
  comments.value = data.comments || [];
  commentForm.author_name = data.guest?.name || '';
  currentImageIndex.value = 0;
};

const updateRsvp = async (status) => {
  if (!guest.value) return;

  const { data } = await api.put(`/public/rsvp/${guest.value.id}`, { rsvp_status: status });
  guest.value = { ...guest.value, ...data };
};

const submitComment = async () => {
  if (!commentForm.message.trim()) return;

  const { data } = await api.post(`/public/invitations/${route.params.slug}/comments`, {
    ...commentForm,
    author_name: commentForm.author_name || guest.value?.name || t('guest'),
    language: language.value,
  });

  comments.value = [data, ...comments.value];
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

const toggleMusic = async () => {
  if (audioPlaying.value) {
    pauseMusic();
    return;
  }

  await playMusic();
};

const openInvitation = async () => {
  invitationOpened.value = true;
  await playMusic();
  requestAnimationFrame(() => scrollToSection('invitation'));
};

const scrollToSection = (id) => {
  const section = sectionRefs[id]?.value;
  if (!section) return;

  section.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

const openGallery = (index) => {
  currentImageIndex.value = index;
  galleryDialog.value = true;
};

const previousImage = () => {
  if (!images.value.length) return;
  currentImageIndex.value = (currentImageIndex.value - 1 + images.value.length) % images.value.length;
};

const nextImage = () => {
  if (!images.value.length) return;
  currentImageIndex.value = (currentImageIndex.value + 1) % images.value.length;
};

onMounted(async () => {
  await loadInvitation();

  countdownTimer = window.setInterval(() => {
    countdownNow.value = Date.now();
  }, 1000);

  observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

      if (visible?.target?.id) {
        activeSection.value = visible.target.id;
      }
    },
    { threshold: 0.35 },
  );

  Object.values(sectionRefs).forEach((sectionRef) => {
    if (sectionRef.value) {
      observer.observe(sectionRef.value);
    }
  });
});

onBeforeUnmount(() => {
  if (countdownTimer) {
    window.clearInterval(countdownTimer);
  }

  observer?.disconnect();
});
</script>
