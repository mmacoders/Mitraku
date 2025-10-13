<template>
  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
          Daftar Pengajuan PKS
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Halaman ini digunakan untuk mengelola data pengajuan PKS. 
          Anda dapat menambah, mencari, mengubah, dan menghapus data pengajuan yang tersedia.
        </p>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Daftar Pengajuan</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola semua pengajuan PKS Anda</p>
              </div>
              <PrimaryButton @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                + Buat Pengajuan Baru
              </PrimaryButton>
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
                    v-model="searchQuery"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 ease-in-out"
                    placeholder="Cari berdasarkan judul atau mitra..."
                    @input="filterSubmissions"
                  />
                </div>
              </div>
              <div class="w-full sm:w-48">
                <select
                  id="status"
                  v-model="statusFilter"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3"
                  @change="filterSubmissions"
                >
                  <option value="">Semua</option>
                  <option value="proses">Proses</option>
                  <option value="disetujui">Disetujui</option>
                  <option value="ditolak">Ditolak</option>
                </select>
              </div>
            </div>

            <!-- Submissions Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      ID Dokumen
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Nama Mitra
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Tanggal
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="submission in filteredSubmissions" :key="submission.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        #{{ submission.id }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">
                        {{ submission.user.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(submission.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <StatusBadge :status="submission.status" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <Link
                        :href="route('pks.show', submission.id)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                      >
                        Detail
                      </Link>
                      <button
                        v-if="submission.status === 'proses'"
                        @click="deleteSubmission(submission.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      >
                        Hapus
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredSubmissions.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center justify-center">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tidak ada pengajuan ditemukan</h3>
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
            <div class="mt-6 flex justify-between items-center">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Menampilkan {{ filteredSubmissions.length }} dari {{ submissions.data.length }} pengajuan
              </div>
              <!-- Pagination controls would go here if needed -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PKS Submission Modal -->
    <PksSubmissionModal 
      :is-open="isCreateModalOpen" 
      @close="closeCreateModal"
      @success="handleCreateSuccess"
    />
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import PksSubmissionModal from '@/Components/PksSubmissionModal.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  submissions: Object,
  statistics: Object,
  auth: Object
})

const user = props.auth.user
const searchQuery = ref('')
const statusFilter = ref('')

// Modal state
const isCreateModalOpen = ref(false)

const filteredSubmissions = computed(() => {
  let result = props.submissions.data
  
  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(submission => 
      (submission.title && submission.title.toLowerCase().includes(query)) || 
      (submission.user?.name && submission.user.name.toLowerCase().includes(query))
    )
  }
  
  // Apply status filter
  if (statusFilter.value) {
    result = result.filter(submission => submission.status === statusFilter.value)
  }
  
  return result
})

// Modal functions
const openCreateModal = () => {
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
}

const handleCreateSuccess = () => {
  // Refresh the page to show the new submission
  router.visit(route('kelola.pks'), {
    preserveState: true,
    preserveScroll: true
  })
}

const deleteSubmission = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) {
    router.delete(route('pks.destroy', id))
  }
}

const filterSubmissions = () => {
  // Filtering is handled by the computed property
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const handleCreateNew = () => {
    router.visit(route('pks.create'));
};

const handleRefresh = () => {
    router.visit(route('kelola.pks'));
};

</script>