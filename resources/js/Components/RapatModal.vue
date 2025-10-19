<template>
  <Modal :show="show" @close="closeModal" max-width="2xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <!-- Modal header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-2xl mr-3 text-blue-600">📅</span>
            <div>
              <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
                Buat Rapat Baru
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Isi detail rapat
              </p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
            <span class="text-2xl">&times;</span>
          </button>
        </div>
      </div>

      <!-- Modal body -->
      <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Invite Mitra (Moved to top) -->
          <div>
            <InputLabel for="invited_mitra" value="Undang Mitra" />
            <div class="mt-2">
              <!-- Search input for mitra -->
              <TextInput
                id="search_mitra"
                v-model="searchMitra"
                type="text"
                class="mt-1 block w-full"
                placeholder="Cari mitra..."
                @input="filterMitra"
              />
              
              <!-- Selected mitra tags -->
              <div class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="mitraId in form.invited_mitra"
                  :key="mitraId"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                >
                  {{ getMitraName(mitraId) }}
                  <button
                    type="button"
                    @click="removeMitra(mitraId)"
                    class="ml-1 inline-flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500 focus:bg-blue-500 focus:text-white focus:outline-none dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700 dark:hover:text-blue-100 dark:focus:bg-blue-500"
                  >
                    <span class="sr-only">Hapus</span>
                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                      <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                    </svg>
                  </button>
                </span>
              </div>
              
              <!-- Dropdown for mitra selection -->
              <div 
                v-show="filteredMitra.length > 0 && searchMitra" 
                class="mt-1 max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 shadow-lg z-10 absolute w-[calc(100%-3rem)]"
              >
                <div 
                  v-for="mitra in filteredMitra" 
                  :key="mitra.id" 
                  class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer"
                  @click="selectMitra(mitra)"
                >
                  {{ mitra.name }} ({{ mitra.company || 'Perusahaan tidak diatur' }})
                </div>
                <div 
                  v-if="filteredMitra.length === 0" 
                  class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                >
                  Tidak ada mitra yang ditemukan
                </div>
              </div>
            </div>
            <InputError class="mt-2" :message="form.errors.invited_mitra" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Cari dan pilih mitra yang ingin Anda undang ke rapat ini
            </p>
          </div>

          <!-- Judul PKS - Only show when mitra is selected -->
          <div v-if="form.invited_mitra.length > 0 && pksSubmissions && pksSubmissions.length > 0">
            <InputLabel for="pks_submission_id" value="Judul PKS" />
            <select
              id="pks_submission_id"
              v-model="form.pks_submission_id"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
            >
              <option value="">Pilih PKS yang akan di bahas</option>
              <option v-for="pks in filteredPksSubmissions" :key="pks.id" :value="pks.id">
                {{ pks.title }} - {{ pks.user ? pks.user.name : 'User tidak ditemukan' }}
              </option>
            </select>
            <InputError class="mt-2" :message="form.errors.pks_submission_id" />
          </div>

          <!-- Judul Rapat -->
          <div>
            <InputLabel for="judul" value="Judul Rapat" />
            <TextInput
              id="judul"
              v-model="form.judul"
              type="text"
              class="mt-1 block w-full"
              required
              :disabled="form.processing"
            />
            <InputError class="mt-2" :message="form.errors.judul" />
          </div>

          <!-- Tanggal & Waktu -->
          <div>
            <InputLabel for="tanggal_waktu" value="Tanggal & Waktu" />
            <TextInput
              id="tanggal_waktu"
              v-model="form.tanggal_waktu"
              type="datetime-local"
              class="mt-1 block w-full"
              required
              :disabled="form.processing"
            />
            <InputError class="mt-2" :message="form.errors.tanggal_waktu" />
          </div>

          <!-- Lokasi/Link Meeting -->
          <div>
            <InputLabel for="lokasi" value="Link Meeting / Lokasi Meeting" />
            <TextInput
              id="lokasi"
              v-model="form.lokasi"
              type="text"
              class="mt-1 block w-full"
              required
              :disabled="form.processing"
              placeholder="Masukkan lokasi fisik atau link meeting"
            />
            <InputError class="mt-2" :message="form.errors.lokasi" />
          </div>

          <!-- Status -->
          <div>
            <InputLabel for="status" value="Status" />
            <select
              id="status"
              v-model="form.status"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
            >
              <option value="akan_datang">Proses</option>
              <option value="selesai">Selesai</option>
              <option value="dibatalkan">Dibatalkan</option>
            </select>
            <InputError class="mt-2" :message="form.errors.status" />
          </div>

          <!-- Deskripsi Agenda Rapat -->
          <div>
            <InputLabel for="deskripsi" value="Deskripsi Agenda Rapat (Opsional)" />
            <textarea
              id="deskripsi"
              v-model="form.deskripsi"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              rows="4"
              :disabled="form.processing"
              placeholder="Deskripsikan agenda rapat..."
            ></textarea>
            <InputError class="mt-2" :message="form.errors.deskripsi" />
          </div>

          <!-- Submit buttons -->
          <div class="flex items-center justify-end space-x-3 pt-4">
            <SecondaryButton @click="closeModal" :disabled="form.processing">
              Batal
            </SecondaryButton>
            <PrimaryButton 
              :class="{ 'opacity-25': form.processing }" 
              :disabled="form.processing"
              type="submit"
            >
              <span v-if="form.processing">Menyimpan...</span>
              <span v-else>Simpan</span>
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps<{
  show: boolean
  onClose: () => void
  availableMitra: Array<{
    id: number
    name: string
    company: string
  }>
  pksSubmissions?: Array<{
    id: number
    title: string
    user: {
      id: number
      name: string
    }
    status: string
  }>
}>()

const emit = defineEmits(['created'])

const searchMitra = ref('')

// Form setup
const form = useForm({
  judul: '',
  tanggal_waktu: '',
  lokasi: '',
  deskripsi: '',
  status: 'akan_datang',
  pks_submission_id: '' as string | number,
  invited_mitra: [] as number[]
})

// Filtered mitra based on search
const filteredMitra = computed(() => {
  if (!searchMitra.value) return []
  return props.availableMitra.filter(mitra => 
    mitra.name.toLowerCase().includes(searchMitra.value.toLowerCase()) ||
    (mitra.company && mitra.company.toLowerCase().includes(searchMitra.value.toLowerCase()))
  )
})

// Filtered PKS submissions - only show "proses" status submissions from selected mitra
const filteredPksSubmissions = computed(() => {
  if (!props.pksSubmissions || form.invited_mitra.length === 0) return []
  
  // Get user IDs of selected mitra
  const selectedMitraIds = form.invited_mitra
  
  // Filter PKS submissions:
  // 1. Only "proses" status submissions
  // 2. Only from selected mitra
  return props.pksSubmissions.filter(pks => 
    pks.status === 'proses' && 
    pks.user && 
    selectedMitraIds.includes(pks.user.id)
  )
})

// Get mitra name by ID
const getMitraName = (mitraId: number) => {
  const mitra = props.availableMitra.find(m => m.id === mitraId)
  return mitra ? mitra.name : ''
}

// Select mitra
const selectMitra = (mitra: { id: number }) => {
  if (!form.invited_mitra.includes(mitra.id)) {
    form.invited_mitra.push(mitra.id)
  }
  searchMitra.value = ''
  
  // Clear PKS selection when mitra changes
  form.pks_submission_id = ''
}

// Remove mitra
const removeMitra = (mitraId: number) => {
  const index = form.invited_mitra.indexOf(mitraId)
  if (index !== -1) {
    form.invited_mitra.splice(index, 1)
  }
  
  // Clear PKS selection if no mitra selected
  if (form.invited_mitra.length === 0) {
    form.pks_submission_id = ''
  }
}

// Filter mitra (called on input)
const filterMitra = () => {
  // Filtering is handled by the computed property
}

// Close modal
const closeModal = () => {
  props.onClose()
  form.reset()
  form.clearErrors()
  searchMitra.value = ''
}

// Submit form
const submit = () => {
  form.post(route('rapat.store'), {
    onSuccess: () => {
      closeModal()
      emit('created')
    },
    onError: (errors) => {
      console.error('Form submission error:', errors)
      // Show a user-friendly error message
      alert('Terjadi kesalahan saat menyimpan rapat. Silakan periksa kembali data yang dimasukkan.')
    }
  })
}
</script>