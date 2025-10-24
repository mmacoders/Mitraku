<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
            <FileText class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
          </div>
          <div class="ml-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Pengajuan PKS</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat informasi lengkap pengajuan PKS.</p>
          </div>
        </div>
        <div class="mt-4 h-0.5 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-full"></div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Nama Mitra Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <User class="h-4 w-4 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Nama Mitra</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ submission?.partner_name || submission?.user?.name || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Judul PKS Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <FileText class="h-4 w-4 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Judul PKS</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ submission?.title || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Tujuan Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md md:col-span-2">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <Target class="h-4 w-4 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-4 flex-1">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Tujuan</h4>
                <div class="mt-1 max-h-40 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">
                  <p class="text-base text-gray-800 dark:text-gray-200 whitespace-pre-line">
                    {{ submission?.description || '-' }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Waktu Pengajuan Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <CalendarDays class="h-4 w-4 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Waktu Pengajuan</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ submission ? formatDate(submission.created_at) : '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Validity Period Card (only shown when status is disetujui) -->
          <div v-if="submission?.status === 'disetujui' && submission?.validity_period_start && submission?.validity_period_end" class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                <Calendar class="h-4 w-4 text-teal-600 dark:text-teal-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Masa Berlaku</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ formatValidityPeriod(submission) }}
                </p>
                <div 
                  v-if="isExpiringSoon(submission.validity_period_end)"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-2"
                >
                  ⚠️ Akan kedaluwarsa dalam {{ getDaysUntilExpiration(submission.validity_period_end) }} hari
                </div>
                <div 
                  v-else-if="isExpired(submission.validity_period_end)"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-2"
                >
                  ⚠️ Sudah kedaluwarsa
                </div>
              </div>
            </div>
          </div>

          <!-- Status Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <BadgeCheck class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Status</h4>
                <span 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-1"
                  :class="getStatusClass(submission?.status)"
                >
                  {{ getStatusText(submission?.status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Document Links Card -->
          <div v-if="(submission?.kak_document_path || submission?.mou_document_path || submission?.final_document_path)" class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md md:col-span-2">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                <File class="h-4 w-4 text-gray-600 dark:text-gray-400" />
              </div>
              <div class="ml-4 flex-1">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Dokumen</h4>
                <div class="mt-3 space-y-3">
                  <div v-if="submission.kak_document_path">
                    <a 
                      :href="`/pks-documents/${submission.kak_document_path}`" 
                      target="_blank"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm"
                    >
                      <File class="h-4 w-4 mr-2" />
                      Lihat Dokumen KAK
                    </a>
                  </div>
                  <div v-if="submission.mou_document_path">
                    <a 
                      :href="`/pks-documents/${submission.mou_document_path}`" 
                      target="_blank"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm"
                    >
                      <File class="h-4 w-4 mr-2" />
                      Lihat Dokumen MoU
                    </a>
                  </div>
                  <div v-if="submission.final_document_path">
                    <a 
                      :href="`/pks-documents/${submission.final_document_path}`" 
                      target="_blank"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm"
                    >
                      <FileCheck class="h-4 w-4 mr-2" />
                      Lihat Dokumen Final
                    </a>
                  </div>
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

<script setup lang="ts">
import { X, User, FileText, Target, CalendarDays, BadgeCheck, File, FileCheck, Calendar } from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';
import { PksSubmission } from '@/types';

interface Props {
  show: boolean;
  submission?: PksSubmission;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const closeModal = () => {
  emit('close');
};

// Format date
const formatDate = (dateString: string | undefined) => {
  if (!dateString) return '-';
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Format validity period
const formatValidityPeriod = (submission: PksSubmission) => {
  if (submission.status === 'disetujui' && submission.validity_period_start && submission.validity_period_end) {
    const start = new Date(submission.validity_period_start).toLocaleDateString('id-ID');
    const end = new Date(submission.validity_period_end).toLocaleDateString('id-ID');
    return `${start} - ${end}`;
  }
  return '-';
};

// Check if PKS is expiring soon (within 7 days)
const isExpiringSoon = (endDate: string) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  const diffTime = Math.abs(end.getTime() - today.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 7 && end > today;
};

// Check if PKS has expired
const isExpired = (endDate: string) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  return end < today;
};

// Get days until expiration
const getDaysUntilExpiration = (endDate: string) => {
  if (!endDate) return 0;
  const end = new Date(endDate);
  const today = new Date();
  const diffTime = Math.abs(end.getTime() - today.getTime());
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

// Get status text
const getStatusText = (status: string | undefined) => {
  const statusMap: Record<string, string> = {
    'proses': 'Menunggu',
    'ditolak': 'Ditolak',
    'disetujui': 'Disetujui'
    // Removed 'revisi' status
  };
  return statusMap[status || ''] || status || 'Menunggu';
};

// Get status class
const getStatusClass = (status: string | undefined) => {
  switch (status) {
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
    // Removed 'revisi' case
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
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