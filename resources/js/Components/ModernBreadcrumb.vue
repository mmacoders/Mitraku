<template>
  <nav class="flex items-center" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
      <template v-for="(segment, index) in breadcrumbSegments" :key="index">
        <li>
          <Link
            v-if="index < breadcrumbSegments.length - 1"
            :href="segment.url"
            class="inline-flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-blue-600 transition-all duration-300 ease-in-out"
          >
            <component :is="segment.icon" class="w-4 h-4 mr-2" />
            <span class="hidden sm:inline truncate max-w-[120px]">{{ segment.title }}</span>
            <span class="sm:hidden">{{ segment.shortTitle || segment.title }}</span>
          </Link>
          <div
            v-else
            class="inline-flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
          >
            <component :is="segment.icon" class="w-4 h-4 mr-2" />
            <span class="truncate max-w-[120px]">{{ segment.title }}</span>
          </div>
        </li>

        <!-- Separator -->
        <li v-if="index < breadcrumbSegments.length - 1">
          <div class="flex items-center">
            <ChevronRight class="w-4 h-4 text-gray-400 dark:text-gray-500" />
          </div>
        </li>
      </template>
    </ol>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Home, FileText, Users, CalendarDays, ChevronRight, User } from 'lucide-vue-next'

// Map route names to breadcrumb segments
const routeToBreadcrumbMap = {
  'dashboard': { title: 'Dashboard', icon: Home, shortTitle: 'Home' },
  'admin.kelola.pks.index': { title: 'Kelola Pengajuan PKS', icon: FileText, shortTitle: 'PKS' },
  'admin.kelola-rapat.index': { title: 'Kelola Rapat', icon: CalendarDays, shortTitle: 'Rapat' },
  'kelola.pks': { title: 'Kelola Pengajuan PKS', icon: FileText, shortTitle: 'PKS' },
  'kelola.mitra': { title: 'Kelola Mitra', icon: Users, shortTitle: 'Mitra' },
  'rapat.index': { title: 'Kelola Rapat', icon: CalendarDays, shortTitle: 'Rapat' },
  'profile.edit': { title: 'Profil', icon: User, shortTitle: 'Profil' }
}

// Get the current route name
const currentRoute = computed(() => {
  return route().current()
})

// Generate breadcrumb segments based on the current route
const breadcrumbSegments = computed(() => {
  const segments = [
    { 
      title: 'Dashboard', 
      url: route('dashboard'), 
      icon: Home,
      shortTitle: 'Home'
    }
  ]

  // Add the current page segment if it's mapped
  if (currentRoute.value && routeToBreadcrumbMap[currentRoute.value]) {
    segments.push({
      ...routeToBreadcrumbMap[currentRoute.value],
      url: route(currentRoute.value)
    })
  }

  return segments
})
</script>