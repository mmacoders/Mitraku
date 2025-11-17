<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Pengajuan PKS</h3>
        <!-- Context-specific subtitle -->
        <p v-if="context === 'mitra'" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Lihat informasi pengajuan PKS Anda
        </p>
        <p v-else class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Lihat informasi pengajuan PKS Mitra
        </p>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="space-y-5">
          <!-- Card 1: Informasi Umum -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="space-y-4">
              <!-- Nama Mitra -->
              <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                  <User class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama Mitra</p>
                  <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                    {{ submission?.partner_name || submission?.user?.name || '-' }}
                  </p>
                </div>
              </div>

              <!-- Judul PKS -->
              <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                  <FileText class="h-3.5 w-3.5 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Judul PKS</p>
                  <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                    {{ submission?.title || '-' }}
                  </p>
                </div>
              </div>

              <!-- Tujuan -->
              <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                  <Target class="h-3.5 w-3.5 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tujuan</p>
                  <p class="text-base text-gray-900 dark:text-gray-100 mt-1 whitespace-pre-line">
                    {{ submission?.description || '-' }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Informasi Waktu -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="space-y-4">
              <!-- Waktu Pengajuan -->
              <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                  <CalendarDays class="h-3.5 w-3.5 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Waktu Pengajuan</p>
                  <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                    {{ submission ? formatDate(submission.created_at) : '-' }}
                  </p>
                </div>
              </div>

              <!-- Validity Period (only shown when status is disetujui) -->
              <div v-if="submission?.status === 'disetujui' && submission?.validity_period_start && submission?.validity_period_end" class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center">
                  <Calendar class="h-3.5 w-3.5 text-teal-600 dark:text-teal-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Masa Berlaku</p>
                  <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                    {{ formatValidityPeriod(submission) }}
                  </p>
                  <div 
                    v-if="isExpiringSoon(submission.validity_period_end)"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200 mt-2"
                  >
                    ⚠️ Akan kedaluwarsa dalam {{ getDaysUntilExpiration(submission.validity_period_end) }} hari
                  </div>
                  <div 
                    v-else-if="isExpired(submission.validity_period_end)"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200 mt-2"
                  >
                    ⚠️ Sudah kedaluwarsa
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Status dan Dokumen -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="space-y-5">
              <!-- Status -->
              <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                  <BadgeCheck class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
                  <span 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                    :class="getStatusClass(submission?.status)"
                  >
                    {{ getStatusText(submission?.status) }}
                  </span>
                </div>
              </div>

              <!-- Document Links -->
              <div v-if="(submission?.kak_document_path || submission?.mou_document_path || submission?.final_document_path)" class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                  <File class="h-3.5 w-3.5 text-gray-600 dark:text-gray-400" />
                </div>
                <div class="ml-3 flex-1">
                  <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Dokumen</p>
                  <div class="mt-3 space-y-2">
                    <div v-if="submission.kak_document_path">
                      <a 
                        :href="`/pks-documents/${submission.kak_document_path}`" 
                        target="_blank"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/40 dark:text-blue-300"
                      >
                        <File class="h-3.5 w-3.5 mr-1.5" />
                        Lihat Dokumen KAK
                      </a>
                    </div>
                    <div v-if="submission.mou_document_path">
                      <a 
                        :href="`/pks-documents/${submission.mou_document_path}`" 
                        target="_blank"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/40 dark:text-blue-300"
                      >
                        <File class="h-3.5 w-3.5 mr-1.5" />
                        Lihat Dokumen MoU
                      </a>
                    </div>
                    <div v-if="submission.final_document_path">
                      <a 
                        :href="`/pks-documents/${submission.final_document_path}`" 
                        target="_blank"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/40 dark:text-blue-300"
                      >
                        <FileCheck class="h-3.5 w-3.5 mr-1.5" />
                        Lihat Dokumen Final
                      </a>
                    </div>
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
          class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors duration-200 text-sm dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
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
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
  show: boolean;
  submission?: PksSubmission;
  context?: 'mitra' | 'admin'; // Add context prop
}

const props = withDefaults(defineProps<Props>(), {
  context: 'admin' // Default to admin context
});

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const closeModal = () => {
  emit('close');
};

// Get user from page props
const page = usePage();
const user = computed(() => page.props.auth?.user);

// Format date
const formatDate = (dateString: string | undefined) => {
  if (!dateString) return '-';
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'short', 
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

// Check if PKS is expiring soon (within 30 days)
const isExpiringSoon = (endDate: string) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  const diffTime = Math.abs(end.getTime() - today.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 30 && end > today;
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