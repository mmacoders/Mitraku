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
                Isi detail rapat dan undang mitra
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
            
            <!-- Selected Mitra Tags -->
            <div v-if="selectedMitra.length > 0" class="mt-3 flex flex-wrap gap-2">
              <span
                v-for="id in selectedMitra"
                :key="id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
              >
                {{ mitra.find(m => m.id === id)?.name }}
                <button
                  type="button"
                  @click.stop="deselectMitra(id)"
                  class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white"
                >
                  <span class="sr-only">Remove</span>
                  <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                  </svg>
                </button>
              </span>
            </div>
            
            <InputError class="mt-2" :message="form.errors.mitra" />
          </div>

          <!-- Upload Dokumen PKS -->
          <div>
            <InputLabel for="pks_document" value="Upload Dokumen PKS (Opsional)" />
            <input
              id="pks_document"
              ref="fileInput"
              type="file"
              class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100
                dark:file:bg-indigo-900 dark:file:text-indigo-200
                dark:hover:file:bg-indigo-800"
              @change="handleFileChange"
              accept=".pdf,.doc,.docx"
            />
            <!-- File preview -->
            <div v-if="form.pks_document" class="mt-2 flex items-center justify-between p-2 bg-gray-50 rounded-lg dark:bg-gray-700">
              <div class="flex items-center">
                <span class="text-lg mr-2">
                  {{ getFileIcon(form.pks_document.type) }}
                </span>
                <div class="text-xs">
                  <p class="font-medium text-gray-900 dark:text-white truncate max-w-[120px]">
                    {{ form.pks_document.name }}
                  </p>
                  <p class="text-gray-500 dark:text-gray-400">
                    {{ formatFileSize(form.pks_document.size) }}
                  </p>
                </div>
              </div>
              <button 
                type="button"
                @click="removeFile"
                class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400"
                title="Hapus file"
              >
                ❌
              </button>
            </div>
            <InputError class="mt-2" :message="form.errors.pks_document" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Format yang diterima: PDF, DOC, DOCX. Maksimal 2MB.
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
import { ref, onMounted } from 'vue'
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
  mitra: Array<{
    id: number
    name: string
    email: string
  }>
}>()

const emit = defineEmits(['created'])

const fileInput = ref<HTMLInputElement | null>(null)
const selectedMitra = ref<number[]>([])

// Initialize selected mitra from props if editing
onMounted(() => {
  // This would be used if we're editing an existing rapat
  // For now, it's just for demonstration
})

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

// Form setup
const form = useForm({
  judul: '',
  tanggal_waktu: '',
  lokasi: '',
  deskripsi: '',
  pks_document: null as File | null,
  mitra: [] as number[]
})

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
    alert('Format file tidak didukung. Harap unggah file PDF, DOC, atau DOCX.')
    return false
  }
  
  if (file.size > maxSize) {
    alert('Ukuran file terlalu besar. Maksimal 2MB.')
    return false
  }
  
  return true
}

// Remove file
const removeFile = () => {
  form.pks_document = null
  if (fileInput.value) {
    fileInput.value.value = ''
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
  props.onClose()
  form.reset()
  form.clearErrors()
  selectedMitra.value = []
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Submit form
const submit = () => {
  // Set the selected mitra to the form
  form.mitra = selectedMitra.value
  
  // Validate that at least one mitra is selected
  if (form.mitra.length === 0) {
    form.setError('mitra', 'Harap pilih minimal satu mitra untuk diundang.')
    return
  }
  
  // We need to use forceFormData to properly handle file uploads
  form.post(route('rapat.store'), {
    forceFormData: true,
    onSuccess: () => {
      closeModal()
      emit('created')
    }
  })
}
</script>