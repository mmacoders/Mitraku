<template>
  <Modal :show="show" @close="closeModal" max-width="2xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <!-- Modal header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-2xl mr-3 text-blue-600">📅</span>
            <div>
              <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
                Detail Rapat
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Informasi lengkap tentang rapat
              </p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
            <X class="h-5 w-5" />
          </button>
        </div>
      </div>

      <!-- Modal body -->
      <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
        <div class="space-y-6">
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

          <!-- Judul PKS Terkait Card -->
          <div v-if="rapat?.pks_submission" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                <FileText class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Judul PKS Terkait</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ rapat.pks_submission.title }}
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
                <Calendar class="h-3.5 w-3.5 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal & Waktu</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ rapat ? formatDate(rapat.tanggal_waktu) : '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Jadwal Penandatanganan Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                <Clock class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Jadwal Penandatanganan</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ rapat ? formatDate(rapat.signing_schedule) : '-' }}
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

          <!-- Dokumen PKS Card -->
          <div v-if="rapat?.pks_document_url" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                <FileText class="h-3.5 w-3.5 text-red-600 dark:text-red-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Dokumen PKS</p>
                <div class="mt-2 flex items-center justify-between">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ getFileIcon('application/pdf') }}</span>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ getFileNameFromUrl(rapat.pks_document_url) }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Dokumen PKS yang dibawa ke rapat
                      </div>
                    </div>
                  </div>
                  <a 
                    :href="rapat.pks_document_url" 
                    target="_blank"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <Download class="h-3 w-3 mr-1" />
                    Unduh
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Draft Dokumen Card -->
          <div v-if="rapat?.draft_document_url" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                <FileText class="h-3.5 w-3.5 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Draft Dokumen</p>
                <div class="mt-2 flex items-center justify-between">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ getFileIcon('application/pdf') }}</span>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ getFileNameFromUrl(rapat.draft_document_url) }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Draft dokumen hasil rapat
                      </div>
                    </div>
                  </div>
                  <a 
                    :href="rapat.draft_document_url" 
                    target="_blank"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                  >
                    <Download class="h-3 w-3 mr-1" />
                    Unduh
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Dokumen yang Ditandatangani Card -->
          <div v-if="rapat?.signed_document_url" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                <FileSignature class="h-3.5 w-3.5 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Dokumen yang Ditandatangani</p>
                <div class="mt-2 flex items-center justify-between">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ getFileIcon('application/pdf') }}</span>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ getFileNameFromUrl(rapat.signed_document_url) }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Dokumen final yang telah ditandatangani
                      </div>
                    </div>
                  </div>
                  <a 
                    :href="rapat.signed_document_url" 
                    target="_blank"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <Download class="h-3 w-3 mr-1" />
                    Unduh
                  </a>
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
import { ref } from 'vue';
import { X, Calendar, FileText, BadgeCheck, Clock, MapPin, Text, Download, FileSignature } from 'lucide-vue-next';
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
    month: 'long', 
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
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
    case 'selesai':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    case 'dibatalkan':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
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
      return status;
  }
};

// Get file icon based on MIME type
const getFileIcon = (mimeType) => {
  switch (mimeType) {
    case 'application/pdf':
      return '📄';
    case 'application/msword':
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
      return '📝';
    default:
      return '📎';
  }
};

// Get file name from URL
const getFileNameFromUrl = (url) => {
  if (!url) return '';
  const parts = url.split('/');
  return parts[parts.length - 1];
};
</script>