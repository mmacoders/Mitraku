<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { FileText, Download, CheckCircle, XCircle, Search } from 'lucide-vue-next'
import Pagination from '@/Components/Pagination.vue'
import debounce from 'lodash/debounce'

const props = defineProps({
  mous: {
    type: Object,
    default: () => ({ data: [], links: [] })
  },
  filters: {
    type: Object,
    default: () => ({ search: '', date: '', status: '' })
  }
})

const search = ref(props.filters.search)
const date = ref(props.filters.date)
const status = ref(props.filters.status)

// Debounce search to update URL
const updateSearch = debounce(() => {
  router.get(route('admin.mou.index'), { 
    search: search.value, 
    date: date.value, 
    status: status.value 
  }, { 
    preserveState: true, 
    preserveScroll: true, 
    replace: true 
  })
}, 300)

watch([date, status], () => {
   updateSearch()
})

const showApprovalModal = ref(false)
const selectedMou = ref<any>(null)
const form = useForm({
  status: '',
  validity_period_start: '',
  validity_period_end: ''
})

const openApprovalModal = (mou: any, status: 'disetujui' | 'ditolak') => {
  selectedMou.value = mou
  form.status = status
  
  if (status === 'disetujui') {
      // Set default dates if needed, e.g., today and 1 year from now
      const today = new Date().toISOString().split('T')[0]
      const nextYear = new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0]
      form.validity_period_start = today
      form.validity_period_end = nextYear
  } else {
      form.validity_period_start = ''
      form.validity_period_end = ''
  }

  showApprovalModal.value = true
}

const closeApprovalModal = () => {
  showApprovalModal.value = false
  selectedMou.value = null
  form.reset()
  form.clearErrors()
}

const submitStatusUpdate = () => {
  if (!selectedMou.value) return

  form.post(route('admin.mou.update-status', selectedMou.value.id), {
    onSuccess: () => {
      closeApprovalModal()
    }
  })
}

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'disetujui': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
    case 'ditolak': return 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'
    default: return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
  }
}

const getStatusLabel = (status: string) => {
    switch (status) {
    case 'disetujui': return 'Disetujui'
    case 'ditolak': return 'Ditolak'
    default: return 'Proses'
  }
}
</script>

<template>
  <Head title="Kelola MoU" />

  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 sm:p-6 mb-6">
        <h2 class="font-bold text-xl sm:text-2xl text-gray-900 dark:text-white leading-tight">
          Kelola MoU Mitra
        </h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
          Kelola semua pengajuan Memorandum of Understanding (MoU) dari mitra.
        </p>
      </div>
    </template>

    <div class="py-4 sm:py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-4 sm:p-6">
            <div class="mb-6">
                <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                Daftar Pengajuan MoU
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Daftar semua pengajuan MoU yang masuk untuk ditinjau dan dikelola.
                </p>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-col sm:flex-row gap-3">
              <div class="flex-1">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <Search class="h-5 w-5 text-gray-400" />
                  </div>
                  <input
                    v-model="search"
                    @input="updateSearch"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 ease-in-out"
                    placeholder="Cari berdasarkan judul atau nama mitra..."
                  />
                </div>
              </div>
              
              <div class="w-full sm:w-40">
                <select
                  v-model="status"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-2 text-sm"
                >
                  <option value="">Semua Status</option>
                  <option value="proses">Proses</option>
                  <option value="disetujui">Disetujui</option>
                  <option value="ditolak">Ditolak</option>
                </select>
              </div>
              
              <div class="w-full sm:w-40">
                <input
                  v-model="date"
                  type="date"
                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-2 text-sm"
                />
              </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mitra</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Judul MoU</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pengajuan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="mou in mous?.data" :key="mou.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ mou.user.name }}</div>
                      <div class="text-xs text-gray-500">{{ mou.user.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ mou.title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(mou.created_at) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusBadgeClass(mou.status)">
                        {{ getStatusLabel(mou.status) }}
                      </span>
                       <div v-if="mou.status === 'disetujui' && mou.validity_period_end" class="text-xs text-gray-500 mt-1">
                          Exp: {{ formatDate(mou.validity_period_end) }}
                       </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                       <a :href="`/${mou.document_path}`" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                          <Download class="w-4 h-4 mr-1" /> Dokumen
                      </a>
                      
                      <button 
                          v-if="mou.status === 'proses'"
                          @click="openApprovalModal(mou, 'disetujui')"
                          class="inline-flex items-center text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                      >
                          <CheckCircle class="w-4 h-4 mr-1" /> Setujui
                      </button>
                      
                      <button 
                          v-if="mou.status === 'proses'"
                          @click="openApprovalModal(mou, 'ditolak')"
                          class="inline-flex items-center text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      >
                          <XCircle class="w-4 h-4 mr-1" /> Tolak
                      </button>
                    </td>
                  </tr>
                   <tr v-if="!mous?.data?.length">
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                      Tidak ada pengajuan MoU.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
             <div class="mt-4" v-if="mous?.links">
                 <Pagination :links="mous.links" />
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Approval/Rejection Modal -->
    <div v-if="showApprovalModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeApprovalModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="submitStatusUpdate">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="form.status === 'disetujui' ? 'bg-green-100' : 'bg-red-100'">
                  <CheckCircle v-if="form.status === 'disetujui'" class="h-6 w-6 text-green-600" aria-hidden="true" />
                  <XCircle v-else class="h-6 w-6 text-red-600" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                    {{ form.status === 'disetujui' ? 'Setujui Pengajuan MoU' : 'Tolak Pengajuan MoU' }}
                  </h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ form.status === 'disetujui' 
                          ? 'Anda akan menyetujui pengajuan MoU ini. Silakan tentukan masa berlaku MoU.' 
                          : 'Apakah Anda yakin ingin menolak pengajuan MoU ini?' }}
                    </p>
                  </div>

                  <div v-if="form.status === 'disetujui'" class="mt-4 space-y-4">
                      <div>
                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Berlaku</label>
                          <input type="date" v-model="form.validity_period_start" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required />
                          <p v-if="form.errors.validity_period_start" class="mt-1 text-sm text-red-600">{{ form.errors.validity_period_start }}</p>
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Berakhir Berlaku</label>
                          <input type="date" v-model="form.validity_period_end" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required />
                          <p v-if="form.errors.validity_period_end" class="mt-1 text-sm text-red-600">{{ form.errors.validity_period_end }}</p>
                      </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50" :class="form.status === 'disetujui' ? 'bg-green-600 hover:bg-green-700 ring-green-500' : 'bg-red-600 hover:bg-red-700 ring-red-500'">
                {{ form.processing ? 'Memproses...' : (form.status === 'disetujui' ? 'Setujui' : 'Tolak') }}
              </button>
              <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700" @click="closeApprovalModal">
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
