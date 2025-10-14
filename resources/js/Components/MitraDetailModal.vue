<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
            <Users class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
          </div>
          <div class="ml-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Mitra</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat informasi lengkap mitra yang terdaftar.</p>
          </div>
        </div>
        <div class="mt-4 h-0.5 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-full"></div>
      </div>

      <!-- Modal Body -->
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="space-y-5">
          <!-- Nama Mitra Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <User class="h-4 w-4 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Nama Mitra</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ mitra?.name || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Email Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <Mail class="h-4 w-4 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Email</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ mitra?.email || '-' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Instansi / Perusahaan Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <Building class="h-4 w-4 text-purple-600 dark:text-purple-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Instansi / Perusahaan</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ mitra?.company || 'Tidak ditentukan' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Jumlah PKS Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <FileText class="h-4 w-4 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-4">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Jumlah PKS</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
                  {{ mitra?.pks_count || '0' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Tanggal Bergabung Card -->
          <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 transition-all duration-200 hover:shadow-md">
            <div class="flex items-start">
              <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                <Calendar class="h-4 w-4 text-gray-600 dark:text-gray-400" />
              </div>
              <div class="ml-4 flex-1">
                <h4 class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 font-medium">Tanggal Bergabung</h4>
                <p class="text-base text-gray-800 dark:text-gray-200 font-medium mt-1">
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
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
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