<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { 
  Dialog, 
  DialogPanel, 
  DialogTitle, 
  TransitionChild, 
  TransitionRoot 
} from '@headlessui/vue'

interface Props {
  isOpen: boolean
}

interface Emits {
  (e: 'close'): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// File input references
const kakFileInput = ref<HTMLInputElement | null>(null)
const mouFileInput = ref<HTMLInputElement | null>(null)

// Drag state
const isDraggingKak = ref(false)
const isDraggingMou = ref(false)

// Confirmation dialog state
const showConfirmation = ref(false)

// Toast notification state
const showToast = ref(false)
const toastMessage = ref('')

// Form setup
const form = useForm({
  title: '',
  purpose: '',
  kak_document: null as File | null,
  mou_document: null as File | null
})

// Handle drag events for KAK
const handleDragOverKak = () => {
  isDraggingKak.value = true
}

const handleDragLeaveKak = () => {
  isDraggingKak.value = false
}

const handleDropKak = (e: DragEvent) => {
  isDraggingKak.value = false
  if (e.dataTransfer?.files?.length) {
    const file = e.dataTransfer.files[0]
    if (validateFile(file)) {
      form.kak_document = file
    }
  }
}

// Handle drag events for MoU
const handleDragOverMou = () => {
  isDraggingMou.value = true
}

const handleDragLeaveMou = () => {
  isDraggingMou.value = false
}

const handleDropMou = (e: DragEvent) => {
  isDraggingMou.value = false
  if (e.dataTransfer?.files?.length) {
    const file = e.dataTransfer.files[0]
    if (validateFile(file)) {
      form.mou_document = file
    }
  }
}

// Trigger file input
const triggerFileInput = (type: 'kak' | 'mou') => {
  if (type === 'kak' && kakFileInput.value) {
    kakFileInput.value.click()
  } else if (type === 'mou' && mouFileInput.value) {
    mouFileInput.value.click()
  }
}

// Handle file change
const handleFileChange = (e: Event, type: 'kak' | 'mou') => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    if (validateFile(file)) {
      if (type === 'kak') {
        form.kak_document = file
      } else {
        form.mou_document = file
      }
    }
  }
}

// Validate file
const validateFile = (file: File): boolean => {
  const validTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  const maxSize = 2 * 1024 * 1024 // 2MB
  
  if (!validTypes.includes(file.type)) {
    form.setError('kak_document', 'Format file tidak didukung. Harap unggah file PDF atau DOCX.')
    form.setError('mou_document', 'Format file tidak didukung. Harap unggah file PDF atau DOCX.')
    return false
  }
  
  if (file.size > maxSize) {
    form.setError('kak_document', 'Ukuran file terlalu besar. Maksimal 2MB.')
    form.setError('mou_document', 'Ukuran file terlalu besar. Maksimal 2MB.')
    return false
  }
  
  return true
}

// Remove file
const removeFile = (type: 'kak' | 'mou') => {
  if (type === 'kak') {
    form.kak_document = null
    if (kakFileInput.value) {
      kakFileInput.value.value = ''
    }
  } else {
    form.mou_document = null
    if (mouFileInput.value) {
      mouFileInput.value.value = ''
    }
  }
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

// Close modal
const closeModal = () => {
  emit('close')
  form.reset()
  form.clearErrors()
  if (kakFileInput.value) kakFileInput.value.value = ''
  if (mouFileInput.value) mouFileInput.value.value = ''
  showConfirmation.value = false
}

// Submit form after confirmation
const confirmSubmit = () => {
  showConfirmation.value = false
  form.post(route('pks.store'), {
    forceFormData: true,
    onSuccess: () => {
      // Show success toast
      toastMessage.value = 'Pengajuan berhasil dikirim ke admin untuk diverifikasi'
      showToast.value = true
      
      // Hide toast after 3 seconds
      setTimeout(() => {
        showToast.value = false
      }, 3000)
      
      closeModal()
      emit('success')
    },
    onError: (errors) => {
      // Handle validation errors from server
      Object.keys(errors).forEach(key => {
        // Type-safe error setting
        if (key === 'title' || key === 'purpose' || key === 'kak_document' || key === 'mou_document') {
          form.setError(key, errors[key])
        }
      })
      
      // Show error in confirmation modal
      toastMessage.value = 'Terjadi kesalahan validasi. Silakan periksa kembali form Anda.'
      showConfirmation.value = true
    }
  })
}

// Cancel submission
const cancelSubmit = () => {
  showConfirmation.value = false
}

// Submit form
const submitForm = () => {
  // Clear previous errors
  form.clearErrors()
  
  // Validate all required fields
  let hasErrors = false
  
  if (!form.title?.trim()) {
    form.setError('title', 'Judul harus diisi.')
    hasErrors = true
  }
  
  if (!form.purpose?.trim()) {
    form.setError('purpose', 'Tujuan harus diisi.')
    hasErrors = true
  }
  
  if (!form.kak_document) {
    form.setError('kak_document', 'Dokumen KAK harus diunggah.')
    hasErrors = true
  }
  
  if (!form.mou_document) {
    form.setError('mou_document', 'Dokumen MoU harus diunggah.')
    hasErrors = true
  }
  
  // Show confirmation dialog (it will display errors if any, or confirmation if none)
  showConfirmation.value = true
}
</script>

<template>
  <TransitionRoot as="template" :show="isOpen">
    <Dialog as="div" class="relative z-50" @close="closeModal">
      <!-- Overlay -->
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
          <!-- Modal -->
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-900/90 shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl p-8 sm:p-10">
              <!-- Header -->
               <div class="flex items-center justify-between">
                  <div class="flex flex-col justify-center">
                <div>
                  <DialogTitle as="h4" class="text-2xl text-left font-semibold text-gray-800 dark:text-white">
                    Ajukan PKS Baru
                  </DialogTitle>
                  <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Isi form berikut untuk mengajukan kerjasama.
                  </p>
                </div>
                </div>
                <button
                  type="button"
                  class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 transition-transform hover:scale-110"
                  @click="closeModal"
                >
                  <span class="sr-only">Close</span>
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Main Content - Either Form or Confirmation Dialog -->
              <div v-if="showConfirmation" class="mt-8">
                <!-- Confirmation Dialog -->
                <div class="text-center">
                  <!-- Icon - Warning for errors, Info for confirmation -->
                  <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4"
                    :class="{
                      'bg-rose-50 dark:bg-rose-900/20': form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document,
                      'bg-amber-50 dark:bg-amber-900/20': !form.errors.title && !form.errors.purpose && !form.errors.kak_document && !form.errors.mou_document
                    }">
                    <svg v-if="form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document" class="h-8 w-8 text-rose-500 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="h-8 w-8 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  
                  <!-- Title -->
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document ? 'Form Tidak Lengkap' : 'Konfirmasi Pengajuan PKS' }}
                  </h3>
                  
                  <!-- Description -->
                  <div class="mt-2 mb-6">
                    <p v-if="form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document" class="text-gray-600 dark:text-gray-300 leading-relaxed">
                      Mohon lengkapi semua field yang diperlukan sebelum mengirimkan pengajuan.
                    </p>
                    <p v-else class="text-gray-600 dark:text-gray-300 leading-relaxed">
                      Pastikan semua data pengajuan sudah benar sebelum dikirim. Setelah diajukan, data tidak dapat diubah tanpa persetujuan admin.
                    </p>
                  </div>
                  
                  <!-- Error Messages -->
                  <div v-if="form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document" class="bg-rose-50 dark:bg-rose-900/20 rounded-xl p-4 mb-6 text-left">
                    <h4 class="font-medium text-rose-800 dark:text-rose-200 mb-2">Field yang harus dilengkapi:</h4>
                    <ul class="space-y-2 text-sm text-rose-700 dark:text-rose-300">
                      <li v-if="form.errors.title" class="flex items-start">
                        <svg class="h-5 w-5 text-rose-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Judul: {{ form.errors.title }}</span>
                      </li>
                      <li v-if="form.errors.purpose" class="flex items-start">
                        <svg class="h-5 w-5 text-rose-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Tujuan: {{ form.errors.purpose }}</span>
                      </li>
                      <li v-if="form.errors.kak_document" class="flex items-start">
                        <svg class="h-5 w-5 text-rose-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Dokumen KAK: {{ form.errors.kak_document }}</span>
                      </li>
                      <li v-if="form.errors.mou_document" class="flex items-start">
                        <svg class="h-5 w-5 text-rose-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Dokumen MoU: {{ form.errors.mou_document }}</span>
                      </li>
                    </ul>
                  </div>
                  
                  <!-- Form Summary (only shown when no errors) -->
                  <div v-else class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6 text-left">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Ringkasan Pengajuan:</h4>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                      <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Judul: {{ form.title || '-' }}</span>
                      </li>
                      <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Dokumen KAK: {{ form.kak_document ? 'Sudah diunggah' : 'Belum diunggah' }}</span>
                      </li>
                      <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Dokumen MoU: {{ form.mou_document ? 'Sudah diunggah' : 'Belum diunggah' }}</span>
                      </li>
                    </ul>
                  </div>
                  
                  <!-- Action Buttons -->
                  <div class="flex flex-col sm:flex-row gap-3">
                    <button
                      v-if="!(form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document)"
                      type="button"
                      class="w-full inline-flex justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-violet-500 px-4 py-3 text-sm font-medium text-white shadow-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5"
                      @click="confirmSubmit"
                    >
                      Ajukan Sekarang
                    </button>
                    <button
                      type="button"
                      class="w-full inline-flex justify-center rounded-xl"
                      :class="{
                        'bg-rose-500 hover:bg-rose-600 text-white': form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document,
                        'bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 ease-in-out': !(form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document)
                      }"
                      @click="cancelSubmit"
                    >
                      {{ form.errors.title || form.errors.purpose || form.errors.kak_document || form.errors.mou_document ? 'Perbaiki Form' : 'Periksa Kembali' }}
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Form -->
              <form v-else @submit.prevent="submitForm" class="mt-8 space-y-6">
                <!-- Judul Pengajuan PKS -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Judul Pengajuan PKS
                  </label>
                  <div class="mt-1">
                    <input
                      id="title"
                      v-model="form.title"
                      type="text"
                      class="block w-full rounded-lg border-0 bg-gray-50 dark:bg-gray-800 py-3 px-4 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-300 dark:focus:ring-indigo-400 sm:text-sm sm:leading-6 transition-all duration-200 ease-in-out"
                      placeholder="Masukkan judul pengajuan Anda..."
                      :class="{ 'border-rose-300/50 ring-rose-300/50': form.errors.title }"
                    />
                  </div>
                  <p v-if="form.errors.title" class="mt-2 text-sm text-rose-400">
                    {{ form.errors.title }}
                  </p>
                  <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Judul harus diisi.
                  </p>
                </div>

                <!-- Tujuan Pengajuan PKS -->
                <div>
                  <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tujuan Pengajuan PKS
                  </label>
                  <div class="mt-1">
                    <textarea
                      id="purpose"
                      v-model="form.purpose"
                      rows="4"
                      class="block w-full rounded-lg border-0 bg-gray-50 dark:bg-gray-800 py-3 px-4 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-300 dark:focus:ring-indigo-400 sm:text-sm sm:leading-6 transition-all duration-200 ease-in-out"
                      placeholder="Tuliskan tujuan dan manfaat kerja sama ini..."
                      :class="{ 'border-rose-300/50 ring-rose-300/50': form.errors.purpose }"
                    />
                  </div>
                  <p v-if="form.errors.purpose" class="mt-2 text-sm text-rose-400">
                    {{ form.errors.purpose }}
                  </p>
                  <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Berikan deskripsi singkat namun jelas.
                  </p>
                </div>

                <!-- Upload Dokumen Section -->
                <div>
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Dokumen Pendukung
                  </h4>
                  
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Upload Dokumen KAK -->
                    <div class="space-y-2">
                      <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                        Dokumen KAK
                      </label>
                      <div 
                        class="relative h-28 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col items-center justify-center transition-all duration-200 ease-in-out"
                        :class="{
                          'bg-indigo-50/50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700': isDraggingKak,
                          'hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20': !isDraggingKak
                        }"
                        @dragover.prevent="handleDragOverKak"
                        @dragleave.prevent="handleDragLeaveKak"
                        @drop.prevent="handleDropKak"
                        @click="triggerFileInput('kak')"
                      >
                        <input
                          ref="kakFileInput"
                          type="file"
                          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                          accept=".pdf,.docx"
                          @change="handleFileChange($event, 'kak')"
                        />
                        <div class="text-center">
                          <div class="text-2xl">📂</div>
                          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Klik atau seret file
                          </p>
                        </div>
                      </div>
                      
                      <!-- File preview for KAK -->
                      <div v-if="form.kak_document" class="mt-2 flex items-center justify-between p-2 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <div class="flex items-center">
                          <span class="text-lg mr-2">
                            {{ getFileIcon(form.kak_document.type) }}
                          </span>
                          <div class="text-xs">
                            <p class="font-medium text-gray-900 dark:text-white truncate max-w-[100px]">
                              {{ form.kak_document.name }}
                            </p>
                            <p class="text-gray-500 dark:text-gray-400">
                              {{ formatFileSize(form.kak_document.size) }}
                            </p>
                          </div>
                        </div>
                        <button 
                          type="button"
                          @click="removeFile('kak')"
                          class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400"
                          title="Hapus file"
                        >
                          ❌
                        </button>
                      </div>
                      
                      <p v-if="form.errors.kak_document" class="text-sm text-rose-400">
                        {{ form.errors.kak_document }}
                      </p>
                    </div>

                    <!-- Upload Dokumen MoU/PKS -->
                    <div class="space-y-2">
                      <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                        Dokumen MoU/PKS
                      </label>
                      <div 
                        class="relative h-28 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col items-center justify-center transition-all duration-200 ease-in-out"
                        :class="{
                          'bg-indigo-50/50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700': isDraggingMou,
                          'hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20': !isDraggingMou
                        }"
                        @dragover.prevent="handleDragOverMou"
                        @dragleave.prevent="handleDragLeaveMou"
                        @drop.prevent="handleDropMou"
                        @click="triggerFileInput('mou')"
                      >
                        <input
                          ref="mouFileInput"
                          type="file"
                          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                          accept=".pdf,.docx"
                          @change="handleFileChange($event, 'mou')"
                        />
                        <div class="text-center">
                          <div class="text-2xl">📂</div>
                          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Klik atau seret file
                          </p>
                        </div>
                      </div>
                      
                      <!-- File preview for MoU -->
                      <div v-if="form.mou_document" class="mt-2 flex items-center justify-between p-2 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <div class="flex items-center">
                          <span class="text-lg mr-2">
                            {{ getFileIcon(form.mou_document.type) }}
                          </span>
                          <div class="text-xs">
                            <p class="font-medium text-gray-900 dark:text-white truncate max-w-[100px]">
                              {{ form.mou_document.name }}
                            </p>
                            <p class="text-gray-500 dark:text-gray-400">
                              {{ formatFileSize(form.mou_document.size) }}
                            </p>
                          </div>
                        </div>
                        <button 
                          type="button"
                          @click="removeFile('mou')"
                          class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400"
                          title="Hapus file"
                        >
                          ❌
                        </button>
                      </div>
                      
                      <p v-if="form.errors.mou_document" class="text-sm text-rose-400">
                        {{ form.errors.mou_document }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Footer -->
                <div class="flex flex-col sm:flex-row-reverse sm:justify-end gap-3 pt-4">
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-violet-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 ease-in-out disabled:opacity-50"
                  >
                    <svg
                      v-if="form.processing"
                      class="mr-2 h-4 w-4 animate-spin text-white"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                      ></circle>
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Kirim Pengajuan</span>
                  </button>
                  <button
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 ease-in-out disabled:opacity-50"
                    @click="closeModal"
                  >
                    Batal
                  </button>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
  
  <!-- Toast Notification -->
  <div 
    v-if="showToast"
    class="fixed bottom-4 right-4 z-50 flex items-center justify-between rounded-lg bg-green-500 px-4 py-3 text-white shadow-lg transition-all duration-300 ease-in-out transform"
    :class="{
      'translate-y-0 opacity-100': showToast,
      'translate-y-4 opacity-0': !showToast
    }"
  >
    <div class="flex items-center">
      <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <span>{{ toastMessage }}</span>
    </div>
    <button 
      @click="showToast = false"
      class="ml-4 text-white hover:text-gray-200 focus:outline-none"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>