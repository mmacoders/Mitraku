<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Mitra</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat informasi lengkap mitra yang terdaftar.</p>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="space-y-5">
          <!-- Nama Mitra Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                <User class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama Mitra</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ mitra?.name || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Email Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                <Mail class="h-3.5 w-3.5 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ mitra?.email || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Instansi / Perusahaan Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                <Building class="h-3.5 w-3.5 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Instansi / Perusahaan</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ mitra?.company || 'Tidak ditentukan' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Jumlah PKS Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                <FileText class="h-3.5 w-3.5 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Jumlah PKS</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ mitra?.pks_count || '0' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Tanggal Bergabung Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <Calendar class="h-3.5 w-3.5 text-gray-600 dark:text-gray-400" />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Bergabung</p>
                <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                  {{ mitra ? formatDate(mitra.created_at) : '-' }}
                </p>
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
import { X, Users, User, Mail, Building, FileText, Calendar } from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: Boolean,
  mitra: Object
});

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
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