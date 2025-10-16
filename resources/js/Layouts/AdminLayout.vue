<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Off-canvas sidebar for mobile -->
    <div v-if="sidebarOpen" class="fixed inset-0 flex z-50 lg:hidden" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity ease-linear duration-300" aria-hidden="true"></div>
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 transition ease-in-out duration-300 transform">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
          <button type="button" @click="toggleSidebar" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
            <span class="sr-only">Close sidebar</span>
            <PanelLeftClose class="h-6 w-6 text-white" />
          </button>
        </div>
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
          <div class="flex-shrink-0 flex justify-center items-center w-full px-4">
            <img src="/assets/logo-app.png" alt="Mitraku Logo" class="h-10 w-auto">
          </div>
          <nav class="mt-5 px-2 space-y-1">
            <Link
              :href="route('admin.dashboard')"
              :class="[
                route().current('admin.dashboard') 
                  ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
                'group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
              ]"
            >
              <Home class="mr-4 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('admin.dashboard') ? 'text-white' : ''" />
              <span>Dashboard</span>
            </Link>
            <Link
              :href="route('kelola.pks')"
              :class="[
                route().current('kelola.pks') || route().current('pks.*') 
                  ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
                'group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
              ]"
            >
              <FileText class="mr-4 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('kelola.pks') || route().current('pks.*') ? 'text-white' : ''" />
              <span>Kelola Pengajuan PKS</span>
            </Link>
            <Link
              :href="route('kelola.mitra')"
              :class="[
                route().current('kelola.mitra') 
                  ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
                'group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
              ]"
            >
              <Users class="mr-4 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('kelola.mitra') ? 'text-white' : ''" />
              <span>Kelola Mitra</span>
            </Link>
            <Link
              :href="route('rapat.index')"
              :class="[
                route().current('rapat.*') 
                  ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
                'group flex items-center px-3 py-3 text-base font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
              ]"
            >
              <CalendarDays class="mr-4 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('rapat.*') ? 'text-white' : ''" />
              <span>Kelola Rapat</span>
            </Link>
          </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
          <div class="flex items-center">
            <div>
              <div class="flex-shrink-0">
                <div @click="toggleUserDropdown" class="cursor-pointer transition-all duration-200 ease-in-out transform hover:scale-105">
                  <Vue3Avatar 
                    :name="username" 
                    :size="48"
                    :imageSrc="profilePicture"
                    class="rounded-full border-2 border-gray-300 dark:border-gray-600"
                  />
                </div>
              </div>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ username }}</p>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate">{{ user?.role === 'admin' ? 'Administrator' : user?.role === 'mitra' ? 'Mitra' : 'Guest' }}</p>
            </div>
          </div>
        </div>
        
        <!-- User dropdown for mobile -->
        <div v-if="showUserDropdown" class="absolute bottom-16 left-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50 border border-gray-200 dark:border-gray-700">
          <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</Link>
          <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
        </div>
      </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div 
      class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 transition-all duration-300 ease-in-out"
      :class="sidebarCollapsed ? 'lg:w-[4.5rem]' : 'lg:w-64'"
    >
      <div class="flex-1 flex flex-col min-h-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-r border-gray-200 dark:border-gray-700 shadow-lg">
        <div class="flex items-center justify-center flex-shrink-0 px-4 py-5">
          <!-- Full logo when sidebar is expanded -->
          <img v-if="!sidebarCollapsed" src="/assets/logo-app.png" alt="Mitraku Logo" class="h-10 w-auto">
          <!-- Mobile logo when sidebar is collapsed -->
          <img v-else src="/assets/logo-mobile.png" alt="Mitraku Logo" class="h-12 w-auto">
        </div>
        
        <!-- Separator line and Menu Aplikasi text -->
        <div v-if="!sidebarCollapsed" class="px-4">
          <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
          <p class="text-xs font-medium text-center text-gray-800 dark:text-gray-400 uppercase tracking-wider mb-4">Menu Aplikasi</p>
        </div>
        
        <nav class="flex-1 px-3 space-y-1 py-4">
          <Link
            :href="route('admin.dashboard')"
            :class="[
              route().current('admin.dashboard') 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 border-l-4 border-white' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
              'group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
            ]"
          >
            <Home class="flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('admin.dashboard') ? 'text-white' : ''" />
            <span v-if="!sidebarCollapsed" class="ml-3">Dashboard</span>
          </Link>

          <!-- Kelola PKS -->
          <Link
            :href="route('kelola.pks')"
            :class="[
              route().current('kelola.pks') || route().current('pks.*') 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 border-l-4 border-white' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
              'group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
            ]"
          >
            <FileText class="flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('kelola.pks') || route().current('pks.*') ? 'text-white' : ''" />
            <span v-if="!sidebarCollapsed" class="ml-3">Kelola Pengajuan PKS</span>
          </Link>
          
          <!-- Kelola Mitra -->
          <Link
            :href="route('kelola.mitra')"
            :class="[
              route().current('kelola.mitra') 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 border-l-4 border-white' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
              'group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
            ]"
          >
            <Users class="flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('kelola.mitra') ? 'text-white' : ''" />
            <span v-if="!sidebarCollapsed" class="ml-3">Kelola Mitra</span>
          </Link>
          
          <!-- Menu Rapat -->
          <Link
            :href="route('rapat.index')"
            :class="[
              route().current('rapat.*') 
                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 border-l-4 border-white' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-blue-400',
              'group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 ease-in-out transform hover:translate-x-1'
            ]"
          >
            <CalendarDays class="flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-blue-500" :class="route().current('rapat.*') ? 'text-white' : ''" />
            <span v-if="!sidebarCollapsed" class="ml-3">Kelola Rapat</span>
          </Link>
        </nav>
        <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
          <div class="flex items-center w-full">
            <div>
              <div class="flex-shrink-0">
                <div 
                  @click="toggleUserDropdown" 
                  class="cursor-pointer transition-all duration-200 ease-in-out transform hover:scale-105"
                >
                  <Vue3Avatar 
                    :name="username" 
                    :size="48" 
                    :imageSrc="profilePicture"
                    class="border-2 border-gray-300 dark:border-gray-600 rounded-full"
                  />
                </div>
              </div>
            </div>
            <div v-if="!sidebarCollapsed" class="ml-3 overflow-hidden">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ username }}</p>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate">{{ user?.role === 'admin' ? 'Administrator' : user?.role === 'mitra' ? 'Mitra' : 'Guest' }}</p>
            </div>
          </div>
        </div>
        
        <!-- User dropdown for desktop -->
        <div 
          v-if="showUserDropdown && !sidebarCollapsed" 
          class="absolute bottom-16 left-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50 border border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out transform origin-bottom"
          :class="showUserDropdown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
        >
          <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</Link>
          <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
        </div>
      </div>
    </div>

    <div 
      class="flex flex-col flex-1 transition-all duration-300 ease-in-out"
      :class="sidebarCollapsed ? 'lg:pl-[4.5rem]' : 'lg:pl-64'"
    >
      <div class="sticky top-0 z-10 flex-shrink-0 flex items-center justify-between h-16 px-6 bg-white dark:bg-gray-900 backdrop-blur-md shadow-sm dark:shadow-gray-800/50">
        <div class="flex items-center">
          <!-- Close/Open sidebar icon next to breadcrumb -->
          <button 
            @click="toggleSidebarCollapse" 
            class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none transition-all duration-300 ease-in-out"
            :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
          >
            <PanelLeftOpen v-if="sidebarCollapsed" class="h-5 w-5" />
            <PanelLeftClose v-else class="h-5 w-5" />
          </button>
          <!-- Modern Breadcrumb Component -->
          <ModernBreadcrumb />
        </div>
        <div class="flex items-center">
          
          <!-- Dark mode toggle -->
          <button 
            @click="toggleDarkMode" 
            class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none relative transition-all duration-300 ease-in-out transform hover:scale-105"
          >
            <span class="sr-only">Toggle dark mode</span>
            <Sun v-if="isDarkMode" class="h-5 w-5" />
            <Moon v-else class="h-5 w-5" />
          </button>

          <!-- Notification Icon -->
          <div class="relative ml-4" id="notification-dropdown">
            <button 
                @click.stop="toggleNotificationsDropdown" 
                class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none relative transition-all duration-300 ease-in-out transform hover:scale-110"
            >
                <span class="sr-only">View notifications</span>
                <Bell class="h-5 w-5" />
                <span v-if="unreadNotificationsCount > 0" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
            </button>

            <!-- Notifications dropdown -->
            <div 
                v-if="showNotificationsDropdown" 
                class="origin-top-right absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 backdrop-blur-md transition ease-out duration-200 transform z-50"
                :class="showNotificationsDropdown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
            >
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Notifikasi</p>
                        <button v-if="unreadNotificationsCount > 0" @click="markAllAsRead" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 flex items-center">
                            <CheckCircle class="h-4 w-4 mr-1" />
                            Tandai semua dibaca
                        </button>
                    </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="notifications.length === 0" class="px-4 py-6 text-center text-sm text-gray-700 dark:text-gray-300">
                        Tidak ada notifikasi
                    </div>
                    <div 
                        v-for="notification in notifications" 
                        :key="notification.id" 
                        @click.stop="markAsRead(notification.id)" 
                        class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-all duration-150 border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                        :class="getNotificationBackgroundColor(notification.type)"
                    >
                        <div class="flex items-start">
                            <div class="flex-shrink-0 pt-0.5">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full" 
                                    :class="getNotificationIconBackground(notification.type)">
                                    <component 
                                        :is="getNotificationIconComponent(notification.type)" 
                                        class="w-4 h-4" 
                                        :class="getNotificationColor(notification.type)"
                                    />
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ formatNotificationMessage(notification) }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDate(notification.created_at) }}
                                </p>
                            </div>
                            <div v-if="!notification.read_at" class="flex-shrink-0 ml-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-400"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="notifications.length > 0" class="text-sm text-center text-blue-600 dark:text-blue-400 py-2 border-t border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10">
                    <Link :href="route('kelola.pks')" class="block w-full h-full">
                        Lihat semua notifikasi
                    </Link>
                </div>
            </div>
          </div>

          <!-- Profile dropdown -->
          <div class="ml-4 relative" id="profile-dropdown">
            <div class="flex items-center">
              <div 
                @click.stop="toggleProfileDropdown" 
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out cursor-pointer transition-all duration-200 ease-in-out transform hover:scale-105"
              >
                <Vue3Avatar 
                  :name="username" 
                  :size="36"
                  :imageSrc="profilePicture"
                  class="rounded-full border-2 border-gray-300 dark:border-gray-600"
                />
              </div>
            </div>
            
            <!-- Profile dropdown menu -->
            <div 
              v-if="showProfileDropdown" 
              class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50 border border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out transform"
              :class="showProfileDropdown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
            >
              <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profil</Link>
              <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Pengaturan</Link>
              <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
            </div>
          </div>
        </div>
      </div>

      <main class="flex-1">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { 
  Users, 
  Home, 
  FileText, 
  CalendarDays, 
  PanelLeftClose, 
  PanelLeftOpen,
  Menu,
  Sun,
  Moon,
  Bell,
  CheckCircle,
  User,
  XCircle
} from 'lucide-vue-next';
import Vue3Avatar from 'vue3-avatar';
import ModernBreadcrumb from '@/Components/ModernBreadcrumb.vue';

// Define reactive properties
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const showNotificationsDropdown = ref(false);
const showProfileDropdown = ref(false);
const showUserDropdown = ref(false);
const notifications = ref([]);
const unreadNotificationsCount = ref(0);
const isDarkMode = ref(false);

// Get user from Inertia page props with a fallback
const user = computed(() => {
  const userData = usePage().props.auth?.user;
  return userData || { name: 'U', role: 'guest', profile_picture: null };
});

// Get username safely for avatar component
const username = computed(() => {
  return user.value?.name || 'User';
});

// Get profile picture URL for avatar component
const profilePicture = computed(() => {
  return user.value?.profile_picture || null;
});

// Toggle sidebar
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

// Toggle sidebar collapse
const toggleSidebarCollapse = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value;
  // Close user dropdown when collapsing sidebar
  if (sidebarCollapsed.value) {
    showUserDropdown.value = false;
  }
};

// Toggle notifications dropdown
const toggleNotificationsDropdown = () => {
  showNotificationsDropdown.value = !showNotificationsDropdown.value;
  // Close other dropdowns
  if (showNotificationsDropdown.value) {
    showProfileDropdown.value = false;
    showUserDropdown.value = false;
  }
};

// Toggle profile dropdown
const toggleProfileDropdown = () => {
  showProfileDropdown.value = !showProfileDropdown.value;
  // Close other dropdowns
  if (showProfileDropdown.value) {
    showNotificationsDropdown.value = false;
    showUserDropdown.value = false;
  }
};

// Toggle user dropdown
const toggleUserDropdown = () => {
  showUserDropdown.value = !showUserDropdown.value;
  // Close other dropdowns
  if (showUserDropdown.value) {
    showNotificationsDropdown.value = false;
    showProfileDropdown.value = false;
  }
};

// Toggle dark mode
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  if (isDarkMode.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('darkMode', 'true');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('darkMode', 'false');
  }
};

// Logout function
const logout = () => {
  router.post(route('logout'));
};

// Fetch notifications
const fetchNotifications = async () => {
  try {
    const response = await fetch('/api/notifications');
    if (response.ok) {
      const data = await response.json();
      notifications.value = data;
      unreadNotificationsCount.value = data.filter(notification => !notification.read_at).length;
    } else {
      console.error('Failed to fetch notifications. Status:', response.status);
      // If unauthorized, redirect to login
      if (response.status === 401) {
        window.location.href = '/login';
      }
    }
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

// Mark notification as read
const markAsRead = async (id) => {
  try {
    const response = await fetch(`/api/notifications/${id}/read`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      })
    });
    
    if (response.ok) {
      // Update notification in the list
      const index = notifications.value.findIndex(notification => notification.id === id);
      if (index !== -1) {
        notifications.value[index].read_at = new Date().toISOString();
        unreadNotificationsCount.value = notifications.value.filter(notification => !notification.read_at).length;
      }
      // Refresh notifications to ensure consistency
      await fetchNotifications();
    } else if (response.status === 404) {
      // Notification not found, remove it from the list
      notifications.value = notifications.value.filter(notification => notification.id !== id);
      unreadNotificationsCount.value = notifications.value.filter(notification => !notification.read_at).length;
      console.log('Notification not found, removed from list');
    } else {
      console.error('Failed to mark notification as read. Status:', response.status);
    }
  } catch (error) {
    console.error('Error marking notification as read:', error);
  }
};

// Mark all notifications as read
const markAllAsRead = async () => {
  try {
    // Get all unread notifications
    const unreadNotifications = notifications.value.filter(notification => !notification.read_at);
    
    // If there are no unread notifications, do nothing
    if (unreadNotifications.length === 0) {
      return;
    }
    
    // Track successfully marked notifications
    const successfullyMarked = [];
    
    // Mark each notification as read
    for (const notification of unreadNotifications) {
      try {
        const response = await fetch(`/api/notifications/${notification.id}/read`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify({
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          })
        });
        
        if (response.ok) {
          // Update notification in the list immediately
          const index = notifications.value.findIndex(n => n.id === notification.id);
          if (index !== -1) {
            notifications.value[index].read_at = new Date().toISOString();
            successfullyMarked.push(notification.id);
          }
        } else if (response.status === 404) {
          // Notification not found, remove it from the list
          notifications.value = notifications.value.filter(n => n.id !== notification.id);
        } else {
          console.error('Failed to mark notification as read. Status:', response.status);
        }
      } catch (error) {
        console.error('Error marking notification as read:', error);
      }
    }
    
    // Update unread count
    unreadNotificationsCount.value = notifications.value.filter(notification => !notification.read_at).length;
    
    // Refresh notifications to ensure consistency
    await fetchNotifications();
    
    console.log(`Marked ${successfullyMarked.length} notifications as read`);
  } catch (error) {
    console.error('Error marking all notifications as read:', error);
  }
};

// Format notification message
const formatNotificationMessage = (notification) => {
  // Handle case where notification data might be missing
  if (!notification.data) {
    return 'New notification';
  }
  
  // Return custom message if provided
  if (notification.data.message) {
    return notification.data.message;
  }
  
  // Generate message based on notification type
  switch (notification.type) {
    case 'App\\Notifications\\PksSubmitted':
      return `Pengajuan PKS baru: ${notification.data.pks_submission_title || 'Tanpa judul'}`;
    case 'App\\Notifications\\PksStatusUpdated':
      return `Status PKS "${notification.data.pks_submission_title || 'Tanpa judul'}" diperbarui menjadi ${notification.data.new_status || 'baru'}`;
    case 'App\\Notifications\\PksRevisionRequested':
      return `Revisi diminta untuk PKS: ${notification.data.pks_submission_title || 'Tanpa judul'}`;
    case 'App\\Notifications\\AdminPksSubmissionNotification':
      return `Pengajuan PKS baru dari ${notification.data.mitra_name || 'mitra'}`;
    default:
      return 'New notification';
  }
};

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Get notification background color based on type
const getNotificationBackgroundColor = (type) => {
  switch (type) {
    case 'pks_submitted':
      return 'bg-blue-50 dark:bg-blue-900/20';
    case 'pks_approved':
      return 'bg-green-50 dark:bg-green-900/20';
    case 'pks_rejected':
      return 'bg-red-50 dark:bg-red-900/20';
    case 'pks_revision_requested':
      return 'bg-yellow-50 dark:bg-yellow-900/20';
    case 'rapat_scheduled':
      return 'bg-purple-50 dark:bg-purple-900/20';
    default:
      return 'bg-gray-50 dark:bg-gray-700/20';
  }
};

// Get notification icon background color based on type
const getNotificationIconBackground = (type) => {
  switch (type) {
    case 'pks_submitted':
      return 'bg-blue-100 dark:bg-blue-900';
    case 'pks_approved':
      return 'bg-green-100 dark:bg-green-900';
    case 'pks_rejected':
      return 'bg-red-100 dark:bg-red-900';
    case 'pks_revision_requested':
      return 'bg-yellow-100 dark:bg-yellow-900';
    case 'rapat_scheduled':
      return 'bg-purple-100 dark:bg-purple-900';
    default:
      return 'bg-gray-100 dark:bg-gray-600';
  }
};

// Get notification icon component based on type
const getNotificationIconComponent = (type) => {
  switch (type) {
    case 'pks_submitted':
      return FileText; // Document icon for submitted PKS
    case 'pks_approved':
      return CheckCircle; // Check circle for approved PKS
    case 'pks_rejected':
      return XCircle; // X circle for rejected PKS
    case 'pks_revision_requested':
      return Bell; // Bell for revision requests
    case 'rapat_scheduled':
      return CalendarDays; // Calendar for scheduled meetings
    default:
      return FileText; // Default to document icon
  }
};

// Get notification color based on type
const getNotificationColor = (type) => {
  switch (type) {
    case 'pks_submitted':
      return 'text-blue-500';
    case 'pks_approved':
      return 'text-green-500';
    case 'pks_rejected':
      return 'text-red-500';
    case 'pks_revision_requested':
      return 'text-yellow-500';
    case 'rapat_scheduled':
      return 'text-purple-500';
    default:
      return 'text-gray-500';
  }
};

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  // More specific checks for each dropdown
  const notificationDropdown = document.getElementById('notification-dropdown');
  const profileDropdown = document.getElementById('profile-dropdown');
  
  // Check if click is outside the notification dropdown
  if (showNotificationsDropdown.value && notificationDropdown && !notificationDropdown.contains(event.target)) {
    showNotificationsDropdown.value = false;
  }
  
  // Check if click is outside the profile dropdown
  if (showProfileDropdown.value && profileDropdown && !profileDropdown.contains(event.target)) {
    showProfileDropdown.value = false;
  }
  
  // Check if click is outside the user dropdown (mobile)
  if (showUserDropdown.value && !event.target.closest('.flex-shrink-0')) {
    showUserDropdown.value = false;
  }
};

// Check for saved dark mode preference
const checkDarkModePreference = () => {
  const savedDarkMode = localStorage.getItem('darkMode');
  if (savedDarkMode === 'true') {
    isDarkMode.value = true;
    document.documentElement.classList.add('dark');
  } else if (savedDarkMode === 'false') {
    isDarkMode.value = false;
    document.documentElement.classList.remove('dark');
  } else {
    // Default to system preference
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark');
    }
  }
};

// Mount and unmount event listeners
onMounted(() => {
  // Check dark mode preference
  checkDarkModePreference();
  
  // Fetch notifications on component mount
  fetchNotifications();
  
  // Set up interval to fetch notifications every 30 seconds
  const notificationInterval = setInterval(fetchNotifications, 30000);
  
  // Add click outside listener
  document.addEventListener('click', handleClickOutside);
  
  // Clean up on unmount
  onUnmounted(() => {
    clearInterval(notificationInterval);
    document.removeEventListener('click', handleClickOutside);
  });
});

// Watch for route changes to close mobile sidebar
watch(() => usePage().props, () => {
  sidebarOpen.value = false;
});
</script>