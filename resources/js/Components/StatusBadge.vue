<template>
  <span :class="badgeClass" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
    {{ statusText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: String
})

const statusText = computed(() => {
  switch (props.status) {
    case 'proses':
      return 'Proses'
    case 'revisi':
      return 'Revisi'
    case 'ditolak':
      return 'Ditolak'
    case 'disetujui':
      return 'Disetujui'
    default:
      return props.status
  }
})

const badgeClass = computed(() => {
  switch (props.status) {
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'revisi':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
})
</script>