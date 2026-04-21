<template>
  <v-text-field
    v-model="model"
    label="Search guest by name"
    prepend-inner-icon="mdi-magnify"
    variant="outlined"
    clearable
    hide-details
  />
</template>

<script setup>
const model = defineModel({ type: String, default: '' });
</script>

