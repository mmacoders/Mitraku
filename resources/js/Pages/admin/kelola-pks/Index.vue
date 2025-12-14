<template>
  <Head title="Daftar Pengajuan PKS" />
  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 sm:p-6 mb-6">
        <h2 class="font-bold text-xl sm:text-2xl text-gray-900 dark:text-white leading-tight">
          {{ user.role === 'admin' ? 'Daftar Pengajuan PKS' : 'Pengajuan PKS Saya' }}
        </h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
          Kelola semua pengajuan PKS dengan mudah dan terorganisir.
        </p>
      </div>
    </template>

    <div class="py-4 sm:py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 sm:mb-6">
              <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                  {{ activeTab === 'berjalan' ? 'PKS yang Berjalan' : 'Semua Pengajuan Kerja Sama' }}
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                  {{ activeTab === 'berjalan' ? 'Daftar PKS yang sedang berjalan' : 'Kelola semua pengajuan PKS Anda' }}
                </p>
              </div>
              <button 
                v-if="activeTab === 'berjalan'"
                @click="openAddExistingPksModal"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 mt-4 md:mt-0"
              >
                <Plus class="h-4 w-4 mr-1" />
                Tambah PKS yang Sudah Berjalan
              </button>
              
              <a
                v-if="user.role === 'admin'"
                :href="getPdfExportUrl()"
                target="_blank"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 mt-4 md:mt-0 md:ml-3"
              >
                <Download class="h-4 w-4 mr-1" />
                Export PDF
              </a>
            </div>

            <!-- Tabs for different views -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
              <nav class="flex space-x-8" aria-label="Tabs">
                <button
                  @click="activeTab = 'semua'"
                  :class="[
                    activeTab === 'semua' 
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                  ]"
                >
                  Semua Pengajuan
                </button>
                <button
                  @click="activeTab = 'berjalan'"
                  :class="[
                    activeTab === 'berjalan' 
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                  ]"
                >
                  PKS yang Berjalan
                </button>
              </nav>
            </div>

            <!-- Filter and Search -->
            <div class="mb-6 flex flex-col sm:flex-row gap-3">
              <div class="flex-1">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input
                    id="search"
                    v-model="searchQuery"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 ease-in-out"
                    placeholder="Cari berdasarkan judul..."
                    @input="filterSubmissions"
                  />
                </div>
              </div>
              
              <div class="w-full sm:w-40" v-show="activeTab !== 'berjalan'">
                <select
                  id="status"
                  v-model="statusFilter"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-2 text-sm"
                  @change="filterSubmissions"
                >
                  <option value="">Semua Status</option>
                  <option value="proses">Proses</option>
                  <!-- Removed 'revisi' option -->
                  <option value="ditolak">Ditolak</option>
                  <option value="disetujui">Disetujui</option>
                </select>
              </div>
              
              <div class="w-full sm:w-40">
                <input
                  id="date"
                  v-model="dateFilter"
                  type="date"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-2 text-sm"
                  @change="filterSubmissions"
                />
              </div>
            </div>  

            <!-- Submissions Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Judul PKS
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Nama Mitra
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Waktu Pengajuan
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Masa Berlaku
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="submission in filteredSubmissions" :key="submission.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                    <td class="px-4 py-3 whitespace-normal">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ submission.title }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ submission.description ? submission.description.substring(0, 50) + (submission.description.length > 50 ? '...' : '') : '-' }}
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-normal">
                      <div v-if="user.role === 'admin'" class="text-sm text-gray-900 dark:text-white">
                        {{ submission.user?.name || '-' }}
                      </div>
                      <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(submission.created_at) }}
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-normal text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(submission.created_at) }}
                    </td>
                    <td class="px-4 py-3 whitespace-normal">
                      <div v-if="submission.status === 'disetujui' && submission.validity_period_start && submission.validity_period_end" class="text-sm">
                        <div>{{ formatDate(submission.validity_period_start) }} - {{ formatDate(submission.validity_period_end) }}</div>
                        <div 
                          v-if="isExpiringSoon(submission.validity_period_end)"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1"
                        >
                          ⚠️ Akan kedaluwarsa
                        </div>
                        <div 
                          v-else-if="isExpired(submission.validity_period_end)"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1"
                        >
                          ⚠️ Sudah kedaluwarsa
                        </div>
                      </div>
                      <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                        -
                      </div>
                    </td>
                    <td class="px-4 py-3 whitespace-normal">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(submission.status)"
                      >
                        {{ getStatusText(submission.status) }}
                      </span>
                    </td>
                    <td class="px-4 py-3 whitespace-normal text-center text-sm font-medium">
                      <div class="flex items-center justify-center gap-1">
                        <button
                          @click="openDetailModal(submission)"
                          class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-1"
                        >
                          <Eye class="w-4 h-4" /> 
                        </button>
                        <button
                          v-if="canDeleteSubmission(submission)"
                          @click="deleteSubmission(submission.id)"
                          class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-1"
                        >
                          <Trash2 class="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredSubmissions.length === 0">
                    <td colspan="6" class="px-4 py-12 text-center">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tidak ada data pengajuan yang ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                          Tidak ada data pengajuan yang sesuai dengan pencarian Anda.
                        </p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
              <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                Menampilkan <span class="font-medium">{{ filteredSubmissions.length }}</span> dari <span class="font-medium">{{ submissions.data.length }}</span> pengajuan
              </div>
              <div class="flex space-x-2">
                <!-- Pagination controls would go here if needed -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </AdminLayout>

    <!-- PKS Submission Detail Modal -->
    <PksSubmissionDetailModal 
      :show="showDetailModal" 
      :submission="selectedSubmission" 
      context="admin"
      @close="closeDetailModal" 
    />
    
    
    <!-- Add Existing PKS Modal -->
    <Modal :show="showAddExistingPksModal" @close="closeAddExistingPksModal" max-width="md">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
              Tambah PKS yang Sudah Berjalan
            </h3>
            <button @click="closeAddExistingPksModal" class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
              <X class="h-5 w-5" />
            </button>
          </div>
        </div>
        <form @submit.prevent="submitExistingPks">
          <div class="px-6 py-4 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul PKS *</label>
              <input
                v-model="existingPksForm.title"
                type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 text-sm"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Mitra *</label>
              <select
                v-model="existingPksForm.user_id"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 text-sm"
                required
              >
                <option value="">Pilih Mitra</option>
                <option v-for="mitra in mitraUsers" :key="mitra.id" :value="mitra.id">
                  {{ mitra.name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Berlaku *</label>
              <input
                v-model="existingPksForm.validity_period_start"
                type="date"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 text-sm"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir Berlaku *</label>
              <input
                v-model="existingPksForm.validity_period_end"
                type="date"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 text-sm"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
              <textarea
                v-model="existingPksForm.description"
                rows="3"
                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3 text-sm"
              ></textarea>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dokumen KAK (Opsional)</label>
              <input
                type="file"
                @change="handleKakDocumentChange"
                class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-lg file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100
                  dark:file:bg-blue-900/30 dark:file:text-blue-300
                  dark:hover:file:bg-blue-800/50
                  rounded-lg border border-gray-300 dark:border-gray-600
                  shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                  dark:bg-gray-700"
              />
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PDF atau DOCX. Maksimal 2MB</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dokumen MoU (Opsional)</label>
              <input
                type="file"
                @change="handleMouDocumentChange"
                class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-lg file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100
                  dark:file:bg-blue-900/30 dark:file:text-blue-300
                  dark:hover:file:bg-blue-800/50
                  rounded-lg border border-gray-300 dark:border-gray-600
                  shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                  dark:bg-gray-700"
              />
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PDF atau DOCX. Maksimal 2MB</p>
            </div>
          </div>
          <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button
              @click="closeAddExistingPksModal"
              type="button"
              class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
              Batal
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 active:bg-blue-700 transition ease-in-out duration-150"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </Modal>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Modal from '@/Components/Modal.vue'
import PksSubmissionDetailModal from '@/Components/admin/PksSubmissionDetailModal.vue'
import { Eye, Edit3, Trash2, Plus, X, Download } from 'lucide-vue-next'
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

const props = defineProps({
  submissions: Object,
  auth: Object
})

const user = props.auth.user
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const activeTab = ref('semua') // 'semua' or 'berjalan'

// Modal state
const showDetailModal = ref(false)
const showAddExistingPksModal = ref(false)
const selectedSubmission = ref(null)

// Form data for existing PKS
const existingPksForm = ref({
  title: '',
  user_id: '',
  validity_period_start: '',
  validity_period_end: '',
  description: '',
  kak_document: null,
  mou_document: null
})

// Mitra users data
const mitraUsers = ref([])

const filteredSubmissions = computed(() => {
  let result = props.submissions.data
  
  // Apply tab filter for active PKS
  if (activeTab.value === 'berjalan') {
    result = result.filter(submission => 
      submission.status === 'disetujui' && 
      submission.validity_period_start && 
      submission.validity_period_end &&
      new Date(submission.validity_period_end) > new Date() &&
      new Date(submission.validity_period_start) <= new Date()
    )
  }
  
  // Apply search filter
  if (searchQuery.value) {
    result = result.filter(submission => 
      submission.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  // Apply status filter (only when not on active tab)
  if (statusFilter.value && activeTab.value !== 'berjalan') {
    result = result.filter(submission => submission.status === statusFilter.value)
  }
  
  // Apply date filter
  if (dateFilter.value) {
    result = result.filter(submission => {
      const submissionDate = new Date(submission.created_at).toISOString().split('T')[0]
      return submissionDate === dateFilter.value
    })
  }
  
  return result
})

const deleteSubmission = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) {
    router.delete(route('pks.destroy', id))
  }
}

const filterSubmissions = () => {
  // Filtering is handled by the computed property
}

const formatDate = (dateString) => {
  if (!dateString) return '-';
  try {
    const date = new Date(dateString);
    return format(date, 'dd MMM yyyy', { locale: id });
  } catch (e) {
    return '-';
  }
}

// Check if PKS is expiring soon (within 30 days)
const isExpiringSoon = (endDate) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  const diffTime = Math.abs(end.getTime() - today.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 30 && end > today;
};

// Check if PKS has expired
const isExpired = (endDate) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  return end < today;
};

// Get status class
const getStatusClass = (status) => {
  switch (status) {
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    // Removed 'revisi' case
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    case 'Pembahasan':
      return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

// Get status text
const getStatusText = (status) => {
  switch (status) {
    case 'proses':
      return 'Proses'
    // Removed 'revisi' case
    case 'ditolak':
      return 'Ditolak'
    case 'disetujui':
      return 'Disetujui'
    case 'Pembahasan':
      return 'Pembahasan'
    default:
      return status
  }
}

// Check if user can delete submission
const canDeleteSubmission = (submission) => {
  if (user.role === 'mitra') {
    return submission.status === 'proses'
  } else if (user.role === 'admin') {
    return true
  }
  return false
}

// Modal functions
const openDetailModal = (submission) => {
  selectedSubmission.value = submission
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedSubmission.value = null
}

// Add Existing PKS Modal functions
const openAddExistingPksModal = () => {
  showAddExistingPksModal.value = true
}

const closeAddExistingPksModal = () => {
  showAddExistingPksModal.value = false
  // Reset form
  existingPksForm.value = {
    title: '',
    user_id: '',
    validity_period_start: '',
    validity_period_end: '',
    description: '',
    kak_document: null,
    mou_document: null
  }
}

// Submit existing PKS form
const submitExistingPks = () => {
  // Validate dates
  if (new Date(existingPksForm.value.validity_period_end) <= new Date(existingPksForm.value.validity_period_start)) {
    alert('Tanggal akhir harus setelah tanggal mulai')
    return
  }
  
  // Submit form with file uploads
  const formData = new FormData()
  
  // Add form fields
  formData.append('title', existingPksForm.value.title)
  formData.append('user_id', existingPksForm.value.user_id)
  formData.append('validity_period_start', existingPksForm.value.validity_period_start)
  formData.append('validity_period_end', existingPksForm.value.validity_period_end)
  formData.append('description', existingPksForm.value.description || '')
  formData.append('purpose', existingPksForm.value.description || '')
  formData.append('status', 'disetujui')
  formData.append('is_existing_pks', 'true')
  
  // Add documents if provided
  if (existingPksForm.value.kak_document) {
    formData.append('kak_document', existingPksForm.value.kak_document)
  }
  
  if (existingPksForm.value.mou_document) {
    formData.append('mou_document', existingPksForm.value.mou_document)
  }
  
  router.post(route('pks.store'), formData, {
    forceFormData: true,
    onSuccess: () => {
      closeAddExistingPksModal()
      // Reload submissions
      router.reload({
        only: ['submissions']
      })
    },
    onError: (errors) => {
      console.error('Error submitting form:', errors)
    },
    preserveState: true,
    preserveScroll: true
  })
}

// Fetch mitra users
const fetchMitraUsers = async () => {
  try {
    const response = await fetch(route('api.mitra.users'))
    const data = await response.json()
    mitraUsers.value = data
  } catch (error) {
    console.error('Error fetching mitra users:', error)
  }
}

// Handle file changes
const handleKakDocumentChange = (event) => {
  existingPksForm.value.kak_document = event.target.files[0]
}

const handleMouDocumentChange = (event) => {
  existingPksForm.value.mou_document = event.target.files[0]
}

// Get PDF Export URL with current filters
const getPdfExportUrl = () => {
  const params = new URLSearchParams()
  
  if (searchQuery.value) params.append('search', searchQuery.value)
  if (statusFilter.value && activeTab.value !== 'berjalan') params.append('status', statusFilter.value)
  if (dateFilter.value) params.append('date', dateFilter.value)
  
  // If active tab is 'berjalan', we might want to filter by valid period in backend too, 
  // but for now let's just export what's visible or all if 'semua'
  // Actually, filtering logic in frontend is complex. 
  // For 'berjalan', we rely on standard filters passed to backend.
  
  return route('pks.export-pdf') + '?' + params.toString()
}

// Fetch mitra users when component mounts
fetchMitraUsers()
</script>