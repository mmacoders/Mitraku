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
                  Semua Pengajuan PKS
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                  Kelola semua pengajuan PKS Anda
                </p>
              </div>
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
              
              <div class="w-full sm:w-40">
                <select
                  id="status"
                  v-model="statusFilter"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-2 text-sm"
                  @change="filterSubmissions"
                >
                  <option value="">Semua Status</option>
                  <option value="proses">Proses</option>
                  <option value="revisi">Revisi</option>
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
                          v-if="canEditSubmission(submission)"
                          @click="openEditModal(submission)"
                          class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 p-1"
                        >
                          <Edit3 class="w-4 h-4" />
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
                    <td colspan="5" class="px-4 py-12 text-center">
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
      @close="closeDetailModal" 
    />
    
    <!-- PKS Submission Edit Modal -->
    <EditPksStatusModal 
      :show="showEditModal" 
      :submission="selectedSubmission" 
      @close="closeEditModal" 
      @success="handleEditSuccess"
    />
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import PksSubmissionDetailModal from '@/Components/PksSubmissionDetailModal.vue'
import EditPksStatusModal from '@/Components/EditPksStatusModal.vue'
import { Eye, Edit3, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  submissions: Object,
  auth: Object
})

const user = props.auth.user
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')

// Modal state
const showDetailModal = ref(false)
const showEditModal = ref(false)
const selectedSubmission = ref(null)

const filteredSubmissions = computed(() => {
  let result = props.submissions.data
  
  // Apply search filter
  if (searchQuery.value) {
    result = result.filter(submission => 
      submission.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  // Apply status filter
  if (statusFilter.value) {
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
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

// Get status class
const getStatusClass = (status) => {
  switch (status) {
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'revisi':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

// Get status text
const getStatusText = (status) => {
  switch (status) {
    case 'proses':
      return 'Proses'
    case 'revisi':
      return 'Revisi'
    case 'ditolak':
      return 'Ditolak'
    case 'disetujui':
      return 'Disetujui'
    default:
      return status
  }
}

// Check if user can edit submission
const canEditSubmission = (submission) => {
  if (user.role === 'mitra') {
    return submission.status === 'revisi'
  } else if (user.role === 'admin') {
    return true
  }
  return false
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

const openEditModal = (submission) => {
  selectedSubmission.value = submission
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  selectedSubmission.value = null
}

const handleEditSuccess = () => {
  // Refresh the submissions data
  router.reload({
    only: ['submissions'],
    preserveState: true,
    preserveScroll: true
  })
}
</script>