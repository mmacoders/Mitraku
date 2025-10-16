<template>
  <Head title="Buat Rapat Baru" />

  <AdminLayout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <div class="text-center py-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
              Buat Rapat Baru
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
              Gunakan tombol di bawah untuk membuat rapat baru melalui modal.
            </p>
            <div class="flex justify-center">
              <button
                @click="openRapatModal"
                class="inline-flex items-center rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-800 transition-all duration-200 ease-in-out"
              >
                Buat Rapat Baru
              </button>
            </div>
            <div class="mt-6">
              <Link
                :href="route('rapat.index')"
                class="inline-flex items-center rounded-lg border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 active:bg-gray-800 transition-all duration-200 ease-in-out"
              >
                Kembali ke Daftar Rapat
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>

  <!-- Rapat Modal -->
  <RapatModal 
    :show="showRapatModal"
    :available-mitra="mitraUsers"
    :pks-submissions="pksSubmissions"
    @close="closeRapatModal"
    @created="handleRapatCreated"
  />
</template>

<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import RapatModal from '@/Components/RapatModal.vue'

const props = defineProps<{
  pksSubmissions: Array<{
    id: number
    title: string
    user: {
      id: number
      name: string
    }
    status: string
  }>
  recentRapat: Array<{
    id: number
    judul: string
    tanggal_waktu: string
    invited_mitra: Array<{
      id: number
      name: string
    }>
  }>
  mitraUsers: Array<{
    id: number
    name: string
    company: string
  }>
}>()

// Modal state
const showRapatModal = ref(false)

// Modal functions
const openRapatModal = () => {
  showRapatModal.value = true
}

const closeRapatModal = () => {
  showRapatModal.value = false
}

const handleRapatCreated = () => {
  // Refresh the page or update the UI as needed
  window.location.reload()
}

// Format date function
const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}
</script>