<template>
  <Head title="Daftar Rapat" />

  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
          Kelola Rapat
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Kelola jadwal rapat dan dokumen pasca rapat dengan mudah dan terorganisir.
        </p>
      </div>
    </template>

    <!-- Type Selection Modal -->
    <MeetingTypeSelectionModal
      :show="showTypeSelectionModal"
      @close="closeTypeSelectionModal"
      @select="handleTypeSelection"
    />

    <!-- Rapat Create Modal -->
    <RapatModal 
      :show="showCreateModal" 
      :on-close="closeCreateModal"
      :available-mitra="mitraUsers"
      :pks-submissions="pksSubmissions"
      :mou-submissions="mouSubmissions"
      :type="creationType"
      @created="refreshRapatList"
    />

    <!-- Rapat Edit Modal -->
    <EditRapatModal 
      :show="showEditModal" 
      @close="closeEditModal"
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
    
    <!-- Process Modals -->
    <PostMeetingDocumentModal
      :show="showProcessModal"
      :process-type="currentProcessType"
      :rapat="selectedRapat"
      @close="closeProcessModal"
      @success="handleProcessSuccess"
    />
    
    <!-- PKS Status Modal -->
    <EditPksStatusModal 
      :show="showPksModal" 
      :submission="selectedPksSubmission" 
      @close="closePksModal" 
      @success="handlePksSuccess"
    />

    <!-- MoU Status Modal -->
    <EditMouStatusModal 
      :show="showMouModal" 
      :mou="selectedMouSubmission" 
      @close="closeMouModal" 
      @success="handleMouSuccess"
    />

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                  {{ activeTab === 'pasca' ? 'Dokumen Pasca Rapat' : 'Daftar Rapat' }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                  {{ activeTab === 'pasca' ? 'Kelola dokumen hasil rapat' : 'Kelola semua jadwal rapat Anda' }}
                </p>
              </div>
              <button
                v-if="activeTab === 'jadwal'"
                @click="openCreateModal"
                class="inline-flex items-center rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-800 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5"
              >
                + Buat Rapat Baru
              </button>
            </div>

            <!-- Tabs for different views -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
              <nav class="flex space-x-8" aria-label="Tabs">
                <button
                  @click="activeTab = 'jadwal'"
                  :class="[
                    activeTab === 'jadwal' 
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                  ]"
                >
                  Jadwal Rapat
                </button>
                <button
                  @click="activeTab = 'pasca'"
                  :class="[
                    activeTab === 'pasca' 
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                  ]"
                >
                  Pasca Rapat
                </button>
              </nav>
            </div>

            <!-- Jadwal Rapat View -->
            <div v-if="activeTab === 'jadwal'">
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
                  Menampilkan <span class="font-medium">{{ rapat.from }}</span> dari <span class="font-medium">{{ rapat.total }}</span> jadwal rapat
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

            <!-- Pasca Rapat View -->
            <div v-if="activeTab === 'pasca'">
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
                      id="search-pasca"
                      v-model="pascaFilters.search"
                      type="text"
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 ease-in-out"
                      placeholder="Cari berdasarkan judul rapat..."
                      @input="applyPascaFilters"
                    />
                  </div>
                </div>
                
                <div class="w-full sm:w-48">
                  <select
                    id="filter-pasca"
                    v-model="pascaFilters.process"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3"
                    @change="applyPascaFilters"
                  >
                    <option value="">Semua Proses</option>
                    <option value="draft_fix">Draft Fix</option>
                    <option value="signing_schedule">Jadwal TTD</option>
                    <option value="signed_document">Dokumen Final</option>
                  </select>
                </div>
              </div>

              <!-- Dokumen Table -->
              <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Judul Rapat
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        PKS Terkait
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        MoU Terkait
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
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Keputusan
                      </th>
                      <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="rapat in pascaRapat.data" :key="rapat.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ rapat.judul }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ rapat.pks_submission?.title || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ rapat.mou?.title || '-' }}
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
                          <template v-if="!rapat?.signing_schedule">
                            <button
                              v-if="rapat?.draft_document_path"
                              @click="openProcessModal('signing_schedule', rapat)"
                              class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                              <Calendar class="h-3 w-3 mr-1" />
                              Atur
                            </button>
                            <span v-else class="text-sm text-gray-400">-</span>
                          </template>
                          <template v-else>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                              {{ formatDate(rapat.signing_schedule) }}
                            </span>
                            <button
                              v-if="rapat?.draft_document_path"
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
                          <template v-if="!rapat?.signed_document_path">
                            <button
                              v-if="rapat?.draft_document_path && rapat?.signing_schedule"
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
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-4">
                            <!-- PKS Decision -->
                            <div v-if="rapat.pks_submission" class="flex flex-col items-start gap-1">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">PKS:</span>
                                <template v-if="rapat.pks_submission.status === 'proses'">
                                    <button
                                    v-if="rapat.signed_document_path"
                                    @click="openPksModal(rapat.pks_submission)"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                    <CheckCircle class="h-3 w-3 mr-1" />
                                    Ambil Keputusan
                                    </button>
                                    <span 
                                    v-else
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                    >
                                    Menunggu Final
                                    </span>
                                </template>
                                <div v-else>
                                    <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getPksStatusClass(rapat.pks_submission.status)"
                                    >
                                    {{ getPksStatusText(rapat.pks_submission.status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- MoU Decision -->
                            <div v-if="rapat.mou" class="flex flex-col items-start gap-1">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">MoU:</span>
                                <template v-if="rapat.mou.status === 'proses'">
                                    <button
                                    v-if="rapat.signed_document_path"
                                    @click="openMouModal(rapat.mou)"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <CheckCircle class="h-3 w-3 mr-1" />
                                        Ambil Keputusan
                                    </button>
                                    <span 
                                    v-else
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                    >
                                    Menunggu Final
                                    </span>
                                </template>
                                <div v-else>
                                    <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getPksStatusClass(rapat.mou.status)"
                                    >
                                    {{ getPksStatusText(rapat.mou.status) }}
                                    </span>
                                </div>
                            </div>
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
                    <tr v-if="pascaRapat.data.length === 0">
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
                  Menampilkan <span class="font-medium">{{ pascaRapat.from }}</span> sampai <span class="font-medium">{{ pascaRapat.to }}</span> dari <span class="font-medium">{{ pascaRapat.total }}</span> hasil
                </div>
                <div class="flex space-x-2">
                  <Link
                    v-if="pascaRapat.prev_page_url"
                    :href="pascaRapat.prev_page_url"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                  >
                    Sebelumnya
                  </Link>
                  <Link
                    v-if="pascaRapat.next_page_url"
                    :href="pascaRapat.next_page_url"
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
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import RapatModal from '@/Components/admin/RapatModal.vue';
import MeetingTypeSelectionModal from '@/Components/admin/MeetingTypeSelectionModal.vue';
import RapatDetailModal from '@/Components/admin/RapatDetailModal.vue';
import EditRapatModal from '@/Pages/admin/kelola-rapat/Edit.vue';
import PostMeetingDocumentModal from '@/Components/admin/PostMeetingDocumentModal.vue';
import EditPksStatusModal from '@/Components/admin/EditPksStatusModal.vue';
import EditMouStatusModal from '@/Components/admin/EditMouStatusModal.vue';
import { Eye, Edit3, Trash2, Upload, Calendar, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
  rapat: Object,
  mitraUsers: Array,
  pksSubmissions: Array,
  mouSubmissions: Array,
  pascaRapat: Object,
});

// Tab management
const activeTab = ref('jadwal');

// Modal states
const showTypeSelectionModal = ref(false);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDetailModal = ref(false);
const showProcessModal = ref(false);
const showPksModal = ref(false);
const showMouModal = ref(false);
const selectedRapat = ref(null);
const selectedPksSubmission = ref(null);
const selectedMouSubmission = ref(null);
const currentProcessType = ref('');
const openedMenu = ref(null);
const creationType = ref('pks'); // 'pks' or 'mou'

// Filters for jadwal rapat
const filters = ref({
  search: '',
  status: '',
  date: '',
});

// Filters for pasca rapat
const pascaFilters = ref({
  search: '',
  process: '',
});

// Open create modal
const openCreateModal = () => {
  showTypeSelectionModal.value = true;
};

const closeTypeSelectionModal = () => {
  showTypeSelectionModal.value = false;
};

const handleTypeSelection = (type) => {
  creationType.value = type;
  showTypeSelectionModal.value = false;
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

// Open process modal
const openProcessModal = (processType, rapat) => {
  // Check if the process can be performed based on the workflow
  if (processType === 'signing_schedule' && !rapat?.draft_document_path) {
    alert('Draft Fix harus diupload terlebih dahulu sebelum mengatur jadwal TTD.');
    return;
  }
  
  if (processType === 'signed_document' && (!rapat?.draft_document_path || !rapat?.signing_schedule)) {
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
    only: ['pascaRapat'],
    preserveState: true,
    preserveScroll: true
  });
};

// Refresh rapat list after creation/update
const refreshRapatList = () => {
  router.reload({ 
    only: ['rapat'],
    preserveState: true,
    preserveScroll: true
  });
};

// Apply filters for jadwal rapat with debounce
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

// Apply filters for pasca rapat with debounce
let pascaTimeout = null;
const applyPascaFilters = () => {
  clearTimeout(pascaTimeout);
  pascaTimeout = setTimeout(() => {
    router.get(
      route('rapat.index'),
      {
        search_pasca: pascaFilters.value.search,
        process: pascaFilters.value.process,
        tab: 'pasca'
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
    // Use POST with _method spoofing to avoid preflight issues on some hosting environments
    router.post(route('rapat.destroy', id), {
      _method: 'delete'
    });
  }
};

// Delete draft document
const deleteDraftDocument = (rapat) => {
  if (confirm('Apakah Anda yakin ingin menghapus draft dokumen ini?')) {
    // Use POST with _method spoofing
    router.post(route('rapat.deleteDraftDocument', rapat?.id), {
      _method: 'delete'
    }, {
      onSuccess: () => {
        router.reload({
          only: ['pascaRapat'],
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
    // Use POST with _method spoofing
    router.post(route('rapat.deleteSignedDocument', rapat?.id), {
      _method: 'delete'
    }, {
      onSuccess: () => {
        router.reload({
          only: ['pascaRapat'],
          preserveState: true,
          preserveScroll: true
        });
      }
    });
  }
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '-';
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

// Open PKS modal
const openPksModal = (submission) => {
  selectedPksSubmission.value = submission;
  showPksModal.value = true;
};

// Close PKS modal
const closePksModal = () => {
  showPksModal.value = false;
  selectedPksSubmission.value = null;
};

// Handle PKS success
const handlePksSuccess = () => {
  router.reload({
    only: ['pascaRapat'],
    preserveState: true,
    preserveScroll: true
  });
};

// Open MoU modal
const openMouModal = (mou) => {
  selectedMouSubmission.value = mou;
  showMouModal.value = true;
};

// Close MoU modal
const closeMouModal = () => {
  showMouModal.value = false;
  selectedMouSubmission.value = null;
};

// Handle MoU success
const handleMouSuccess = () => {
  router.reload({
    only: ['pascaRapat'],
    preserveState: true,
    preserveScroll: true
  });
};

// Get PKS status class
const getPksStatusClass = (status) => {
  switch (status) {
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
};

// Get PKS status text
const getPksStatusText = (status) => {
  switch (status) {
    case 'proses': return 'Proses';
    case 'ditolak': return 'Ditolak';
    case 'disetujui': return 'Disetujui';
    default: return status;
  }
};
</script>