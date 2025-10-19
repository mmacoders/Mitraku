<template>
  <Head title="Daftar Rapat" />

  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
          Daftar Rapat
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Kelola semua jadwal rapat Anda dengan mudah dan terorganisir.
        </p>
      </div>
    </template>

    <!-- Rapat Create Modal -->
    <RapatModal 
      :show="showCreateModal" 
      :on-close="closeCreateModal"
      :available-mitra="mitraUsers"
      :pks-submissions="pksSubmissions"
      @created="refreshRapatList"
    />

    <!-- Rapat Edit Modal -->
    <Edit 
      :show="showEditModal" 
      :on-close="closeEditModal"
      :rapat="selectedRapat"
      :available-mitra="mitraUsers"
      :pks-submissions="pksSubmissions"
      @updated="refreshRapatList"
    />

    <!-- Rapat Detail Modal -->
    <RapatDetailModal
      :show="showDetailModal"
      :rapat="selectedRapat"
      @close="closeDetailModal"
    />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Daftar Rapat</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola semua jadwal rapat Anda</p>
              </div>
              <button
                @click="openCreateModal"
                class="inline-flex items-center rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-800 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5"
              >
                + Buat Rapat Baru
              </button>
            </div>

            <!-- Filter and Search -->
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
                  id="status"
                  v-model="filters.status"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3"
                  @change="applyFilters"
                >
                  <option value="">Semua Status</option>
                  <option value="akan_datang">Proses</option>
                  <option value="selesai">Selesai</option>
                  <option value="dibatalkan">Dibatalkan</option>
                </select>
              </div>
              
              <div class="w-full sm:w-48">
                <input
                  id="date"
                  v-model="filters.date"
                  type="date"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3"
                  @change="applyFilters"
                />
              </div>
            </div>

            <!-- Rapat Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Judul Rapat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Tanggal & Waktu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Lokasi/Link
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Status
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
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ rapat.deskripsi ? rapat.deskripsi.substring(0, 50) + (rapat.deskripsi.length > 50 ? '...' : '') : '-' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(rapat.tanggal_waktu) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ rapat.lokasi || '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                        :class="getStatusClass(rapat.status)"
                      >
                        {{ getStatusText(rapat.status) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                       <div class="flex items-center justify-center gap">
                      <button
                        @click="openDetailModal(rapat)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                      >
                        <Eye class="w-4 h-4" /> 
                      </button>
                      <button
                        @click="openEditModal(rapat)"
                        class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 mr-3"
                      >
                        <Edit3 class="w-4 h-4" />
                      </button>
                      <button
                        @click="deleteRapat(rapat.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                       </div>
                    </td>
                  </tr>
                  <tr v-if="rapat.data.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import RapatModal from '@/Components/RapatModal.vue';
import Edit from './Edit.vue';
import RapatDetailModal from '@/Components/RapatDetailModal.vue';
import {Eye, Edit3, Trash2} from 'lucide-vue-next'

const props = defineProps({
  rapat: Object,
  mitraUsers: Array,
  pksSubmissions: Array,
});

// Modal states
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDetailModal = ref(false);
const selectedRapat = ref(null);

// Open create modal
const openCreateModal = () => {
  showCreateModal.value = true;
};

// Close create modal
const closeCreateModal = () => {
  showCreateModal.value = false;
};

// Open edit modal
const openEditModal = (rapat) => {
  // Prepare the rapat data with the document URL
  const rapatWithDocument = {
    ...rapat,
    pks_document_url: rapat.pks_document_url || null
  };
  selectedRapat.value = rapatWithDocument;
  showEditModal.value = true;
};

// Close edit modal
const closeEditModal = () => {
  showEditModal.value = false;
  selectedRapat.value = null;
};

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

// Refresh rapat list after creation/update
const refreshRapatList = () => {
  router.reload({ only: ['rapat'] });
};

// Filters
const filters = ref({
  search: '',
  status: '',
  date: '',
});

// Apply filters with debounce
let timeout = null;
const applyFilters = () => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    router.get(
      route('rapat.index'),
      {
        search: filters.value.search,
        status: filters.value.status,
        date: filters.value.date,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 300);
};

// Delete rapat
const deleteRapat = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus rapat ini?')) {
    router.delete(route('rapat.destroy', id));
  }
};

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
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
      return 'Proses';
    case 'selesai':
      return 'Selesai';
    case 'dibatalkan':
      return 'Dibatalkan';
    default:
      return status;
  }
};
</script>