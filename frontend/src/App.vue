<script setup>
import Toast from './components/Toast.vue'
</script>

<template>
  <router-view></router-view>
  <Toast />
</template>

<style>
/* Global styles if needed, mostly handled by Tailwind */
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
}
</style>
