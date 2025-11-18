<template>
  <Head title="Kelola Dokumen Pasca-Rapat" />

  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
          Kelola Dokumen Pasca-Rapat
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Kelola dokumen hasil rapat: Draft Fix, Jadwal Penandatanganan, dan Dokumen yang Ditandatangani.
        </p>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filter Section -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
              </div>
              <input
                id="search"
                v-model="filters.search"
                type="text"
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 ease-in-out"
                placeholder="Cari berdasarkan judul rapat..."
                @input="applyFilters"
              />
            </div>
          </div>
          
          <div class="w-full sm:w-48">
            <select
              id="filter"
              v-model="filters.process"
              class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3"
              @change="applyFilters"
            >
              <option value="">Semua Proses</option>
              <option value="draft_fix">Draft Fix</option>
              <option value="signing_schedule">Jadwal TTD</option>
              <option value="signed_document">Dokumen Final</option>
            </select>
          </div>
        </div>

        <!-- Dokumen Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Judul Rapat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Tanggal Rapat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Draft Fix
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Jadwal TTD
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Dokumen Final
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="rapat in rapat.data" :key="rapat.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ rapat.judul }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(rapat.tanggal_waktu) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <!-- Draft Fix Actions -->
                        <template v-if="!rapat.draft_document_path">
                          <button
                            @click="openProcessModal('draft_fix', rapat)"
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                          >
                            <Upload class="h-3 w-3 mr-1" />
                            Upload
                          </button>
                        </template>
                        <template v-else>
                          <a 
                            :href="rapat.draft_document_url" 
                            target="_blank"
                            class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                          >
                            <Eye class="h-3 w-3 mr-1" />
                            Lihat
                          </a>
                          <button
                            @click="deleteDraftDocument(rapat)"
                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                          >
                            <Trash2 class="h-3 w-3" />
                          </button>
                        </template>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <!-- Signing Schedule Actions -->
                        <template v-if="!rapat.signing_schedule">
                          <button
                            v-if="rapat.draft_document_path"
                            @click="openProcessModal('signing_schedule', rapat)"
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                          >
                            <Calendar class="h-3 w-3 mr-1" />
                            Atur
                          </button>
                          <span 
                            v-else
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                          >
                            Tunggu Draft Fix
                          </span>
                        </template>
                        <template v-else>
                          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ formatDate(rapat.signing_schedule) }}
                          </span>
                          <button
                            v-if="rapat.draft_document_path"
                            @click="openProcessModal('signing_schedule', rapat)"
                            class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                          >
                            <Edit3 class="h-3 w-3" />
                          </button>
                        </template>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <!-- Signed Document Actions -->
                        <template v-if="!rapat.signed_document_path">
                          <button
                            v-if="rapat.draft_document_path && rapat.signing_schedule"
                            @click="openProcessModal('signed_document', rapat)"
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                          >
                            <Upload class="h-3 w-3 mr-1" />
                            Upload
                          </button>
                          <span 
                            v-else
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                          >
                            Tunggu Proses Sebelumnya
                          </span>
                        </template>
                        <template v-else>
                          <a 
                            :href="rapat.signed_document_url" 
                            target="_blank"
                            class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                          >
                            <Eye class="h-3 w-3 mr-1" />
                            Lihat
                          </a>
                          <button
                            @click="deleteSignedDocument(rapat)"
                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                          >
                            <Trash2 class="h-3 w-3" />
                          </button>
                        </template>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="openDetailModal(rapat)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                      >
                        Detail
                      </button>
                    </td>
                  </tr>
                  <tr v-if="rapat.data.length === 0">
                    <td colspan="6" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tidak ada data rapat yang ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                          Tidak ada data rapat yang sesuai dengan pencarian Anda.
                        </p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Menampilkan <span class="font-medium">{{ rapat.from }}</span> sampai <span class="font-medium">{{ rapat.to }}</span> dari <span class="font-medium">{{ rapat.total }}</span> hasil
              </div>
              <div class="flex space-x-2">
                <Link
                  v-if="rapat.prev_page_url"
                  :href="rapat.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Sebelumnya
                </Link>
                <Link
                  v-if="rapat.next_page_url"
                  :href="rapat.next_page_url"
                  class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Selanjutnya
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <RapatDetailModal
      :show="showDetailModal"
      :rapat="selectedRapat"
      @close="closeDetailModal"
    />
    
    <!-- Process Modals -->
    <PostMeetingDocumentModal
      :show="showProcessModal"
      :process-type="currentProcessType"
      :rapat="selectedRapat"
      @close="closeProcessModal"
      @success="handleProcessSuccess"
    />
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import RapatDetailModal from '@/Components/admin/RapatDetailModal.vue';
import PostMeetingDocumentModal from '@/Components/admin/PostMeetingDocumentModal.vue';
import { Eye, Upload, Calendar, Edit3, Trash2 } from 'lucide-vue-next';
import ModernBreadcrumb from '@/Components/ModernBreadcrumb.vue';

const props = defineProps({
  rapat: Object,
});

// Modal states
const showDetailModal = ref(false);
const showProcessModal = ref(false);
const selectedRapat = ref(null);
const currentProcessType = ref('');
const openedMenu = ref(null);

// Open detail modal
const openDetailModal = (rapat) => {
  selectedRapat.value = rapat;
  showDetailModal.value = true;
};

// Close detail modal
const closeDetailModal = () => {
  showDetailModal.value = false;
  selectedRapat.value = null;
};

// Open action menu
const openActionMenu = (rapat) => {
  if (openedMenu.value === rapat.id) {
    openedMenu.value = null;
  } else {
    openedMenu.value = rapat.id;
  }
};

// Close action menu when clicking outside
document.addEventListener('click', (event) => {
  if (!event.target.closest('.relative')) {
    openedMenu.value = null;
  }
});

// Open process modal
const openProcessModal = (processType, rapat) => {
  // Check if the process can be performed based on the workflow
  if (processType === 'signing_schedule' && !rapat.draft_document_path) {
    alert('Draft Fix harus diupload terlebih dahulu sebelum mengatur jadwal TTD.');
    return;
  }
  
  if (processType === 'signed_document' && (!rapat.draft_document_path || !rapat.signing_schedule)) {
    alert('Draft Fix harus diupload dan jadwal TTD harus diatur terlebih dahulu sebelum mengupload dokumen final.');
    return;
  }
  
  openedMenu.value = null;
  currentProcessType.value = processType;
  selectedRapat.value = rapat;
  showProcessModal.value = true;
};

// Close process modal
const closeProcessModal = () => {
  showProcessModal.value = false;
  currentProcessType.value = '';
  selectedRapat.value = null;
};

// Handle process success
const handleProcessSuccess = () => {
  // Refresh the page to show updated data
  router.reload({
    only: ['rapat'],
    preserveState: true,
    preserveScroll: true
  });
};

// Delete draft document
const deleteDraftDocument = (rapat) => {
  if (confirm('Apakah Anda yakin ingin menghapus draft dokumen ini?')) {
    router.delete(route('rapat.deleteDraftDocument', rapat.id), {
      onSuccess: () => {
        router.reload({
          only: ['rapat'],
          preserveState: true,
          preserveScroll: true
        });
      }
    });
  }
};

// Delete signed document
const deleteSignedDocument = (rapat) => {
  if (confirm('Apakah Anda yakin ingin menghapus dokumen yang ditandatangani ini?')) {
    router.delete(route('rapat.deleteSignedDocument', rapat.id), {
      onSuccess: () => {
        router.reload({
          only: ['rapat'],
          preserveState: true,
          preserveScroll: true
        });
      }
    });
  }
};

// Filters
const filters = ref({
  search: '',
  process: '',
});

// Apply filters with debounce
let timeout = null;
const applyFilters = () => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    router.get(
      route('kelola.dokumen.pasca.rapat'),
      {
        search: filters.value.search,
        process: filters.value.process,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 300);
};

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};
</script>