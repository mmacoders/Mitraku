<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
// @ts-ignore
import VueApexCharts from 'vue3-apexcharts';
import Vue3Avatar from 'vue3-avatar';

const user = computed(() => {
  const userData = usePage().props.auth?.user;
  return userData || { name: 'U', profile_picture: null };
});

const username = computed(() => {
  return user.value?.name || 'User';
});

const profilePicture = computed(() => {
  return user.value?.profile_picture || null;
});

// State for statistics
const statistics = ref({
  total: 0,
  approved: 0,
  pending: 0,
  rejected: 0,
  revision: 0
});

// Info cards data
const infoCards = computed(() => [
  {
    id: 1,
    title: 'Jumlah Pengajuan',
    value: statistics.value.total,
    description: 'Total pengajuan yang sedang aktif',
    icon: '📑',
    color: 'bg-blue-500'
  },
  {
    id: 2,
    title: 'Jumlah Mitra',
    value: 24, // This would need to be fetched from the backend
    description: 'Jumlah mitra yang masih aktif',
    icon: '👥',
    color: 'bg-green-500'
  },
  {
    id: 3,
    title: 'Jadwal Rapat',
    value: 3, // This would need to be fetched from the backend
    description: 'Rapat yang terjadwal minggu ini',
    icon: '📅',
    color: 'bg-purple-500'
  },
  {
    id: 4,
    title: 'Reminder Penting',
    value: 5, // This would need to be fetched from the backend
    description: 'Reminder yang harus segera ditindaklanjuti',
    icon: '⚠️',
    color: 'bg-yellow-500'
  }
]);

// Reactive variables for filters
const search = ref('');
const statusFilter = ref('');
const dateRange = ref('');
const showDatePicker = ref(false);
const startDate = ref('');
const endDate = ref('');

// Chart data
const donutChartSeries = ref([0, 0, 0, 0]);
const donutChartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: ['Disetujui', 'Dalam Proses', 'Revisi', 'Ditolak'],
  colors: ['#10B981', '#3B82F6', '#8B5CF6', '#EF4444'],
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
});

const lineChartSeries = ref([{
  name: 'Pengajuan',
  data: []
}]);
const lineChartOptions = ref({
  chart: {
    type: 'line',
    height: 350
  },
  xaxis: {
    categories: []
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
});

// Recent activities data
const recentActivities = ref([
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
]);

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

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    // Fetch summary data
    const summaryResponse = await axios.get('/api/dashboard/summary');
    statistics.value = summaryResponse.data.statistics;
    
    // Fetch chart data
    const chartResponse = await axios.get('/api/dashboard/chart-data');
    const statusData = chartResponse.data.statusDistribution;
    
    // Update donut chart series
    const statusMap: Record<string, number> = {
      'disetujui': 0,
      'proses': 1,
      'revisi': 2,
      'ditolak': 3
    };
    
    const newDonutSeries = [0, 0, 0, 0];
    statusData.forEach((item: any) => {
      if (statusMap[item.status] !== undefined) {
        newDonutSeries[statusMap[item.status]] = item.count;
      }
    });
    
    donutChartSeries.value = newDonutSeries;
    
    // Update line chart series
    const monthlyData = chartResponse.data.submissionsPerMonth;
    const categories = monthlyData.map((item: any) => {
      const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      return `${monthNames[item.month - 1]} ${item.year}`;
    });
    
    const data = monthlyData.map((item: any) => item.count);
    
    // @ts-ignore
    lineChartOptions.value.xaxis.categories = categories;
    lineChartSeries.value[0].data = data;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

onMounted(() => {
  fetchDashboardData();
});
</script>

<template>
  <Head title="Dashboard Kelola PKS" />

  <AdminLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Card -->
      <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl shadow-sm p-6 border border-blue-100 dark:border-blue-900/50">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di Mitraku 🎉</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">Kelola pengajuan, rapat, dan mitra dengan mudah di satu platform.</p>
          </div>
          <div class="mt-4 md:mt-0">
            <!-- Profile Avatar in Welcome Card -->
            <Vue3Avatar 
              :name="username" 
              :size="48"
              :imageSrc="profilePicture"
              class="rounded-full border border-gray-300 dark:border-gray-600"
            />
          </div>
        </div>
      </div>

      <!-- Grid 4 Info Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div v-for="card in infoCards" :key="card.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 p-5 flex flex-col h-full">
          <div class="flex items-start">
            <div class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center" :class="card.color + '/10'">
              <span class="text-2xl">{{ card.icon }}</span>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ card.title }}</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ card.value }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ card.description }}</p>
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
      
      <!-- Recent Activity Section -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aktivitas terbaru dalam sistem</p>
        </div>
        <div class="p-6">
          <div class="flow-root">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
              <li v-for="activity in recentActivities" :key="activity.id" class="py-4">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0 mt-1">
                    <span class="text-lg">{{ activity.icon }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ activity.title }}
                    </p>
                    <p v-if="activity.date" class="text-sm text-gray-500 dark:text-gray-400">
                      {{ activity.date }} {{ activity.time }}
                    </p>
                  </div>
                </div>
              </li>
              <li v-if="recentActivities.length === 0" class="py-4 text-center">
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
  </AdminLayout>
</template>

<style scoped>
/* Add any additional styles here if needed */
</style>