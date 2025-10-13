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
            <DialogPanel class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl transition-all sm:w-full sm:max-w-md">
              <div class="px-6 py-6 sm:px-8 sm:py-8">
                <DialogTitle as="h3" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                  Edit Status Pengajuan PKS
                </DialogTitle>
                
                <div class="space-y-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Status
                    </label>
                    <select
                      v-model="formData.status"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3"
                      :disabled="isProcessing"
                    >
                      <option value="">Pilih Status</option>
                      <option value="disetujui">Disetujui</option>
                      <option value="ditolak">Ditolak</option>
                      <option value="revisi">Revisi</option>
                    </select>
                  </div>
                  
                  <div v-if="formData.status === 'ditolak' || formData.status === 'revisi'" class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Catatan Revisi
                    </label>
                    <textarea
                      v-model="formData.notes"
                      rows="3"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3"
                      placeholder="Tambahkan catatan revisi jika diperlukan..."
                      :disabled="isProcessing"
                    ></textarea>
                  </div>
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
                    type="button"
                    class="flex-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-3 text-sm font-medium text-white shadow-md hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 flex items-center justify-center"
                    @click="updateStatus"
                    :disabled="isProcessing || !isFormValid"
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
                    {{ isProcessing ? 'Menyimpan...' : 'Update Status' }}
                  </button>
                </div>
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
  status: '',
  notes: ''
})

// Processing state
const isProcessing = ref(false)

// Validation
const isFormValid = computed(() => {
  return formData.value.status !== ''
})

// Watch for show prop changes
watch(() => props.show, (newValue) => {
  if (newValue) {
    // Reset form when modal opens
    formData.value.status = ''
    formData.value.notes = ''
    isProcessing.value = false
  }
})

// Modal functions
const closeModal = () => {
  emit('close')
}

const updateStatus = () => {
  if (!isFormValid.value) return
  
  isProcessing.value = true
  
  // Actual implementation to update status
  router.put(route('pks.updateStatus', props.submission.id), {
    status: formData.value.status,
    revision_notes: formData.value.notes
  }, {
    onSuccess: (response) => {
      isProcessing.value = false
      emit('success')
      closeModal()
      // Show notification
      alert('Status pengajuan berhasil diperbarui!')
    },
    onError: (errors) => {
      isProcessing.value = false
      // Handle error
      console.error('Error updating status:', errors)
      alert('Terjadi kesalahan saat memperbarui status.')
    }
  })
}

// Computed property for modal visibility
const isOpen = computed(() => props.show)
</script>