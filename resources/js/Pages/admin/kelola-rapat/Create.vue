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
            <Link
              :href="route('rapat.index')"
              class="inline-flex items-center rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-800 transition-all duration-200 ease-in-out"
            >
              Kembali ke Daftar Rapat
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

const props = defineProps<{
  mitra: Array<{
    id: number
    name: string
    email: string
  }>
  pksSubmissions: Array<{
    id: number
    title: string
    user: {
      name: string
    }
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
}>()

// Form setup
const form = useForm({
  pks_submission_id: '',
  judul: '',
  tanggal_waktu: '',
  lokasi: '',
  deskripsi: '',
  mitra: [] as number[]
})

// Selected mitra tracking
const selectedMitra = ref<number[]>([])

// Select mitra function
const selectMitra = (id: number) => {
  if (!selectedMitra.value.includes(id)) {
    selectedMitra.value.push(id)
  }
}

// Deselect mitra function
const deselectMitra = (id: number) => {
  selectedMitra.value = selectedMitra.value.filter(mitraId => mitraId !== id)
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

// Submit function
const submit = () => {
  form.mitra = selectedMitra.value
  form.post(route('rapat.store'))
}
</script>