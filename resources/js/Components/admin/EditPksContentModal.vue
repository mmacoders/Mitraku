<template>
  <TransitionRoot as="template" :show="isOpen">
    <Dialog as="div" class="relative z-50" @close="closeModal">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl transition-all sm:w-full sm:max-w-lg">
              <div class="px-6 py-6 sm:px-8 sm:py-8">
                <DialogTitle as="h3" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                  Edit Konten Pengajuan PKS
                </DialogTitle>
                
                <form @submit.prevent="updateContent" class="space-y-6 text-left">
                  <!-- Title -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                       Judul PKS *
                    </label>
                    <input
                      v-model="formData.title"
                      type="text"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3"
                      required
                      :disabled="isProcessing"
                    />
                  </div>

                  <!-- Description -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                       Deskripsi *
                    </label>
                    <textarea
                      v-model="formData.purpose"
                      rows="4"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3"
                      required
                      :disabled="isProcessing"
                    ></textarea>
                  </div>

                  <!-- KAK Document -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                       Update Dokumen KAK
                    </label>
                    <input
                      type="file"
                      @change="(e) => handleFileChange(e, 'kak_document')"
                      class="block w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        dark:file:bg-blue-900/30 dark:file:text-blue-300
                        dark:hover:file:bg-blue-800/50
                        rounded-lg border border-gray-300 dark:border-gray-600
                        shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                        dark:bg-gray-700"
                       :disabled="isProcessing"
                       accept=".pdf,.doc,.docx"
                    />
                     <p v-if="submission.kak_document_path" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                      File saat ini: <a :href="`/storage/${submission.kak_document_path}`" target="_blank" class="text-blue-500 hover:underline">Lihat Dokumen</a>
                    </p>
                  </div>

                  <!-- MoU Document -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                       Update Dokumen MoU
                    </label>
                    <input
                      type="file"
                      @change="(e) => handleFileChange(e, 'mou_document')"
                      class="block w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        dark:file:bg-blue-900/30 dark:file:text-blue-300
                        dark:hover:file:bg-blue-800/50
                        rounded-lg border border-gray-300 dark:border-gray-600
                        shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                        dark:bg-gray-700"
                        :disabled="isProcessing"
                        accept=".pdf,.doc,.docx"
                    />
                    <p v-if="submission.mou_document_path" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                      File saat ini: <a :href="`/storage/${submission.mou_document_path}`" target="_blank" class="text-blue-500 hover:underline">Lihat Dokumen</a>
                    </p>
                  </div>

                  <div class="mt-8 flex gap-3">
                    <button
                      type="button"
                      class="flex-1 rounded-lg bg-gray-200 dark:bg-gray-600 px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-200"
                      @click="closeModal"
                      :disabled="isProcessing"
                    >
                      Batal
                    </button>
                    <button
                      type="submit"
                      class="flex-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-3 text-sm font-medium text-white shadow-md hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 flex items-center justify-center"
                      :disabled="isProcessing"
                    >
                      <svg
                        v-if="isProcessing"
                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                      >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ isProcessing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                  </div>
                </form>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot
} from '@headlessui/vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  submission: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'success'])

// Form data
const formData = ref({
  title: '',
  purpose: '', // Maps to description in controller
  kak_document: null,
  mou_document: null
})

// Processing state
const isProcessing = ref(false)

// Watch for show prop changes to init form
watch(() => props.show, (newValue) => {
  if (newValue && props.submission) {
    formData.value.title = props.submission.title
    formData.value.purpose = props.submission.description || props.submission.purpose
    formData.value.kak_document = null
    formData.value.mou_document = null
    isProcessing.value = false
  }
})

// Handle file change
const handleFileChange = (e, field) => {
  if (e.target.files && e.target.files[0]) {
    formData.value[field] = e.target.files[0]
  } else {
    formData.value[field] = null
  }
}

// Modal functions
const closeModal = () => {
  emit('close')
}

const updateContent = () => {
  isProcessing.value = true
  
  // Prepare data for submission
  // Use _method: PUT for file uploads via Inertia/POST
  const data = new FormData()
  data.append('_method', 'PUT')
  data.append('title', formData.value.title)
  data.append('purpose', formData.value.purpose)
  
  if (formData.value.kak_document) {
    data.append('kak_document', formData.value.kak_document)
  }
  
  if (formData.value.mou_document) {
    data.append('mou_document', formData.value.mou_document)
  }

  // Use router.post because sending files requires FormData which usually uses POST method spoofing
  router.post(route('pks.update', props.submission.id), data, {
    forceFormData: true,
    onSuccess: (response) => {
      isProcessing.value = false
      emit('success')
      closeModal()
      alert('Konten pengajuan berhasil diperbarui!')
    },
    onError: (errors) => {
      isProcessing.value = false
      console.error('Error updating content:', errors)
      alert('Terjadi kesalahan saat memperbarui konten. Silakan periksa kembali input Anda.')
    }
  })
}

// Computed property for modal visibility
const isOpen = computed(() => props.show)
</script>
