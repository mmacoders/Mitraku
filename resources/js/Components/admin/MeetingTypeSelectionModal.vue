<template>
  <Modal :show="show" @close="close">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        Pilih Jenis Rapat
      </h2>
      <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
        Silahkan pilih jenis rapat yang ingin Anda buat.
      </p>
      
      <div class="grid grid-cols-2 gap-4">
        <button
          @click="$emit('select', 'mou')"
          class="flex flex-col items-center justify-center p-6 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 dark:hover:border-indigo-500 transition-all duration-200 group"
        >
          <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-200 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <span class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-700 dark:group-hover:text-indigo-300">Rapat MoU</span>
          <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">Untuk pembahasan MoU baru</span>
        </button>

        <button
          @click="$emit('select', 'pks')"
          class="flex flex-col items-center justify-center p-6 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 dark:hover:border-blue-500 transition-all duration-200 group"
        >
          <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </div>
          <span class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300">Rapat PKS</span>
          <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">Untuk pembahasan PKS baru</span>
        </button>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="close"
          class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
        >
          Batal
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'

defineProps({
  show: Boolean
})

const emit = defineEmits(['close', 'select'])

const close = () => {
  emit('close')
}
</script>
