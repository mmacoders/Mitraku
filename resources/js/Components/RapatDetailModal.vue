<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
            <Calendar class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
          </div>
          <div class="ml-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Rapat</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat informasi lengkap rapat yang telah dijadwalkan.</p>
          </div>
        </div>
        <div class="mt-4 h-0.5 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-full"></div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="space-y-5">
          <!-- Judul Rapat Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <FileText class="h-4 w-4 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Judul Rapat</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ rapat?.judul || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Status Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <BadgeCheck class="h-4 w-4 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Status</h4>
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-1"
                  :class="getStatusClass(rapat?.status)"
                >
                  {{ getStatusText(rapat?.status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Tanggal & Waktu Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <Clock class="h-4 w-4 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Tanggal & Waktu</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ rapat ? formatDate(rapat.tanggal_waktu) : '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Lokasi/Link Meeting Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <MapPin class="h-4 w-4 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Lokasi/Link Meeting</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ rapat?.lokasi || 'Tidak ditentukan' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Deskripsi Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                <Text class="h-4 w-4 text-gray-600 dark:text-gray-400" />
              </div>
              <div class="ml-4 flex-1">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Deskripsi</h4>
                <div class="mt-1 max-h-40 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">
                  <p class="text-base text-gray-800 dark:text-gray-200 whitespace-pre-line">
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
          class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          <X class="h-4 w-4 mr-2" />
          Tutup
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { X, Calendar, FileText, BadgeCheck, Clock, MapPin, Text, Users } from 'lucide-vue-next';
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
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
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

// Get kehadiran class
const getKehadiranClass = (status) => {
  switch (status) {
    case 'hadir':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    case 'tidak_hadir':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    case 'belum_dikonfirmasi':
    default:
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
  }
};

// Get kehadiran text
const getKehadiranText = (status) => {
  switch (status) {
    case 'hadir':
      return 'Hadir';
    case 'tidak_hadir':
      return 'Tidak Hadir';
    case 'belum_dikonfirmasi':
    default:
      return 'Belum Dikonfirmasi';
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