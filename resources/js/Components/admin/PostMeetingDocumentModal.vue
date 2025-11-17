<template>
  <Modal :show="show" @close="closeModal" max-width="md">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
          {{ modalTitle }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          {{ modalDescription }}
        </p>
      </div>
      
      <div class="px-6 py-4">
        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Draft Document Upload -->
          <div v-if="processType === 'draft_fix'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Draft Dokumen
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md">
              <div class="space-y-1 text-center">
                <div v-if="!draftFile" class="flex text-sm text-gray-600 dark:text-gray-400">
                  <label class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Upload file</span>
                    <input 
                      ref="draftFileInput"
                      type="file" 
                      class="sr-only" 
                      accept=".pdf,.doc,.docx"
                      @change="handleDraftFileChange"
                    />
                  </label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <div v-else class="flex items-center justify-between">
                  <span class="text-sm text-gray-900 dark:text-white truncate">
                    {{ draftFile.name }}
                  </span>
                  <button 
                    type="button" 
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    @click="removeDraftFile"
                  >
                    <X class="h-5 w-5" />
                  </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  PDF, DOC, DOCX hingga 2MB
                </p>
              </div>
            </div>
            <div v-if="errors.draft_document" class="mt-2 text-sm text-red-600 dark:text-red-400">
              {{ errors.draft_document }}
            </div>
          </div>
          
          <!-- Signing Schedule -->
          <div v-else-if="processType === 'signing_schedule'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Jadwal Penandatanganan
            </label>
            <input
              v-model="signingSchedule"
              type="datetime-local"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              :disabled="submitting"
            />
            <div v-if="errors.signing_schedule" class="mt-2 text-sm text-red-600 dark:text-red-400">
              {{ errors.signing_schedule }}
            </div>
          </div>
          
          <!-- Signed Document Upload -->
          <div v-else-if="processType === 'signed_document'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Dokumen yang Ditandatangani
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md">
              <div class="space-y-1 text-center">
                <div v-if="!signedFile" class="flex text-sm text-gray-600 dark:text-gray-400">
                  <label class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Upload file</span>
                    <input 
                      ref="signedFileInput"
                      type="file" 
                      class="sr-only" 
                      accept=".pdf,.doc,.docx"
                      @change="handleSignedFileChange"
                    />
                  </label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <div v-else class="flex items-center justify-between">
                  <span class="text-sm text-gray-900 dark:text-white truncate">
                    {{ signedFile.name }}
                  </span>
                  <button 
                    type="button" 
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    @click="removeSignedFile"
                  >
                    <X class="h-5 w-5" />
                  </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  PDF, DOC, DOCX hingga 2MB
                </p>
              </div>
            </div>
            <div v-if="errors.signed_document" class="mt-2 text-sm text-red-600 dark:text-red-400">
              {{ errors.signed_document }}
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
              @click="closeModal"
              :disabled="submitting"
            >
              Batal
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
              :disabled="submitting"
              :class="{ 'opacity-25': submitting }"
            >
              <span v-if="submitting">Memproses...</span>
              <span v-else>Simpan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
  show: Boolean,
  processType: String, // 'draft_fix', 'signing_schedule', 'signed_document'
  rapat: Object
});

const emit = defineEmits(['close', 'success']);

// Form data
const draftFile = ref(null);
const draftFileInput = ref(null);
const signingSchedule = ref('');
const signedFile = ref(null);
const signedFileInput = ref(null);

// Form state
const submitting = ref(false);
const errors = ref({});

// Computed properties for modal content
const modalTitle = computed(() => {
  switch (props.processType) {
    case 'draft_fix':
      return 'Upload Draft Fix';
    case 'signing_schedule':
      return 'Atur Jadwal Penandatanganan';
    case 'signed_document':
      return 'Upload Dokumen yang Ditandatangani';
    default:
      return 'Proses Dokumen';
  }
});

const modalDescription = computed(() => {
  switch (props.processType) {
    case 'draft_fix':
      return 'Upload draft dokumen hasil rapat untuk dilihat oleh mitra.';
    case 'signing_schedule':
      return 'Atur jadwal penandatanganan dokumen.';
    case 'signed_document':
      return 'Upload dokumen yang telah ditandatangani.';
    default:
      return 'Proses dokumen pasca rapat.';
  }
});

// File handling methods
const handleDraftFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    draftFile.value = file;
  }
};

const removeDraftFile = () => {
  draftFile.value = null;
  if (draftFileInput.value) {
    draftFileInput.value.value = '';
  }
};

const handleSignedFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    signedFile.value = file;
  }
};

const removeSignedFile = () => {
  signedFile.value = null;
  if (signedFileInput.value) {
    signedFileInput.value.value = '';
  }
};

// Form submission
const submitForm = () => {
  errors.value = {};
  submitting.value = true;
  
  const formData = new FormData();
  
  switch (props.processType) {
    case 'draft_fix':
      if (!draftFile.value) {
        errors.value.draft_document = 'File draft dokumen harus diunggah.';
        submitting.value = false;
        return;
      }
      formData.append('draft_document', draftFile.value);
      router.post(route('rapat.uploadDraftDocument', props.rapat.id), formData, {
        onSuccess: () => {
          submitting.value = false;
          emit('success');
          closeModal();
        },
        onError: (error) => {
          errors.value = error;
          submitting.value = false;
        }
      });
      break;
      
    case 'signing_schedule':
      if (!signingSchedule.value) {
        errors.value.signing_schedule = 'Jadwal penandatanganan harus diisi.';
        submitting.value = false;
        return;
      }
      formData.append('signing_schedule', signingSchedule.value);
      router.post(route('rapat.setSigningSchedule', props.rapat.id), formData, {
        onSuccess: () => {
          submitting.value = false;
          emit('success');
          closeModal();
        },
        onError: (error) => {
          errors.value = error;
          submitting.value = false;
        }
      });
      break;
      
    case 'signed_document':
      if (!signedFile.value) {
        errors.value.signed_document = 'File dokumen yang ditandatangani harus diunggah.';
        submitting.value = false;
        return;
      }
      formData.append('signed_document', signedFile.value);
      router.post(route('rapat.uploadSignedDocument', props.rapat.id), formData, {
        onSuccess: () => {
          submitting.value = false;
          emit('success');
          closeModal();
        },
        onError: (error) => {
          errors.value = error;
          submitting.value = false;
        }
      });
      break;
  }
};

// Close modal
const closeModal = () => {
  // Reset form
  draftFile.value = null;
  signingSchedule.value = '';
  signedFile.value = null;
  
  if (draftFileInput.value) {
    draftFileInput.value.value = '';
  }
  if (signedFileInput.value) {
    signedFileInput.value.value = '';
  }
  
  errors.value = {};
  submitting.value = false;
  
  emit('close');
};
</script>