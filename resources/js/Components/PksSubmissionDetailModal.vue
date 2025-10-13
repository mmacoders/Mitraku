<template>
  <Modal :show="show" @close="closeModal" max-width="md">
    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
          Detail Pengajuan PKS
        </h3>
      </div>

      <!-- Modal Body -->
      <div class="p-6 sm:p-8 overflow-y-auto max-h-[70vh]">
        <div class="space-y-4">
          <!-- Nama Mitra -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800">
            <div class="flex items-start">
              <User class="h-5 w-5 text-indigo-500 dark:text-indigo-400 mt-0.5 mr-3 flex-shrink-0" />
              <div>
                <h4 class="font-medium text-gray-800 dark:text-gray-100">Nama Mitra</h4>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                  {{ submission?.partner_name || submission?.user?.name || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Judul PKS -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800">
            <div class="flex items-start">
              <FileText class="h-5 w-5 text-indigo-500 dark:text-indigo-400 mt-0.5 mr-3 flex-shrink-0" />
              <div>
                <h4 class="font-medium text-gray-800 dark:text-gray-100">Judul PKS</h4>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ submission?.title || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Tujuan -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800">
            <div class="flex items-start">
              <Target class="h-5 w-5 text-indigo-500 dark:text-indigo-400 mt-0.5 mr-3 flex-shrink-0" />
              <div>
                <h4 class="font-medium text-gray-800 dark:text-gray-100">Tujuan</h4>
                <p class="text-gray-600 dark:text-gray-300 mt-1 whitespace-pre-line">
                  {{ submission?.description || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Waktu Pengajuan -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800">
            <div class="flex items-start">
              <CalendarDays class="h-5 w-5 text-indigo-500 dark:text-indigo-400 mt-0.5 mr-3 flex-shrink-0" />
              <div>
                <h4 class="font-medium text-gray-800 dark:text-gray-100">Waktu Pengajuan</h4>
                <p class="text-gray-600 dark:text-gray-300 mt-1">
                  {{ formatDate(submission?.created_at) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800">
            <div class="flex items-start">
              <BadgeCheck class="h-5 w-5 text-indigo-500 dark:text-indigo-400 mt-0.5 mr-3 flex-shrink-0" />
              <div>
                <h4 class="font-medium text-gray-800 dark:text-gray-100">Update Status</h4>
                <div class="mt-1">
                  <span 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusClass(submission?.status)"
                  >
                    {{ getStatusText(submission?.status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Document Links -->
          <div v-if="submission?.kak_document_path || submission?.mou_document_path || submission?.final_document_path" class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
            <h4 class="font-medium text-gray-800 dark:text-gray-100 mb-3">Dokumen</h4>
            <div class="space-y-3">
              <div v-if="submission.kak_document_path">
                <a 
                  :href="`/storage/${submission.kak_document_path}`" 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  <File class="h-4 w-4 mr-2" />
                  Lihat Dokumen KAK
                </a>
              </div>
              <div v-if="submission.mou_document_path">
                <a 
                  :href="`/storage/${submission.mou_document_path}`" 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  <File class="h-4 w-4 mr-2" />
                  Lihat Dokumen MoU
                </a>
              </div>
              <div v-if="submission.final_document_path">
                <a 
                  :href="`/storage/${submission.final_document_path}`" 
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  <FileCheck class="h-4 w-4 mr-2" />
                  Lihat Dokumen Final
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
        <div class="flex justify-end">
          <button
            @click="closeModal"
            type="button"
            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition ease-in-out duration-150"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Modal from '@/Components/Modal.vue'
import { 
  X, 
  User, 
  FileText, 
  Target, 
  CalendarDays, 
  BadgeCheck,
  File,
  FileCheck
} from 'lucide-vue-next'
import { PksSubmission } from '@/types'

interface Props {
  show: boolean
  submission?: PksSubmission
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const closeModal = () => {
  emit('close')
}

const formatDate = (dateString: string | undefined) => {
  if (!dateString) return '-'
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const getStatusText = (status: string | undefined) => {
  const statusMap: Record<string, string> = {
    'proses': 'Menunggu',
    'ditolak': 'Ditolak',
    'disetujui': 'Disetujui',
    'revisi': 'Revisi'
  }
  return statusMap[status || ''] || status || 'Menunggu'
}

const getStatusClass = (status: string | undefined) => {
  switch (status) {
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    case 'revisi':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}
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