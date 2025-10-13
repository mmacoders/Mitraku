<template>
  <Head title="Kelola Mitra" />
  <AdminLayout>
    <template #header>
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
          Kelola Mitra
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Lihat dan kelola data mitra yang terdaftar dalam sistem E-PKS.
        </p>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                  Daftar Mitra
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                  Kelola semua mitra dalam sistem
                </p>
              </div>
              <!-- Removed "Tambah Mitra" button as per requirements -->
            </div>

            <!-- Filter and Search -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input
                    id="search"
                    v-model="searchQuery"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 ease-in-out"
                    placeholder="Cari berdasarkan nama atau email..."
                    @input="filterMitra"
                  />
                </div>
              </div>
            </div>

            <!-- Loading indicator -->
            <div v-if="loading" class="flex justify-center items-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <!-- Mitra Table -->
            <div v-else>
              <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        No
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Nama Mitra
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Email
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Instansi / Perusahaan
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Jumlah PKS
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Tanggal Bergabung
                      </th>
                      <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="(mitra, index) in filteredMitra" :key="mitra.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ index + 1 }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ mitra.name }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ mitra.email }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ mitra.company || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ mitra.pks_count }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(mitra.created_at) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center gap-2">
                          <button
                            @click="openDetailModal(mitra)"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                          >
                            <Eye class="w-4 h-4" />
                          </button>
                          <button
                            @click="openEditModal(mitra)"
                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                          >
                            <Pencil class="w-4 h-4" />
                          </button>
                          <button
                            @click="openDeleteModal(mitra)"
                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                          >
                            <Trash2 class="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="filteredMitra.length === 0">
                      <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                          <Users class="h-12 w-12 text-gray-400" />
                          <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tidak ada data mitra yang ditemukan</h3>
                          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data mitra yang sesuai dengan pencarian Anda.
                          </p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                  Menampilkan <span class="font-medium">{{ filteredMitra.length }}</span> mitra
                </div>
                <div class="flex space-x-2">
                  <!-- Pagination controls would go here if needed -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Mitra Modal (removed create functionality) -->
    <div v-if="showMitraModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                  Edit Mitra
                </h3>
                <div class="mt-4">
                  <form @submit.prevent="saveMitra">
                    <div class="space-y-4">
                      <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                          Nama Mitra
                        </label>
                        <input
                          id="name"
                          v-model="form.name"
                          type="text"
                          required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                          :class="{ 'border-red-500': form.errors.name }"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                          {{ form.errors.name }}
                        </div>
                      </div>

                      <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                          Email
                        </label>
                        <input
                          id="email"
                          v-model="form.email"
                          type="email"
                          required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                          :class="{ 'border-red-500': form.errors.email }"
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                          {{ form.errors.email }}
                        </div>
                      </div>

                      <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                          Instansi / Perusahaan
                        </label>
                        <input
                          id="company"
                          v-model="form.company"
                          type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        />
                      </div>

                      <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                          Password (Opsional)
                        </label>
                        <input
                          id="password"
                          v-model="form.password"
                          type="password"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                          :class="{ 'border-red-500': form.errors.password }"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                          {{ form.errors.password }}
                        </div>
                      </div>

                      <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                          Konfirmasi Password
                        </label>
                        <input
                          id="password_confirmation"
                          v-model="form.password_confirmation"
                          type="password"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                        />
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="saveMitra"
              :disabled="form.processing"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-base font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200 ease-in-out"
            >
              <span v-if="form.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
              </span>
              <span v-else>Simpan</span>
            </button>
            <button
              type="button"
              @click="closeMitraModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200 ease-in-out"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                  Konfirmasi Hapus
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 dark:text-gray-300">
                    Apakah Anda yakin ingin menghapus mitra ini? Semua data terkait pengajuan akan ikut terhapus.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="deleteMitra"
              :disabled="deleting"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200 ease-in-out"
            >
              <span v-if="deleting" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menghapus...
              </span>
              <span v-else>Hapus</span>
            </button>
            <button
              type="button"
              @click="closeDeleteModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200 ease-in-out"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-4 right-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
          <div v-if="toastType === 'success'" class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div v-else class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium" :class="toastType === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'">
              {{ toastMessage }}
            </p>
          </div>
          <div class="ml-4 flex">
            <button @click="hideToast" class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { Eye, Pencil, Trash2, Users } from 'lucide-vue-next';

// Props
const props = defineProps({
  mitra: Array
});

// Reactive data
const mitraList = ref(props.mitra || []);
const searchQuery = ref('');
const loading = ref(false);
const showMitraModal = ref(false);
const showDeleteModal = ref(false);
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');
const isEditing = ref(true); // Always true now since we only edit
const deleting = ref(false);

const selectedMitra = ref(null);

// Form data
const form = ref({
  name: '',
  email: '',
  company: '',
  password: '',
  password_confirmation: '',
  errors: {}
});

// Watch for changes in props.mitra and update mitraList accordingly
watch(() => props.mitra, (newMitra) => {
  if (newMitra) {
    mitraList.value = newMitra;
  }
}, { deep: true });

// Computed properties
const filteredMitra = computed(() => {
  if (!searchQuery.value) {
    return mitraList.value;
  }
  
  const query = searchQuery.value.toLowerCase();
  return mitraList.value.filter(mitra => 
    mitra.name.toLowerCase().includes(query) || 
    mitra.email.toLowerCase().includes(query)
  );
});

// Methods
const filterMitra = () => {
  // Filtering is handled by the computed property
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Removed openCreateModal function as per requirements

const openEditModal = (mitra) => {
  isEditing.value = true;
  selectedMitra.value = mitra;
  form.value = {
    name: mitra.name,
    email: mitra.email,
    company: mitra.company || '',
    password: '',
    password_confirmation: '',
    errors: {}
  };
  showMitraModal.value = true;
};

const closeMitraModal = () => {
  showMitraModal.value = false;
};

const openDetailModal = (mitra) => {
  // For now, we'll just show a toast with mitra details
  showToastMessage(`Detail mitra: ${mitra.name}`, 'success');
};

const openDeleteModal = (mitra) => {
  selectedMitra.value = mitra;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  selectedMitra.value = null;
};

const saveMitra = () => {
  // Reset errors
  form.value.errors = {};
  
  // Simple validation
  if (!form.value.name) {
    form.value.errors.name = 'Nama mitra wajib diisi';
  }
  
  if (!form.value.email) {
    form.value.errors.email = 'Email wajib diisi';
  } else if (!/\S+@\S+\.\S+/.test(form.value.email)) {
    form.value.errors.email = 'Format email tidak valid';
  }
  
  // If there are errors, don't proceed
  if (Object.keys(form.value.errors).length > 0) {
    return;
  }
  
  // Simulate API call
  form.value.processing = true;
  
  // Update existing mitra (no more create functionality)
  router.put(route('mitra.update', selectedMitra.value.id), form.value, {
    onSuccess: () => {
      form.value.processing = false;
      closeMitraModal();
      showToastMessage('Data mitra berhasil diperbarui', 'success');
      // Refresh the page to get updated data
      router.reload({ only: ['mitra'] });
    },
    onError: (errors) => {
      form.value.processing = false;
      form.value.errors = errors;
      showToastMessage('Gagal memperbarui data mitra', 'error');
    }
  });
};

const deleteMitra = () => {
  deleting.value = true;
  
  router.delete(route('mitra.destroy', selectedMitra.value.id), {
    onSuccess: () => {
      deleting.value = false;
      closeDeleteModal();
      showToastMessage('Data mitra berhasil dihapus', 'success');
      
      // Remove the deleted mitra from the local state immediately
      const index = mitraList.value.findIndex(m => m.id === selectedMitra.value.id);
      if (index !== -1) {
        mitraList.value.splice(index, 1);
      }
    },
    onError: () => {
      deleting.value = false;
      showToastMessage('Gagal menghapus mitra', 'error');
    }
  });
};

const showToastMessage = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  showToast.value = true;
  
  // Auto hide after 3 seconds
  setTimeout(() => {
    showToast.value = false;
  }, 3000);
};

const hideToast = () => {
  showToast.value = false;
};

// Fetch mitra data when component mounts
onMounted(() => {
  // In a real application, you would fetch data from the server here
  // fetchMitraData();
});
</script>

<style scoped>
/* Add any additional styles here if needed */
</style>