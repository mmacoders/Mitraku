<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HttpsWarningBanner from '@/Components/HttpsWarningBanner.vue';
import { Head } from '@inertiajs/vue3';
// @ts-ignore
import VueApexCharts from 'vue3-apexcharts';

// Mock data for the dashboard
const notifications = [
  {
    id: 1,
    type: 'meeting',
    title: 'Rapat Evaluasi PKS',
    date: '12 Oktober 2025',
    time: '09:00',
    icon: '✅'
  },
  {
    id: 2,
    type: 'submission',
    title: 'Kerjasama PT. Citra Mandiri',
    date: '',
    time: '',
    icon: '📝'
  },
  {
    id: 3,
    type: 'reminder',
    title: 'Anda memiliki 2 rapat minggu ini',
    date: '',
    time: '',
    icon: '🔔'
  }
];

const statistics = {
  totalSubmissions: 16,
  approved: 3,
  pending: 10,
  rejected: 3
};

// Info cards data
const infoCards = [
  {
    id: 1,
    title: 'Jumlah Pengajuan',
    value: 16,
    icon: '📑',
    color: 'bg-blue-500'
  },
  {
    id: 2,
    title: 'Jumlah Mitra ',
    value: 24,
    icon: '👥',
    color: 'bg-green-500'
  },
  {
    id: 3,
    title: 'Jadwal Rapat',
    value: 3,
    icon: '📅',
    color: 'bg-purple-500'
  },
  {
    id: 4,
    title: 'Reminder Penting',
    value: 5,
    icon: '⚠️',
    color: 'bg-yellow-500'
  }
];

// Reactive variables for filters
const search = ref('');
const statusFilter = ref('');
const dateRange = ref('');
const showDatePicker = ref(false);
const startDate = ref('');
const endDate = ref('');

// Methods
const handleSearch = () => {
  // Implement search functionality
  console.log('Searching for:', search.value);
};

const applyFilters = () => {
  // Implement filter functionality
  console.log('Applying filters');
};

const toggleDatePicker = () => {
  showDatePicker.value = !showDatePicker.value;
};

const clearDateRange = () => {
  startDate.value = '';
  endDate.value = '';
  showDatePicker.value = false;
};

const applyDateRange = () => {
  // Implement date range functionality
  console.log('Applying date range:', startDate.value, 'to', endDate.value);
  showDatePicker.value = false;
};

// Chart data
const donutChartOptions = {
  chart: {
    type: 'donut',
  },
  labels: ['Disetujui', 'Menunggu Proses', 'Ditolak'],
  colors: ['#10B981', '#F59E0B', '#EF4444'],
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }],
  theme: {
    mode: 'light'
  }
};

const donutChartSeries = [3, 10, 3];

const lineChartOptions = {
  chart: {
    type: 'line',
    height: 350
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  },
  colors: ['#3B82F6'],
  stroke: {
    curve: 'smooth'
  },
  markers: {
    size: 4
  },
  theme: {
    mode: 'light'
  }
};

const lineChartSeries = [{
  name: 'Pengajuan',
  data: [10, 15, 8, 12, 18, 14, 16, 20, 18, 22, 25, 28]
}];
</script>

<template>
  <Head title="Dashboard Admin" />

  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- HTTPS Warning Banner -->
    <HttpsWarningBanner />
    
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <div class="block h-9 w-auto text-gray-800 dark:text-gray-200">
                <svg class="h-8 w-auto" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
            
            <!-- Navigation -->
            <nav class="ml-6 flex space-x-8">
              <a :href="route('admin.dashboard')" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium text-gray-900 dark:text-gray-100">
                <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
              </a>
              
              <a :href="route('kelola.pks')" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-500">
                <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Semua PKS
              </a>
            </nav>
          </div>
          
          <div class="flex items-center">
            <!-- Dark mode toggle placeholder -->
            <div class="flex items-center mr-4">
              <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
              </button>
            </div>
            
            <!-- Profile dropdown -->
            <div class="ml-3 relative">
              <div class="relative">
                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                  <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                    <span class="text-indigo-800 dark:text-indigo-200 font-medium">A</span>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Breadcrumb -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700" aria-label="Breadcrumb">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-10 items-center">
          <ol class="flex items-center space-x-2">
            <li>
              <div class="flex items-center">
                <svg class="h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.213-.727zM9.03 8l-1 4h2.938l1-4H9.031z" clip-rule="evenodd" />
                </svg>
                <a href="#" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">e-PKS</a>
              </div>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Dashboard</span>
              </div>
            </li>
          </ol>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Card -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl shadow-sm p-6 border border-blue-100 dark:border-blue-900/50">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di SI-Huyula 🎉</h1>
              <p class="mt-2 text-gray-600 dark:text-gray-300">Kelola pengajuan, rapat, dan mitra dengan mudah di satu platform.</p>
            </div>
            <div class="mt-4 md:mt-0">
              <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-sm">
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid 4 Info Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div v-for="card in infoCards" :key="card.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" :class="card.color + '/10'">
                <span class="text-lg">{{ card.icon }}</span>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ card.title }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ card.value }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Donut Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Distribusi Jenis Pengajuan</h2>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Persentase berdasarkan jenis pengajuan</p>
            </div>
            <div class="p-6">
              <div class="h-80">
                <apexchart type="donut" :options="donutChartOptions" :series="donutChartSeries"></apexchart>
              </div>
            </div>
          </div>

          <!-- Line Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Tren Pengajuan Per Bulan</h2>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah pengajuan per bulan</p>
            </div>
            <div class="p-6">
              <div class="h-80">
                <apexchart type="line" :options="lineChartOptions" :series="lineChartSeries"></apexchart>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Filter Pengajuan</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cari dan filter pengajuan berdasarkan kriteria tertentu</p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- Search Input -->
              <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>
                  <input
                    id="search"
                    v-model="search"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                    placeholder="Cari berdasarkan judul atau deskripsi..."
                    @input="handleSearch"
                  />
                </div>
              </div>

              <!-- Status Filter -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select
                  id="status"
                  v-model="statusFilter"
                  class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="">Semua Status</option>
                  <option value="proses">Dalam Proses</option>
                  <option value="revisi">Revisi</option>
                  <option value="disetujui">Disetujui</option>
                  <option value="ditolak">Ditolak</option>
                </select>
              </div>

              <!-- Date Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                <div class="relative">
                  <input
                    type="text"
                    :value="dateRange"
                    class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white sm:text-sm"
                    placeholder="Pilih tanggal"
                    readonly
                    @click="toggleDatePicker"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
                <!-- Date Range Picker -->
                <div v-if="showDatePicker" class="absolute z-10 mt-1 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                  <div class="flex space-x-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari</label>
                      <input
                        type="date"
                        v-model="startDate"
                        class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white sm:text-sm"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai</label>
                      <input
                        type="date"
                        v-model="endDate"
                        class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white sm:text-sm"
                      />
                    </div>
                  </div>
                  <div class="mt-4 flex justify-end space-x-2">
                    <button
                      @click="clearDateRange"
                      class="px-3 py-1 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                      Batal
                    </button>
                    <button
                      @click="applyDateRange"
                      class="px-3 py-1 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700"
                    >
                      Terapkan
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aktivitas terbaru dalam sistem</p>
          </div>
          <div class="p-6">
            <div class="flow-root">
              <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <li v-for="notification in notifications" :key="notification.id" class="py-4">
                  <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 mt-1">
                      <span class="text-lg">{{ notification.icon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ notification.title }}
                      </p>
                      <p v-if="notification.date" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ notification.date }} {{ notification.time }}
                      </p>
                    </div>
                  </div>
                </li>
                <li v-if="notifications.length === 0" class="py-4 text-center">
                  <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada aktivitas terbaru</p>
                </li>
              </ul>
            </div>
            <div class="mt-6">
              <a :href="route('kelola.pks')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Lihat Semua Pengajuan
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex justify-center md:justify-start">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
              &copy; 2025 e-PKS - Sistem Pengajuan Kerja Sama
            </p>
          </div>
          <div class="mt-4 flex justify-center md:mt-0">
            <div class="flex space-x-6">
              <a href="#" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                <span class="sr-only">Bantuan</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </a>
              <a href="#" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                <span class="sr-only">Dokumentasi</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </a>
              <a href="#" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                <span class="sr-only">Kontak</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Add any additional styles here if needed */
</style>
