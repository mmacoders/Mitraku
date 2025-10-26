<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Rapat</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat informasi lengkap rapat yang telah dijadwalkan.</p>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="space-y-5">
          <!-- Judul Rapat Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                <FileText class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Judul Rapat</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ rapat?.judul || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Status Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                <BadgeCheck class="h-3.5 w-3.5 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                  :class="getStatusClass(rapat?.status)"
                >
                  {{ getStatusText(rapat?.status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Tanggal & Waktu Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <Clock class="h-3.5 w-3.5 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal & Waktu</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ rapat ? formatDate(rapat.tanggal_waktu) : '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Lokasi/Link Meeting Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                <MapPin class="h-3.5 w-3.5 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lokasi/Link Meeting</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ rapat?.lokasi || 'Tidak ditentukan' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Deskripsi Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <Text class="h-3.5 w-3.5 text-gray-600 dark:text-gray-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Deskripsi</p>
                <div class="mt-1">
                  <p class="text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">
                    {{ rapat?.deskripsi || 'Tidak ada deskripsi' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/30 border-t border-gray-200 dark:border-gray-700 flex justify-end">
        <button
          @click="closeModal"
          type="button"
          class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors duration-200 text-sm dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
        >
          <X class="h-4 w-4 mr-2" />
          Tutup
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { X, Calendar, FileText, BadgeCheck, Clock, MapPin, Text } from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: Boolean,
  rapat: Object
});

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Get status class
const getStatusClass = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
    case 'selesai':
      return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
    case 'dibatalkan':
      return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
};

// Get status text
const getStatusText = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'Akan Datang';
    case 'selesai':
      return 'Selesai';
    case 'dibatalkan':
      return 'Dibatalkan';
    default:
      return status || 'Tidak diketahui';
  }
};
</script>

<style scoped>
/* Custom scrollbar for modal body */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.8);
}
</style>