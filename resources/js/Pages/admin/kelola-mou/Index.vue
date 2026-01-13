<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Download, Search } from 'lucide-vue-next'
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
  </AdminLayout>
</template>
