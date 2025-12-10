<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
// @ts-ignore
import VueApexCharts from 'vue3-apexcharts';
import Vue3Avatar from 'vue3-avatar';

// Define the statistics type
interface Statistics {
  total: number;
  approved: number;
  pending: number;
  rejected: number;
  // Removed revision property
  mitra: number;
  scheduled_meetings: number;
}

const user = computed(() => {
  const userData = usePage().props.auth?.user;
  return userData || { name: 'User' };
});

const username = computed(() => {
  return user.value?.name || 'User';
});

const profilePicture = computed(() => {
  return user.value?.profile_picture || null;
});

// Check for dark mode
const isDarkMode = ref(false);

// Get initial statistics from page props (server-side rendered data)
const pageProps = usePage().props;
const initialStatistics = pageProps.statistics as Statistics | undefined;

// State for statistics - initialized with server-side data, then updated via API
// This data is fetched from the database through API endpoints:
// - /api/dashboard/summary (for statistics)
// - /api/dashboard/chart-data (for chart information)
const statistics = ref<Statistics>({
  total: initialStatistics?.total || 0,
  approved: initialStatistics?.approved || 0,
  pending: initialStatistics?.pending || 0,
  rejected: initialStatistics?.rejected || 0,
  // Removed revision: initialStatistics?.revision || 0,
  mitra: initialStatistics?.mitra || 0,
  scheduled_meetings: initialStatistics?.scheduled_meetings || 0
});

// Info cards data - computed from statistics
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
    value: statistics.value.mitra,
    description: 'Jumlah mitra yang masih aktif',
    icon: '👥',
    color: 'bg-green-500'
  },
  {
    id: 3,
    title: 'Jadwal Rapat',
    value: statistics.value.scheduled_meetings,
    description: 'Rapat yang akan datang',
    icon: '📅',
    color: 'bg-purple-500'
  },
]);

// Chart data
const donutChartSeries = ref([0, 0, 0]); // Changed from [0, 0, 0, 0] to [0, 0, 0]
const donutChartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: ['Disetujui', 'Dalam Proses', 'Ditolak'], // Removed 'Revisi'
  colors: ['#10B981', '#3B82F6', '#EF4444'], // Removed '#8B5CF6' (purple for revision)
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
const recentActivities = ref<any[]>([]);

// Polling interval
let pollingInterval: number | null = null;

// Update chart themes based on dark mode
const updateChartThemes = () => {
  const mode = isDarkMode.value ? 'dark' : 'light';
  
  // Update donut chart theme
  donutChartOptions.value = {
    ...donutChartOptions.value,
    theme: {
      mode: mode
    }
  };
  
  // Update line chart theme
  lineChartOptions.value = {
    ...lineChartOptions.value,
    theme: {
      mode: mode
    }
  };
};

// Check for dark mode preference
const checkDarkModePreference = () => {
  // Check localStorage first
  const savedDarkMode = localStorage.getItem('darkMode');
  if (savedDarkMode === 'true') {
    isDarkMode.value = true;
  } else if (savedDarkMode === 'false') {
    isDarkMode.value = false;
  } else {
    // Default to system preference
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
  }
  
  updateChartThemes();
};

// Watch for dark mode changes
const watchDarkMode = () => {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
        const isDark = document.documentElement.classList.contains('dark');
        if (isDarkMode.value !== isDark) {
          isDarkMode.value = isDark;
          updateChartThemes();
        }
      }
    });
  });
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  });
  
  return observer;
};

// Fetch dashboard data from API endpoint /api/dashboard/summary
// This endpoint retrieves real-time data from the database:
// - Mitra count from users table where role = 'mitra'
// - Scheduled meetings count from rapat table where status = 'akan_datang' and tanggal_waktu >= now()
// - PKS submission counts grouped by status from pks_submissions table
const fetchDashboardData = async () => {
  try {
    // Fetch summary data from API
    const summaryResponse = await axios.get('/api/dashboard/summary');
    statistics.value = summaryResponse.data.statistics;
    
    // Fetch chart data
    const chartResponse = await axios.get('/api/dashboard/chart-data');
    const statusData = chartResponse.data.statusDistribution;
    
    // Update donut chart series
    const statusMap: Record<string, number> = {
      'disetujui': 0,
      'proses': 1,
      'ditolak': 2
    };
    
    // Changed from [0, 0, 0, 0] to [0, 0, 0]
    const newDonutSeries = [0, 0, 0];
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
    
    // Fetch recent activities
    if (pageProps.recentActivities) {
      recentActivities.value = Array.isArray(pageProps.recentActivities) ? pageProps.recentActivities : [];
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

// Start polling for real-time updates (fetches data every 30 seconds)
const startPolling = () => {
  pollingInterval = window.setInterval(() => {
    fetchDashboardData();
  }, 30000);
};

// Stop polling
const stopPolling = () => {
  if (pollingInterval) {
    clearInterval(pollingInterval);
    pollingInterval = null;
  }
};

onMounted(() => {
  checkDarkModePreference();
  const observer = watchDarkMode();
  fetchDashboardData();
  startPolling();
  
  // Clean up observer and polling
  return () => {
    observer.disconnect();
    stopPolling();
  };
});

onUnmounted(() => {
  stopPolling();
});

// Methods
const getActivityIcon = (type: string) => {
  switch (type) {
    case 'pks_submitted':
      return '📝';
    case 'pks_status_updated':
      return '🔄';
    case 'pks_revision_requested': // This can be kept for backward compatibility
      return '✏️';
    case 'rapat_scheduled':
      return '📅';
    default:
      return '🔔';
  }
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

</script>

<template>
  <AdminLayout>
    <Head title="Dashboard Admin - SI-Huyula"/>
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
         <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl shadow-sm p-6 border border-blue-100 dark:border-blue-900/50">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di SI-Huyula 🎉</h1>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div 
            v-for="card in infoCards" 
            :key="card.id"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg flex flex-col"
          >
            <div class="flex items-center">
              <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <span class="text-2xl">{{ card.icon }}</span>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ card.title }}</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ card.value }}</p>
              </div>
            </div>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ card.description }}</p>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Donut Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi Status Pengajuan</h3>
            <VueApexCharts 
              type="donut" 
              :options="donutChartOptions" 
              :series="donutChartSeries" 
              height="300" 
            />
          </div>

          <!-- Line Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tren Pengajuan Bulanan</h3>
            <VueApexCharts 
              type="line" 
              :options="lineChartOptions" 
              :series="lineChartSeries" 
              height="300" 
            />
          </div>
        </div>

        <!-- Recent Activities and Filters -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent Activities -->
          <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
              <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500 dark:text-gray-400" v-if="recentActivities.length > 0">
                  ({{ recentActivities.length }} aktivitas)
                </span>
                <button 
                  v-if="recentActivities.length >= 4" 
                  class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  Lihat Semua
                </button>
              </div>
            </div>
            <div class="space-y-4">
              <div 
                v-for="activity in recentActivities" 
                :key="activity.id"
                class="flex items-start p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
              >
                <div class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                  <span class="text-lg">
                    {{ getActivityIcon(activity.data.type || activity.type) }}
                  </span>
                </div>
                <div class="ml-4 flex-1">
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ activity.data.message || activity.message }}
                  </h4>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ formatDate(activity.created_at) }}
                  </p>
                </div>
              </div>
              <div v-if="recentActivities.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                Tidak ada aktivitas terbaru. Anda seharusnya melihat aktivitas di sini, coba refresh halaman atau periksa apakah ada notifikasi baru.
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter Data</h3>
            
            <!-- Search -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
              <div class="relative">
                <input
                  type="text"
                  placeholder="Cari pengajuan..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <select
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              >
                <option value="">Semua Status</option>
                <option value="approved">Disetujui</option>
                <option value="pending">Dalam Proses</option>
                <!-- Removed revision option -->
                <option value="rejected">Ditolak</option>
              </select>
            </div>

            <!-- Date Range -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rentang Tanggal</label>
              <div class="relative">
                <input
                  type="text"
                  value="Pilih tanggal..."
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white cursor-pointer"
                  readonly
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <button
              class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
            >
              Terapkan Filter
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>