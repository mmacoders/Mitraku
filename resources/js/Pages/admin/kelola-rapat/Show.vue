<template>
  <Head :title="'Detail Rapat: ' + rapat.judul" />

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
            <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Detail Rapat</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Rapat</h1>
      </div>
      <div class="mt-4 sm:mt-0 flex space-x-2">
        <Link
          :href="route('rapat.edit', rapat.id)"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          Edit Rapat
        </Link>
        <button
          @click="deleteRapat"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
          Hapus Rapat
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Rapat Details -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Rapat</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Judul Rapat</h3>
              <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ rapat.judul }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1" :class="getStatusClass(rapat.status)">
                {{ getStatusText(rapat.status) }}
              </span>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal & Waktu</h3>
              <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ formatDate(rapat.tanggal_waktu) }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi/Link Meeting</h3>
              <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ rapat.lokasi || 'Tidak ditentukan' }}</p>
            </div>
          </div>
          <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</h3>
            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ rapat.deskripsi || 'Tidak ada deskripsi' }}</p>
          </div>
        </div>
      </div>

      <!-- Creator Info -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">Dibuat Oleh</h2>
        </div>
        <div class="p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <span class="text-blue-800 dark:text-blue-200 font-medium">{{ rapat.creator.name.charAt(0) }}</span>
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ rapat.creator.name }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ rapat.creator.email }}</p>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat pada</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(rapat.created_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Invited Mitra -->
      <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">Mitra yang Diundang</h2>
        </div>
        <div class="p-6">
          <div v-if="rapat.invited_mitra.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="mitra in rapat.invited_mitra" :key="mitra.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <span class="text-green-800 dark:text-green-200 font-medium">{{ mitra.name.charAt(0) }}</span>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-md font-medium text-gray-900 dark:text-white">{{ mitra.name }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ mitra.email }}</p>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1" :class="getKehadiranClass(mitra.pivot.status_kehadiran)">
                    {{ getKehadiranText(mitra.pivot.status_kehadiran) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada mitra yang diundang</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada mitra yang diundang ke rapat ini.</p>
            <div class="mt-4">
              <Link
                :href="route('rapat.edit', rapat.id)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Tambah Mitra
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  rapat: Object,
});

// Delete rapat
const deleteRapat = () => {
  if (confirm('Apakah Anda yakin ingin menghapus rapat ini?')) {
    router.delete(route('rapat.destroy', props.rapat.id), {
      onSuccess: () => {
        router.visit(route('rapat.index'));
      }
    });
  }
};

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Get status class
const getStatusClass = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
    case 'selesai':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    case 'dibatalkan':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
  }
};

// Get status text
const getStatusText = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'Akan Datang';
    case 'selesai':
      return 'Selesai';
    case 'dibatalkan':
      return 'Dibatalkan';
    default:
      return status;
  }
};

// Get kehadiran class
const getKehadiranClass = (status) => {
  switch (status) {
    case 'hadir':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    case 'tidak_hadir':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    case 'belum_dikonfirmasi':
    default:
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
  }
};

// Get kehadiran text
const getKehadiranText = (status) => {
  switch (status) {
    case 'hadir':
      return 'Hadir';
    case 'tidak_hadir':
      return 'Tidak Hadir';
    case 'belum_dikonfirmasi':
    default:
      return 'Belum Dikonfirmasi';
  }
};
</script>