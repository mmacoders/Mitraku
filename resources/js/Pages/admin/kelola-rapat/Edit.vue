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
                  class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800"
                >
                  Lihat
                </a>
                <button
                  type="button"
                  @click="removeExistingFile"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>

          <!-- Upload Dokumen PKS Baru -->
          <div>
            <InputLabel for="pks_document" value="Upload Dokumen PKS (Opsional)" />
            <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md">
              <div class="space-y-1 text-center">
                <div v-if="!form.pks_document" class="text-gray-600 dark:text-gray-400">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="flex text-sm text-gray-600 dark:text-gray-400">
                    <label for="pks_document" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                      <span>Unggah file</span>
                      <input 
                        id="pks_document" 
                        ref="fileInput"
                        type="file" 
                        class="sr-only" 
                        @change="handleFileChange"
                        accept=".pdf,.doc,.docx"
                        :disabled="form.processing"
                      />
                    </label>
                    <p class="pl-1">atau drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    PDF, DOC, DOCX hingga 2MB
                  </p>
                </div>
                <div v-else class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
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
                    @click="removeNewFile"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
            <InputError class="mt-2" :message="form.errors.pks_document" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Unggah dokumen PKS terkait dengan rapat ini (opsional)
            </p>
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
              <span v-if="form.processing">Memperbarui...</span>
              <span v-else>Perbarui Rapat</span>
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
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
  rapat: {
    id: number
    judul: string
    tanggal_waktu: string
    lokasi: string
    deskripsi: string
    status: string
    pks_document_url?: string
    pks_submission_id?: number | null
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

const emit = defineEmits(['updated'])

const fileInput = ref<HTMLInputElement | null>(null)
const existingDocumentUrl = ref<string | null>(null)
const searchMitra = ref('')

// Form setup
const form = useForm({
  judul: '',
  tanggal_waktu: '',
  lokasi: '',
  deskripsi: '',
  status: 'akan_datang',
  pks_document: null as File | null,
  shouldRemoveExistingDocument: false,
  invited_mitra: [] as number[],
  pks_submission_id: '' as string | number
})

// Filtered mitra based on search
const filteredMitra = computed(() => {
  if (!searchMitra.value || !props.availableMitra) return []
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
  if (!props.availableMitra) return ''
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

// Format datetime from DB ("YYYY-MM-DD HH:MM:SS") to HTML5 datetime-local ("YYYY-MM-DDTHH:MM")
const formatDateTimeForInput = (dateTimeString: string): string => {
  if (!dateTimeString) return ''
  try {
    // Handle both formats: "YYYY-MM-DD HH:MM:SS" and "YYYY-MM-DDTHH:MM"
    let date: Date
    if (dateTimeString.includes(' ')) {
      // Database format: "YYYY-MM-DD HH:MM:SS"
      const [datePart, timePart] = dateTimeString.split(' ')
      const [year, month, day] = datePart.split('-')
      const [hour, minute] = timePart.split(':')
      date = new Date(
        parseInt(year),
        parseInt(month) - 1, // Month is 0-indexed in JS
        parseInt(day),
        parseInt(hour),
        parseInt(minute)
      )
    } else if (dateTimeString.includes('T')) {
      // Already in datetime-local format: "YYYY-MM-DDTHH:MM"
      const [datePart, timePart] = dateTimeString.split('T')
      const [year, month, day] = datePart.split('-')
      const [hour, minute] = timePart.split(':')
      date = new Date(
        parseInt(year),
        parseInt(month) - 1, // Month is 0-indexed in JS
        parseInt(day),
        parseInt(hour),
        parseInt(minute)
      )
    } else {
      // Try parsing as a standard date string
      date = new Date(dateTimeString)
    }
    
    // Check if date is valid
    if (isNaN(date.getTime())) return ''
    
    // Format for datetime-local input (YYYY-MM-DDTHH:MM)
    const y = date.getFullYear()
    const m = String(date.getMonth() + 1).padStart(2, '0')
    const d = String(date.getDate()).padStart(2, '0')
    const h = String(date.getHours()).padStart(2, '0')
    const min = String(date.getMinutes()).padStart(2, '0')
    
    return `${y}-${m}-${d}T${h}:${min}`
  } catch (error) {
    console.error('Error formatting datetime:', error)
    return ''
  }
}

// Convert datetime-local format back to database format for submission
const formatDateTimeForSubmit = (dateTimeString: string): string => {
  if (!dateTimeString) return ''
  try {
    // datetime-local format: "YYYY-MM-DDTHH:MM"
    const [datePart, timePart] = dateTimeString.split('T')
    // Return database format: "YYYY-MM-DD HH:MM:SS"
    return `${datePart} ${timePart}:00`
  } catch (error) {
    console.error('Error formatting datetime for submission:', error)
    return ''
  }
}

// Validate datetime format before submission
const validateDateTimeFormat = (dateTimeString: string): boolean => {
  // Regex for datetime-local format: YYYY-MM-DDTHH:MM
  const dateTimeRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/
  return dateTimeRegex.test(dateTimeString)
}

// Validate form fields
const validateForm = (): boolean => {
  // Check if required fields are filled
  if (!form.judul?.trim()) {
    form.setError('judul', 'Judul rapat harus diisi.')
    return false
  }
  
  if (!form.tanggal_waktu) {
    form.setError('tanggal_waktu', 'Tanggal dan waktu rapat harus diisi.')
    return false
  }
  
  if (!validateDateTimeFormat(form.tanggal_waktu)) {
    form.setError('tanggal_waktu', 'Format tanggal dan waktu tidak valid.')
    return false
  }
  
  if (!form.lokasi?.trim()) {
    form.setError('lokasi', 'Lokasi atau link meeting harus diisi.')
    return false
  }
  
  if (!form.status) {
    form.setError('status', 'Status rapat harus dipilih.')
    return false
  }
  
  // Validate PKS submission if mitra is selected
  if (form.invited_mitra.length > 0 && form.pks_submission_id === '') {
    form.setError('pks_submission_id', 'Silakan pilih PKS yang akan dibahas.')
    return false
  }
  
  // If mitra is selected, make sure we have PKS submissions data
  if (form.invited_mitra.length > 0 && (!props.pksSubmissions || props.pksSubmissions.length === 0)) {
    form.setError('pks_submission_id', 'Data PKS tidak tersedia.')
    return false
  }
  
  return true
}

// Watch for changes in the show prop to populate form when modal opens
watch(() => props.show, (newShow) => {
  if (newShow && props.rapat) {
    // Populate form fields when modal is opened and rapat data is available
    form.judul = props.rapat.judul || ''
    form.tanggal_waktu = formatDateTimeForInput(props.rapat.tanggal_waktu) || ''
    form.lokasi = props.rapat.lokasi || ''
    form.deskripsi = props.rapat.deskripsi || ''
    form.status = props.rapat.status || 'akan_datang'
    form.pks_submission_id = props.rapat.pks_submission_id || ''
    existingDocumentUrl.value = props.rapat.pks_document_url || null
    
    // Populate invited mitra
    if (props.rapat.invited_mitra) {
      form.invited_mitra = props.rapat.invited_mitra.map(mitra => mitra.id)
    }
  }
}, { immediate: true })

// Watch for changes in the rapat prop
watch(() => props.rapat, (newRapat) => {
  if (props.show && newRapat) {
    // Populate form fields when rapat data changes and modal is open
    form.judul = newRapat.judul || ''
    form.tanggal_waktu = formatDateTimeForInput(newRapat.tanggal_waktu) || ''
    form.lokasi = newRapat.lokasi || ''
    form.deskripsi = newRapat.deskripsi || ''
    form.status = newRapat.status || 'akan_datang'
    form.pks_submission_id = newRapat.pks_submission_id || ''
    existingDocumentUrl.value = newRapat.pks_document_url || null
    
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
const validateFile = (file: File): boolean => {
  const validTypes = ['application/pdf', 'application/msword', 
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  const maxSize = 2 * 1024 * 1024 // 2MB
  
  if (!validTypes.includes(file.type)) {
    form.setError('pks_document', 'Format file tidak didukung. Harap unggah file PDF, DOC, atau DOCX.')
    return false
  }
  
  if (file.size > maxSize) {
    form.setError('pks_document', 'Ukuran file terlalu besar. Maksimal 2MB.')
    return false
  }
  
  // Clear any previous file errors
  form.clearErrors('pks_document')
  return true
}

// Remove existing file
const removeExistingFile = () => {
  existingDocumentUrl.value = null
  // Mark that the existing file should be removed
  form.shouldRemoveExistingDocument = true
}

// Remove new file
const removeNewFile = () => {
  form.pks_document = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
  // Clear file errors
  form.clearErrors('pks_document')
}

// Get file icon
const getFileIcon = (type: string) => {
  if (type.includes('pdf')) return '📄'
  if (type.includes('word') || type.includes('doc')) return '📝'
  return '📁'
}

// Format file size
const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Get file name from URL
const getFileNameFromUrl = (url: string | null) => {
  if (!url) return 'dokumen_pks.pdf'
  try {
    const urlObj = new URL(url)
    const pathParts = urlObj.pathname.split('/')
    return pathParts[pathParts.length - 1] || 'dokumen_pks.pdf'
  } catch {
    return 'dokumen_pks.pdf'
  }
}

// Close modal
const closeModal = () => {
  props.onClose()
  form.reset()
  form.clearErrors()
  existingDocumentUrl.value = null
  searchMitra.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Submit form
const submit = () => {
  if (props.rapat) {
    // Clear previous errors
    form.clearErrors()
    
    // Validate form
    if (!validateForm()) {
      // Scroll to the first error
      setTimeout(() => {
        const firstError = document.querySelector('.text-red-500, .text-red-400')
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
      }, 100)
      return
    }
    
    // Transform the data before submission to ensure proper formatting
    form.transform((data) => ({
      ...data,
      judul: data.judul?.trim() || '',
      tanggal_waktu: formatDateTimeForSubmit(data.tanggal_waktu) || '',
      lokasi: data.lokasi?.trim() || '',
      status: data.status || '',
      deskripsi: data.deskripsi?.trim() || ''
    })).put(route('rapat.update', props.rapat.id), {
      onSuccess: () => {
        closeModal()
        emit('updated')
      },
      onError: (errors) => {
        // Errors are automatically handled by Inertia
        console.error('Form submission error:', errors)
        // Show a user-friendly error message
        alert('Terjadi kesalahan saat memperbarui rapat. Silakan periksa kembali data yang dimasukkan.')
      }
    })
  } else {
    console.error('Rapat data is not available')
    alert('Terjadi kesalahan. Data rapat tidak tersedia.')
  }
}
</script>