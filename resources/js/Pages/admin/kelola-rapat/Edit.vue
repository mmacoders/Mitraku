<template>
  <Head :title="'Edit Rapat: ' + rapat.judul" />

  <AdminLayout>
    <!-- Breadcrumb -->
    <nav class="mb-6 flex" aria-label="Breadcrumb">
      <ol class="flex items-center space-x-2">
        <li>
          <div class="flex items-center">
            <svg class="h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            <a href="#" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Dashboard</a>
          </div>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <a :href="route('rapat.index')" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Rapat</a>
          </div>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Edit Rapat</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Rapat</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Form -->
      <div class="lg:col-span-2">
        <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Rapat</h2>
          </div>
          <div class="p-6 space-y-6">
            <!-- PKS Submission -->
            <div>
              <InputLabel for="pks_submission_id" value="Pengajuan PKS (Opsional)" />
              <select
                id="pks_submission_id"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                v-model="form.pks_submission_id"
              >
                <option value="">Pilih pengajuan PKS (opsional)</option>
                <option v-for="submission in pksSubmissions" :key="submission.id" :value="submission.id">
                  {{ submission.title }} - {{ submission.user.name }}
                </option>
              </select>
              <InputError :message="form.errors.pks_submission_id" class="mt-2" />
            </div>

            <!-- Judul -->
            <div>
              <InputLabel for="judul" value="Judul Rapat" />
              <TextInput
                id="judul"
                type="text"
                class="mt-1 block w-full"
                v-model="form.judul"
                required
                autocomplete="judul"
              />
              <InputError :message="form.errors.judul" class="mt-2" />
            </div>

            <!-- Tanggal & Waktu -->
            <div>
              <InputLabel for="tanggal_waktu" value="Tanggal & Waktu" />
              <TextInput
                id="tanggal_waktu"
                type="datetime-local"
                class="mt-1 block w-full"
                v-model="form.tanggal_waktu"
                required
              />
              <InputError :message="form.errors.tanggal_waktu" class="mt-2" />
            </div>

            <!-- Lokasi -->
            <div>
              <InputLabel for="lokasi" value="Lokasi/Link Meeting" />
              <TextInput
                id="lokasi"
                type="text"
                class="mt-1 block w-full"
                v-model="form.lokasi"
                placeholder="Masukkan lokasi fisik atau link meeting"
              />
              <InputError :message="form.errors.lokasi" class="mt-2" />
            </div>

            <!-- Status -->
            <div>
              <InputLabel for="status" value="Status" />
              <select
                id="status"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                v-model="form.status"
                required
              >
                <option value="akan_datang">Akan Datang</option>
                <option value="selesai">Selesai</option>
                <option value="dibatalkan">Dibatalkan</option>
              </select>
              <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Deskripsi -->
            <div>
              <InputLabel for="deskripsi" value="Deskripsi" />
              <textarea
                id="deskripsi"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                v-model="form.deskripsi"
                rows="4"
                placeholder="Deskripsikan agenda rapat..."
              ></textarea>
              <InputError :message="form.errors.deskripsi" class="mt-2" />
            </div>

            <!-- Mitra Selection -->
            <div>
              <InputLabel value="Undang Mitra" />
              <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div
                  v-for="m in mitra"
                  :key="m.id"
                  @click="selectMitra(m.id)"
                  :class="[
                    'p-3 rounded-lg border cursor-pointer transition-colors',
                    selectedMitra.includes(m.id)
                      ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                      : 'border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                  ]"
                >
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                        <span class="text-green-800 dark:text-green-200 text-sm font-medium">{{ m.name.charAt(0) }}</span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ m.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ m.email }}</p>
                    </div>
                    <div v-if="selectedMitra.includes(m.id)" class="ml-auto">
                      <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
              <InputError :message="form.errors.mitra" class="mt-2" />
              
              <!-- Selected Mitra Tags -->
              <div v-if="selectedMitra.length > 0" class="mt-3 flex flex-wrap gap-2">
                <span
                  v-for="id in selectedMitra"
                  :key="id"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
                >
                  {{ props.mitra.find(m => m.id === id)?.name }}
                  <button
                    type="button"
                    @click="deselectMitra(id)"
                    class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white"
                  >
                    <span class="sr-only">Remove</span>
                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                      <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                    </svg>
                  </button>
                </span>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end">
              <Link
                :href="route('rapat.index')"
                class="mr-3 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Batal
              </Link>
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Update Rapat
              </PrimaryButton>
            </div>
          </div>
        </form>
      </div>

      <!-- Rapat Info -->
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Rapat</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat oleh</h3>
                <div class="mt-1 flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                      <span class="text-blue-800 dark:text-blue-200 text-sm font-medium">{{ rapat.creator.name.charAt(0) }}</span>
                    </div>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ rapat.creator.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ rapat.creator.email }}</p>
                  </div>
                </div>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat pada</h3>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(rapat.created_at) }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1" :class="getStatusClass(rapat.status)">
                  {{ getStatusText(rapat.status) }}
                </span>
              </div>
            </div>
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
  rapat: {
    id: number
    judul: string
    tanggal_waktu: string
    lokasi: string
    deskripsi: string
    status: string
    pks_submission_id: number | null
    creator: {
      name: string
      email: string
    }
    created_at: string
    invited_mitra: Array<{
      id: number
      name: string
      email: string
    }>
  }
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
}>()

// Form setup
const form = useForm({
  pks_submission_id: props.rapat.pks_submission_id || '',
  judul: props.rapat.judul,
  tanggal_waktu: props.rapat.tanggal_waktu,
  lokasi: props.rapat.lokasi,
  deskripsi: props.rapat.deskripsi,
  status: props.rapat.status,
  mitra: [] as number[]
})

// Selected mitra tracking - initialized with already invited mitra
const selectedMitra = ref<number[]>(props.rapat.invited_mitra.map(m => m.id))

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

// Get status class
const getStatusClass = (status: string) => {
  switch (status) {
    case 'akan_datang':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'selesai':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    case 'dibatalkan':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
  }
}

// Get status text
const getStatusText = (status: string) => {
  switch (status) {
    case 'akan_datang':
      return 'Akan Datang'
    case 'selesai':
      return 'Selesai'
    case 'dibatalkan':
      return 'Dibatalkan'
    default:
      return status
  }
}

// Submit function
const submit = () => {
  form.mitra = selectedMitra.value
  form.put(route('rapat.update', props.rapat.id))
}
</script>