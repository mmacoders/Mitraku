<template>
  <div v-if="showWarning" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20 dark:border-yellow-600">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3">
        <p class="text-sm text-yellow-700 dark:text-yellow-300">
          <span class="font-medium">Mode tidak aman!</span> Aplikasi ini sedang dijalankan tanpa HTTPS. Untuk pengalaman terbaik dan fitur offline, aktifkan HTTPS.
        </p>
      </div>
      <div class="ml-auto pl-3">
        <div class="-mx-1.5 -my-1.5">
          <button @click="dismissWarning" class="inline-flex rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-600 focus:ring-offset-2 dark:hover:bg-yellow-800/30 dark:text-yellow-400">
            <span class="sr-only">Dismiss</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showWarning = ref(false);

const dismissWarning = () => {
  showWarning.value = false;
  localStorage.setItem('httpsWarningDismissed', 'true');
};

onMounted(() => {
  // Check if we're not on HTTPS and the warning hasn't been dismissed
  const isHttps = window.location.protocol === 'https:';
  const warningDismissed = localStorage.getItem('httpsWarningDismissed') === 'true';
  
  if (!isHttps && !warningDismissed) {
    showWarning.value = true;
  }
});
</script>