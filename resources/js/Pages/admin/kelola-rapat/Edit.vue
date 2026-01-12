<template>
  <Modal :show="show" @close="closeModal" max-width="lg">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <!-- Modal header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-2xl mr-3 text-blue-600">📅</span>
            <div>
              <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
                Edit Rapat
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Perbarui detail rapat
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
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                placeholder="Cari mitra berdasarkan nama atau perusahaan..."
              />
              
              <!-- Mitra selection list -->
              <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md">
                <div 
                  v-for="mitra in filteredMitra" 
                  :key="mitra.id"
                  @click="toggleMitraSelection(mitra.id)"
                  class="flex items-center px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30': form.invited_mitra.includes(mitra.id) }"
                >
                  <input
                    type="checkbox"
                    :checked="form.invited_mitra.includes(mitra.id)"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    @click.stop
                  />
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ mitra.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ mitra.company }}</p>
                  </div>
                </div>
                
                <!-- No results message -->
                <div v-if="filteredMitra.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                  Tidak ada mitra yang ditemukan
                </div>
              </div>
              
              <!-- Selected mitra tags -->
              <div v-if="selectedMitraNames.length > 0" class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="(name, index) in selectedMitraNames"
                  :key="index"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                >
                  {{ name }}
                  <button
                    type="button"
                    @click="removeMitra(index)"
                    class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-blue-400 hover:bg-blue-200 hover:text-blue-500 focus:outline-none focus:bg-blue-500 focus:text-white dark:hover:bg-blue-800 dark:hover:text-blue-200 dark:focus:bg-blue-600 dark:focus:text-white"
                  >
                    <span class="sr-only">Hapus</span>
                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                      <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                    </svg>
                  </button>
                </span>
              </div>
            </div>
            <InputError class="mt-2" :message="form.errors.invited_mitra" />
          </div>

          <!-- Judul Rapat -->
          <div>
            <InputLabel for="judul" value="Judul Rapat *" />
            <TextInput
              id="judul"
              v-model="form.judul"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
              placeholder="Masukkan judul rapat..."
              required
            />
            <InputError class="mt-2" :message="form.errors.judul" />
          </div>

          <!-- Tanggal & Waktu -->
          <div>
            <InputLabel for="tanggal_waktu" value="Tanggal & Waktu *" />
            <TextInput
              id="tanggal_waktu"
              v-model="form.tanggal_waktu"
              type="datetime-local"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
              required
            />
            <InputError class="mt-2" :message="form.errors.tanggal_waktu" />
          </div>

          <!-- Lokasi/Link Meeting -->
          <div>
            <InputLabel for="lokasi" value="Lokasi/Link Meeting" />
            <TextInput
              id="lokasi"
              v-model="form.lokasi"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
              placeholder="Masukkan lokasi fisik atau link meeting (Zoom, Google Meet, dll)..."
            />
            <InputError class="mt-2" :message="form.errors.lokasi" />
          </div>

          <!-- Deskripsi -->
          <div>
            <InputLabel for="deskripsi" value="Deskripsi" />
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

          <!-- Status -->
          <div>
            <InputLabel for="status" value="Status" />
            <select
              id="status"
              v-model="form.status"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
            >
              <option value="akan_datang">Akan Datang</option>
              <option value="selesai">Selesai</option>
              <option value="dibatalkan">Dibatalkan</option>
            </select>
            <InputError class="mt-2" :message="form.errors.status" />
          </div>

          <!-- PKS Submission (if PKS meeting) -->
          <div v-if="!isMouMeeting && pksSubmissions && pksSubmissions.length > 0">
            <InputLabel for="pks_submission_id" value="Pengajuan PKS Terkait" />
            <select
              id="pks_submission_id"
              v-model="form.pks_submission_id"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
            >
              <option value="">Pilih pengajuan PKS (opsional)</option>
              <option
                v-for="submission in filteredPksSubmissions"
                :key="submission.id"
                :value="submission.id"
              >
                {{ submission.title }}
              </option>
            </select>
            <InputError class="mt-2" :message="form.errors.pks_submission_id" />
          </div>

          <!-- MOU Submission (if MOU meeting) -->
          <div v-if="isMouMeeting && mouSubmissions && mouSubmissions.length > 0">
            <InputLabel for="mou_id" value="Pengajuan MoU Terkait" />
            <select
              id="mou_id"
              v-model="form.mou_id"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              :disabled="form.processing"
            >
              <option value="">Pilih pengajuan MoU</option>
              <option
                v-for="submission in filteredMouSubmissions"
                :key="submission.id"
                :value="submission.id"
              >
                {{ submission.title }}
              </option>
            </select>
            <InputError class="mt-2" :message="form.errors.mou_id" />
          </div>

          <!-- Dokumen PKS (Existing) -->
          <div v-if="existingDocumentUrl">
            <InputLabel value="Dokumen PKS Saat Ini" />
            <div class="mt-2 flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <div class="flex items-center">
                <span class="text-2xl mr-3">{{ getFileIcon('application/pdf') }}</span>
                <div>
                  <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ getFileNameFromUrl(existingDocumentUrl) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    Dokumen PKS yang sudah diunggah
                  </div>
                </div>
              </div>
              <div class="flex space-x-2">
                <a 
                  :href="existingDocumentUrl" 
                  target="_blank"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <Eye class="h-3 w-3 mr-1" />
                  Lihat
                </a>
              </div>
            </div>
          </div>

          <!-- Upload Dokumen PKS -->
          <div>
            <InputLabel for="pks_document" value="Upload Dokumen PKS" />
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-md">
              <div class="space-y-1 text-center">
                <div v-if="!form.pks_document" class="flex text-sm text-gray-600 dark:text-gray-400">
                  <label
                    for="pks_document"
                    class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                  >
                    <span>Upload file</span>
                    <input
                      id="pks_document"
                      ref="fileInput"
                      type="file"
                      class="sr-only"
                      @change="handleFileChange"
                      :disabled="form.processing"
                      accept=".pdf,.doc,.docx"
                    />
                  </label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p v-if="!form.pks_document" class="text-xs text-gray-500 dark:text-gray-400">
                  PDF, DOC, DOCX hingga 2MB
                </p>
                <div v-if="form.pks_document" class="flex items-center justify-between">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ getFileIcon(form.pks_document.type) }}</span>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ form.pks_document.name }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatFileSize(form.pks_document.size) }}
                      </div>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="removeFile('pks_document')"
                    class="ml-4 bg-white dark:bg-gray-800 rounded-md text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none"
                  >
                    <X class="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
            <InputError class="mt-2" :message="form.errors.pks_document" />
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
import { ref, onMounted, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { Eye, Trash2, X } from 'lucide-vue-next'

const props = defineProps<{
  show: boolean
  rapat: {
    id: number
    judul: string
    tanggal_waktu: string
    lokasi: string
    deskripsi: string
    status: string
    pks_document_url?: string
    pks_submission_id?: number | null
    mou_id?: number | null
    invited_mitra?: Array<{ id: number }>
  } | null
  availableMitra?: Array<{
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

const emit = defineEmits(['updated', 'close'])

const fileInput = ref<HTMLInputElement | null>(null)
const existingDocumentUrl = ref<string | null>(null)
const searchMitra = ref('')
const isMouMeeting = ref(false)

// Form setup
const form = useForm({
  judul: '',
  tanggal_waktu: '',
  lokasi: '',
  deskripsi: '',
  status: 'akan_datang',
  pks_document: null as File | null,
  invited_mitra: [] as number[],
  pks_submission_id: '' as string | number,
  mou_id: '' as string | number
})

// Filtered mitra based on search
const filteredMitra = computed(() => {
  if (!props.availableMitra) return []
  
  return props.availableMitra.filter(mitra => 
    mitra.name.toLowerCase().includes(searchMitra.value.toLowerCase()) ||
    (mitra.company && mitra.company.toLowerCase().includes(searchMitra.value.toLowerCase()))
  )
})

// Get selected mitra names for display
const selectedMitraNames = computed(() => {
  if (!props.availableMitra) return []
  
  return props.availableMitra
    .filter(mitra => form.invited_mitra.includes(mitra.id))
    .map(mitra => mitra.name)
})

// Filter PKS submissions based on selected mitra
const filteredPksSubmissions = computed(() => {
  if (!props.pksSubmissions) return []
  
  if (form.invited_mitra.length === 0) {
    return []
  }
  
  const selectedMitraId = form.invited_mitra[0]
  
  return props.pksSubmissions.filter(submission => submission.user.id === selectedMitraId)
})

// Filter MOU submissions based on selected mitra
const filteredMouSubmissions = computed(() => {
  if (!props.mouSubmissions) return []
  
  if (form.invited_mitra.length === 0) {
    return []
  }
  
  const selectedMitraId = form.invited_mitra[0]
  
  return props.mouSubmissions.filter(submission => submission.user.id === selectedMitraId)
})

// Toggle mitra selection (Enforce Single Selection)
const toggleMitraSelection = (mitraId: number) => {
  // If clicked one is already selected, deselect it
  if (form.invited_mitra.includes(mitraId)) {
    form.invited_mitra = []
    form.pks_submission_id = '' // Reset PKS when mitra removed
    form.mou_id = '' // Reset MOU when mitra removed
  } else {
    // Select the new one (replacing any existing)
    form.invited_mitra = [mitraId]
    form.pks_submission_id = '' // Reset PKS when mitra changes
    form.mou_id = '' // Reset MOU when mitra changes
  }
}

// Remove mitra from selection
const removeMitra = (index: number) => {
  form.invited_mitra = []
  form.pks_submission_id = ''
}

// Format date for datetime-local input
const formatDateTimeForInput = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${year}-${month}-${day}T${hours}:${minutes}`
}

// Populate form when rapat prop changes
watch(() => props.rapat, (newRapat) => {
  if (props.show && newRapat) {
    // Populate form fields when rapat data changes and modal is open
    form.judul = newRapat.judul || ''
    form.tanggal_waktu = formatDateTimeForInput(newRapat.tanggal_waktu) || ''
    form.lokasi = newRapat.lokasi || ''
    form.deskripsi = newRapat.deskripsi || ''
    form.status = newRapat.status || 'akan_datang'
    form.status = newRapat.status || 'akan_datang'
    form.pks_submission_id = newRapat.pks_submission_id || ''
    form.mou_id = newRapat.mou_id || ''
    existingDocumentUrl.value = newRapat.pks_document_url || null
    
    // Determine meeting type
    isMouMeeting.value = !!newRapat.mou_id
    
    // Populate invited mitra
    if (newRapat.invited_mitra) {
      form.invited_mitra = newRapat.invited_mitra.map(mitra => mitra.id)
    }
  }
}, { immediate: true })

// Handle file change
const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    if (validateFile(file)) {
      form.pks_document = file
    }
  }
}

// Validate file
const validateFile = (file: File) => {
  const maxSize = 2 * 1024 * 1024 // 2MB in bytes
  const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  
  if (file.size > maxSize) {
    alert('Ukuran file tidak boleh lebih dari 2MB')
    return false
  }
  
  if (!allowedTypes.includes(file.type)) {
    alert('Format file tidak didukung. Hanya PDF, DOC, dan DOCX yang diizinkan.')
    return false
  }
  
  return true
}

// Remove file
const removeFile = (field: 'pks_document') => {
  if (field === 'pks_document') {
    form.pks_document = null
    if (fileInput.value) fileInput.value.value = ''
  }
}

// Format file size
const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Get file icon based on MIME type
const getFileIcon = (mimeType: string) => {
  if (mimeType.includes('pdf')) {
    return '📄'
  } else if (mimeType.includes('word')) {
    return '📝'
  } else if (mimeType.includes('excel')) {
    return '📊'
  } else if (mimeType.includes('image')) {
    return '🖼️'
  } else {
    return '📁'
  }
}

// Get file name from URL
const getFileNameFromUrl = (url: string) => {
  if (!url) return ''
  const parts = url.split('/')
  return parts[parts.length - 1]
}

// Submit form
const submit = () => {
  if (!props.rapat) return
  
  form.post(route('rapat.update', props.rapat.id), {
    onSuccess: () => {
      emit('updated')
      closeModal()
    },
    onError: () => {
      console.log('Error updating rapat')
    }
  })
}

// Close modal
const closeModal = () => {
  // Reset form
  form.reset()
  form.clearErrors()
  
  // Reset file inputs
  if (fileInput.value) fileInput.value.value = ''
  
  // Reset document URLs
  existingDocumentUrl.value = null
  
  // Reset search
  searchMitra.value = ''
  
  // Emit close event
  emit('close')
}
</script>