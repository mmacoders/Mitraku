<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import Vue3Avatar from 'vue3-avatar'
import { 
  ChevronDown,
  Settings,
  LogOut,
  Home
} from 'lucide-vue-next'

const pageProps = usePage().props
const user = computed(() => {
  return pageProps.auth?.user || { name: 'User', profile_picture: null }
})

const username = computed(() => {
  return user.value?.name || 'User'
})

const profilePicture = computed(() => {
  return user.value?.profile_picture || null
})

const dropdownOpen = ref(false)

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const logout = () => {
  router.post(route('logout'))
}
</script>

<template>
  <!-- Glassmorphism Header -->
  <div class="fixed top-0 left-0 right-0 z-50
     backdrop-blur-xl
     bg-gradient-to-r from-white/30 via-white/20 to-white/30
     dark:bg-gradient-to-r dark:from-black/30 dark:via-black/20 dark:to-black/30
     border-b border-white/20 dark:border-white/10
     shadow-lg shadow-black/5
     supports-[backdrop-filter]:backdrop-blur-2xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo and App Name -->
        <div class="flex items-center">
          <Link :href="route('mitra.dashboard')" class="flex items-center">
            <img src="/assets/logo.png" alt="SI-Huyula Logo" class="h-8 w-auto" />
            <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white">SI-Huyula</span>
          </Link>
        </div>

        <!-- Navigation Links (Optional, if needed) -->
        <div class="hidden md:flex space-x-8 ml-10">
             <Link :href="route('mitra.dashboard')" 
                   :class="route().current('mitra.dashboard') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400'"
                   class="flex items-center px-3 py-2 text-sm font-medium transition-colors">
                <Home class="w-4 h-4 mr-2" />
                Dashboard
             </Link>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Username Display -->
            <div class="text-center hidden sm:block">
              <span class="text-lg font-medium dark:text-gray-300">
                {{ username }}
              </span>
            </div>
            
            <!-- User Profile with Dropdown -->
            <div class="relative">
              <div 
                class="flex items-center cursor-pointer"
                @click="toggleDropdown"
              >
                <Vue3Avatar 
                  :name="username" 
                  :size="40"
                  :imageSrc="profilePicture"
                  class="rounded-full border-2 border-white/30 dark:border-white/20 shadow-sm"
                />
                <ChevronDown class="h-4 w-4 ml-1 text-gray-600 dark:text-gray-300" />
              </div>
              
              <!-- Dropdown Menu -->
              <div 
                v-show="dropdownOpen" 
                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
                @click.outside="closeDropdown"
              >
                <div class="py-1">
                  <Link 
                    :href="route('profile.edit')" 
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    <Settings class="h-4 w-4 inline mr-2" />
                    Settings
                </Link>
                  <a 
                    href="#" 
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click.prevent="logout"
                  >
                    <LogOut class="h-4 w-4 inline mr-2" />
                    Logout
                  </a>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="pt-16 min-h-screen bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-gray-900/50 dark:to-gray-900/50">
    <slot />
  </div>
</template>

<style scoped>
.glass-card {
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
}
</style>
