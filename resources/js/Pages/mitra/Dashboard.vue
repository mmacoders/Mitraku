<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { 
  Bell, 
  Sun, 
  Moon, 
  User, 
  Settings, 
  LogOut,
  Menu,
  X,
  FileText,
  CheckCircle,
  Clock,
  XCircle,
  Calendar,
  Plus,
  Eye,
  Trash2
} from 'lucide-vue-next';
import PksSubmissionDetailModal from '@/Components/PksSubmissionDetailModal.vue';
import PksSubmissionModal from '@/Components/PksSubmissionModal.vue';
import { PksSubmission, PaginatedSubmissions } from '@/types';

interface Props {
  submissions: PaginatedSubmissions;
  statistics: {
    total: number;
    approved: number;
    pending: number;
    rejected: number;
  };
}

const props = defineProps<Props>();

const selectedSubmission = ref<PksSubmission | null>(null);
const isModalOpen = ref(false);
const isCreateModalOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const isMobileMenuOpen = ref(false);

const openModal = (submission: PksSubmission) => {
  selectedSubmission.value = submission;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedSubmission.value = null;
};

const openCreateModal = () => {
  isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
  isCreateModalOpen.value = false;
};

const handleCreateSuccess = () => {
  // Refresh only the submissions and statistics data without full page reload
  router.reload({
    only: ['submissions', 'statistics'],
    onSuccess: () => {
      // Close the modal after successful submission
      closeCreateModal();
    }
  });
};

const deleteSubmission = (id: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) {
    router.delete(route('pks.destroy', id), {
      onSuccess: () => {
        // Refresh submissions after deletion
        router.reload({
          only: ['submissions', 'statistics']
        });
      }
    });
  }
};

const logout = () => {
  router.post(route('logout'));
};

const toggleDarkMode = () => {
  // Implementation would depend on your dark mode setup
  document.documentElement.classList.toggle('dark');
};

const toggleProfileDropdown = () => {
  isProfileDropdownOpen.value = !isProfileDropdownOpen.value;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// Close dropdown when clicking outside
document.addEventListener('click', (event: MouseEvent) => {
  const profileDropdown = document.getElementById('profile-dropdown');
  const profileButton = document.getElementById('profile-button');
  
  if (profileDropdown && profileButton && 
      !profileDropdown.contains(event.target as Node) && 
      !profileButton.contains(event.target as Node)) {
    isProfileDropdownOpen.value = false;
  }
});

// Handle ESC key for dropdown
document.addEventListener('keydown', (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    isProfileDropdownOpen.value = false;
    isMobileMenuOpen.value = false;
  }
});

const getStatusText = (status: string) => {
  const statusMap: Record<string, string> = {
    'proses': 'Menunggu',
    'ditolak': 'Ditolak',
    'disetujui': 'Diterima',
  };
  return statusMap[status] || status || 'Menunggu';
};

const getStatusClass = (status: string) => {
  switch (status) {
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    case 'revisi':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
  }
};

// Format validity period
const formatValidityPeriod = (submission: PksSubmission) => {
  if (submission.status === 'disetujui' && submission.validity_period_start && submission.validity_period_end) {
    const start = new Date(submission.validity_period_start).toLocaleDateString('id-ID');
    const end = new Date(submission.validity_period_end).toLocaleDateString('id-ID');
    return `${start} - ${end}`;
  }
  return '-';
};

// Check if PKS is expiring soon (within 7 days)
const isExpiringSoon = (endDate: string) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  const diffTime = Math.abs(end.getTime() - today.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 7 && end > today;
};

// Check if PKS has expired
const isExpired = (endDate: string) => {
  if (!endDate) return false;
  const end = new Date(endDate);
  const today = new Date();
  return end < today;
};

// Get days until expiration
const getDaysUntilExpiration = (endDate: string) => {
  if (!endDate) return 0;
  const end = new Date(endDate);
  const today = new Date();
  const diffTime = end.getTime() - today.getTime();
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

// Check if user can delete submission (only for 'proses' status)
const canDeleteSubmission = (submission: PksSubmission) => {
  return submission.status === 'proses';
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <!-- Left side - Logo and mobile menu button -->
          <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center">
              <div class="block h-8 w-auto text-gray-800 dark:text-gray-200 font-bold text-xl">
                Mitraku
              </div>
            </div>
            <button 
              @click="toggleMobileMenu"
              class="md:hidden ml-4 inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
            >
              <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
              <X v-else class="h-6 w-6" />
            </button>
          </div>

          <!-- Center - Greeting -->
          <div class="hidden md:block">
            <div class="text-lg font-medium text-gray-800 dark:text-gray-200">
              Selamat Datang, {{ $page.props.auth.user.name }}
            </div>
          </div>

          <!-- Right side - Icons and Profile -->
          <div class="flex items-center space-x-4">
            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-1 rounded-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <Sun class="h-6 w-6 hidden dark:block" />
              <Moon class="h-6 w-6 dark:hidden" />
            </button>

            <!-- Profile Dropdown -->
            <div class="relative">
              <button
                id="profile-button"
                @click="toggleProfileDropdown"
                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
                aria-expanded="false"
                aria-haspopup="true"
              >
                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center border-2 border-transparent hover:border-indigo-300 dark:hover:border-indigo-700 focus:border-indigo-500 transition-colors">
                  <span class="text-indigo-800 dark:text-indigo-200 font-medium">
                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                  </span>
                </div>
              </button>

              <!-- Dropdown Menu -->
              <div
                id="profile-dropdown"
                v-show="isProfileDropdownOpen"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="profile-button"
                tabindex="-1"
              >
                <a
                  :href="route('profile.edit')"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                  role="menuitem"
                >
                  <div class="flex items-center">
                    <User class="h-4 w-4 mr-2" />
                    <span>Profil</span>
                  </div>
                </a>
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                  role="menuitem"
                >
                  <div class="flex items-center">
                    <LogOut class="h-4 w-4 mr-2" />
                    <span>Logout</span>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div 
        v-if="isMobileMenuOpen" 
        class="md:hidden absolute top-16 inset-x-0 bg-white dark:bg-gray-800 shadow-lg z-50"
      >
        <div class="pt-2 pb-3 space-y-1 px-4">
          <div class="text-lg font-medium text-gray-800 dark:text-gray-200 py-2">
            Selamat Datang, {{ $page.props.auth.user.name }}
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="pt-6">
      <div class="max-w-7xl mx-auto px-6 md:px-8 lg:px-12">
        <!-- Welcome Card -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl shadow-sm p-6 border border-blue-100 dark:border-blue-900/50">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Dashboard Mitra 🤝</h1>
              <p class="mt-2 text-gray-600 dark:text-gray-300">Kelola pengajuan PKS Anda dengan mudah di satu platform.</p>
            </div>
            <div class="mt-4 md:mt-0">
              <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-sm">
                <User class="w-8 h-8 text-indigo-500" />
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Submissions -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/50">
                <FileText class="h-6 w-6 text-blue-600 dark:text-blue-400" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pengajuan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total }}</p>
              </div>
            </div>
          </div>

          <!-- Approved -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-green-100 dark:bg-green-900/50">
                <CheckCircle class="h-6 w-6 text-green-600 dark:text-green-400" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Disetujui</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.approved }}</p>
              </div>
            </div>
          </div>

          <!-- Pending -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/50">
                <Clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.pending }}</p>
              </div>
            </div>
          </div>

          <!-- Rejected -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100 dark:bg-red-900/50">
                <XCircle class="h-6 w-6 text-red-600 dark:text-red-400" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ditolak</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.rejected }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Submissions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
          <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Pengajuan Terbaru</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar pengajuan PKS Anda</p>
              </div>
              <button 
                @click="openCreateModal"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-blue-500 border border-transparent rounded-xl font-medium text-sm text-white shadow-md hover:from-indigo-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
              >
                <Plus class="h-4 w-4 mr-2" />
                Tambah Pengajuan PKS
              </button>
            </div>
          </div>
          <div class="p-6">
            <div v-if="submissions.data && submissions.data.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Judul PKS</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pengajuan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Pengajuan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Masa Berlaku</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="submission in submissions.data" :key="submission.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ submission.title }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">{{ submission.user?.name || '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">{{ new Date(submission.created_at).toLocaleDateString('id-ID') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(submission.status)"
                      >
                        {{ getStatusText(submission.status) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">
                        {{ formatValidityPeriod(submission) }}
                        <div 
                          v-if="submission.status === 'disetujui' && submission.validity_period_end && isExpiringSoon(submission.validity_period_end)"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1"
                        >
                          ⚠️ Akan kedaluwarsa
                        </div>
                        <div 
                          v-else-if="submission.status === 'disetujui' && submission.validity_period_end && isExpired(submission.validity_period_end)"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1"
                        >
                          ⚠️ Sudah kedaluwarsa
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2 gap-2">
                        <button 
                          @click="openModal(submission)"
                          class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                          title="Lihat Detail"
                        >
                          <Eye class="h-5 w-5" />
                        </button>
                        <button 
                          v-if="canDeleteSubmission(submission)"
                          @click="deleteSubmission(submission.id)"
                          class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                          title="Hapus"
                        >
                          <Trash2 class="h-5 w-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-8">
              <Calendar class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada pengajuan</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum membuat pengajuan PKS.</p>
              <div class="mt-6">
                <button 
                  @click="openCreateModal"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-blue-500 border border-transparent rounded-xl font-medium text-sm text-white shadow-md hover:from-indigo-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  <Plus class="h-4 w-4 mr-2" />
                  + Tambah Pengajuan PKS
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- PKS Submission Detail Modal -->
    <PksSubmissionDetailModal 
      :show="isModalOpen" 
      :submission="selectedSubmission || undefined" 
      @close="closeModal" 
    />

    <!-- PKS Submission Create Modal -->
    <PksSubmissionModal 
      :is-open="isCreateModalOpen" 
      @close="closeCreateModal"
      @success="handleCreateSuccess"
    />
  </div>
</template>

<style scoped>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.8);
}
</style>