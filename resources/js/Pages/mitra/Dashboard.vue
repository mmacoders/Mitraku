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
          <img src="/assets/logo.png" alt="SI-Huyula Logo" class="h-8 w-auto" />
          <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white">SI-Huyula</span>
        </div>

        <!-- Username Display -->
        <div class="text-center">
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

  <!-- Main Content with Glassmorphism Greeting -->
  <div class="pt-16 min-h-screen bg-gradient-to-br  from-blue-50/50 to-purple-50/50 dark:from-gray-900/50 dark:to-gray-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
        </div></div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div 
          v-for="card in infoCards" 
          :key="card.id"
          class="glass-card bg-white/40 dark:bg-black/30 backdrop-blur-[25px] border border-white/20 dark:border-white/10 rounded-2xl shadow-lg shadow-gray-500/10 dark:shadow-gray-500/5 p-5 transition-all duration-300 hover:shadow-xl"
        >
          <div class="flex items-center">
            <div :class="card.bgColor" class="rounded-lg p-3">
              <component :is="card.icon" :class="card.iconColor" class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ card.title }}
              </dt>
              <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                {{ card.value }}
              </dd>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent PKS Submissions -->
        <div class="lg:col-span-2">
          <div class="glass-card bg-white/40 dark:bg-black/30 backdrop-blur-[25px] border border-white/20 dark:border-white/10 rounded-2xl shadow-lg shadow-gray-500/10 dark:shadow-gray-500/5 overflow-hidden">
            <div class="px-6 py-5 border-b border-white/20 dark:border-white/10">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                  Pengajuan PKS Terbaru
                </h3>
                <button 
                  @click="openCreatePksModal"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                  <Plus class="h-4 w-4 mr-1" />
                  Buat Pengajuan
                </button>
              </div>
            </div>
            <div class="p-6">
              <div v-if="submissions?.data?.length > 0" class="overflow-hidden rounded-lg">
                <ul class="divide-y divide-white/20 dark:divide-white/10">
                  <li v-for="submission in submissions.data" :key="submission.id">
                    <div class="block hover:bg-white/20 dark:hover:bg-white/10 transition-colors duration-150 rounded-lg px-4 py-4">
                      <Link 
                        :href="route('mitra.pks.show', submission.id)" 
                        class="block"
                      >
                        <div class="flex items-center justify-between">
                          <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                              {{ submission.title }}
                            </p>
                          </div>
                          <div class="ml-4 flex-shrink-0">
                            <StatusBadge :status="submission.status" />
                          </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                          <div class="sm:flex">
                            <p class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                              <User class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-500 dark:text-gray-400" />
                              {{ submission.pks_tujuan || 'Tujuan tidak ditentukan' }}
                            </p>
                          </div>
                          <div class="mt-2 flex items-center text-sm text-gray-700 dark:text-gray-300 sm:mt-0">
                            <Calendar class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-500 dark:text-gray-400" />
                            <p>
                              {{ formatDate(submission.created_at) }}
                            </p>
                          </div>
                        </div>
                      </Link>
                      <!-- Actions for 'proses' status submissions -->
                      <div v-if="submission.status === 'proses'" class="mt-2 flex justify-end gap-2">
                        <button
                          @click="openEditPksModal(submission)"
                          class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-900/30 dark:text-yellow-300 dark:hover:bg-yellow-800/50 transition-colors duration-200"
                        >
                          <Edit3 class="h-3 w-3 mr-1" />
                          Edit
                        </button>
                        <button
                          @click="deleteSubmission(submission)"
                          class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-800/50 transition-colors duration-200"
                        >
                          <Trash2 class="h-3 w-3 mr-1" />
                          Hapus
                        </button>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              <div v-else class="text-center py-12">
                <FileText class="mx-auto h-12 w-12 text-gray-500 dark:text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada pengajuan</h3>
                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                  Anda belum membuat pengajuan PKS apa pun.
                </p>
                <div class="mt-6">
                  <button
                    @click="openCreatePksModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                  >
                    <Plus class="h-5 w-5 mr-2" />
                    Buat Pengajuan PKS Pertama
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Active PKS Section -->
          <div class="glass-card bg-white/40 dark:bg-black/30 backdrop-blur-[25px] border border-white/20 dark:border-white/10 rounded-2xl shadow-lg shadow-gray-500/10 dark:shadow-gray-500/5 overflow-hidden mt-8">
            <div class="px-6 py-5 border-b border-white/20 dark:border-white/10">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                  PKS yang Sedang Berjalan
                </h3>
              </div>
            </div>
            <div class="p-6">
              <div v-if="activeSubmissions?.length > 0" class="overflow-hidden rounded-lg">
                <ul class="divide-y divide-white/20 dark:divide-white/10">
                  <li v-for="submission in activeSubmissions" :key="submission.id">
                    <div class="block hover:bg-white/20 dark:hover:bg-white/10 transition-colors duration-150 rounded-lg px-4 py-4">
                      <Link 
                        :href="route('mitra.pks.show', submission.id)" 
                        class="block"
                      >
                        <div class="flex items-center justify-between">
                          <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                              {{ submission.title }}
                            </p>
                          </div>
                          <div class="ml-4 flex-shrink-0">
                            <StatusBadge :status="submission.status" />
                          </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                          <div class="sm:flex">
                            <p class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                              <User class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-500 dark:text-gray-400" />
                              {{ submission.pks_tujuan || 'Tujuan tidak ditentukan' }}
                            </p>
                          </div>
                          <div class="mt-2 flex items-center text-sm text-gray-700 dark:text-gray-300 sm:mt-0">
                            <Calendar class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-500 dark:text-gray-400" />
                            <p>
                              {{ formatDate(submission.created_at) }}
                            </p>
                          </div>
                        </div>
                        <div v-if="submission.validity_period_start && submission.validity_period_end" class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                          Berlaku: {{ formatDate(submission.validity_period_start) }} - {{ formatDate(submission.validity_period_end) }}
                        </div>
                      </Link>
                    </div>
                  </li>
                </ul>
              </div>
              <div v-else class="text-center py-12">
                <FileText class="mx-auto h-12 w-12 text-gray-500 dark:text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada PKS aktif</h3>
                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                  Anda tidak memiliki PKS yang sedang berjalan.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-8">
          <!-- Upcoming Meetings -->
          <div class="glass-card bg-white/40 dark:bg-black/30 backdrop-blur-[25px] border border-white/20 dark:border-white/10 rounded-2xl shadow-lg shadow-gray-500/10 dark:shadow-gray-500/5 overflow-hidden">
            <div class="px-6 py-5 border-b border-white/20 dark:border-white/10">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Jadwal Rapat Mendatang
              </h3>
            </div>
            <div class="p-6">
              <div v-if="upcomingMeetings?.length > 0" class="flow-root">
                <ul class="divide-y divide-white/20 dark:divide-white/10">
                  <li 
                    v-for="meeting in upcomingMeetings" 
                    :key="meeting.id"
                    class="py-4 cursor-pointer hover:bg-white/20 dark:hover:bg-white/10 rounded-lg px-2 transition-colors duration-150"
                    @click="openMeetingDetail(meeting)"
                  >
                    <div class="flex items-center space-x-4">
                      <div class="flex-shrink-0">
                        <Calendar class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                          {{ meeting.judul }}
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                          {{ formatDate(meeting.tanggal_waktu) }}
                        </p>
                      </div>
                      <div>
                        <span 
                          :class="getMeetingStatusClass(meeting.status)"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        >
                          {{ getMeetingStatusText(meeting.status) }}
                        </span>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              <div v-else class="text-center py-8">
                <Calendar class="mx-auto h-12 w-12 text-gray-500 dark:text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada rapat terjadwal</h3>
                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                  Tidak ada rapat yang dijadwalkan dalam waktu dekat.
                </p>
              </div>
            </div>
          </div>

          <!-- Document Templates -->
          <div class="glass-card bg-white/40 dark:bg-black/30 backdrop-blur-[25px] border border-white/20 dark:border-white/10 rounded-2xl shadow-lg shadow-gray-500/10 dark:shadow-gray-500/5 overflow-hidden">
            <div class="px-6 py-5 border-b border-white/20 dark:border-white/10">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Template Dokumen
              </h3>
            </div>
            <div class="p-6 space-y-4">
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Unduh template berikut untuk keperluan pengajuan PKS.
              </p>
              
              <!-- Template MOU -->
              <div class="flex items-center justify-between p-3 bg-white/20 dark:bg-white/5 rounded-lg border border-white/10 transition-colors hover:bg-white/30 dark:hover:bg-white/10">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                    <FileText class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Template MOU</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Format .docx</p>
                  </div>
                </div>
                <a 
                  href="/templates/Template MOU.docx" 
                  download
                  class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors bg-white/50 dark:bg-black/20 rounded-full hover:bg-white dark:hover:bg-black/40"
                  title="Unduh Template MOU"
                >
                  <Download class="h-5 w-5" />
                </a>
              </div>

              <!-- Template PKS -->
              <div class="flex items-center justify-between p-3 bg-white/20 dark:bg-white/5 rounded-lg border border-white/10 transition-colors hover:bg-white/30 dark:hover:bg-white/10">
                <div class="flex items-center">
                  <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg mr-3">
                    <FileSignature class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Template PKS</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Format .docx</p>
                  </div>
                </div>
                <a 
                  href="/templates/Template PKS.docx" 
                  download
                  class="p-2 text-gray-500 hover:text-purple-600 dark:text-gray-400 dark:hover:text-purple-400 transition-colors bg-white/50 dark:bg-black/20 rounded-full hover:bg-white dark:hover:bg-black/40"
                  title="Unduh Template PKS"
                >
                  <Download class="h-5 w-5" />
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Meeting Detail Modal -->
  <Modal :show="showMeetingDetail" @close="closeMeetingDetail" max-width="md">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
            Detail Rapat
          </h3>
          <button @click="closeMeetingDetail" class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
            <X class="h-5 w-5" />
          </button>
        </div>
      </div>
      <div class="px-6 py-4">
        <div v-if="selectedMeeting" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Rapat</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ selectedMeeting.judul }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal & Waktu</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(selectedMeeting.tanggal_waktu) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi/Link Meeting</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ selectedMeeting.lokasi || 'Tidak ditentukan' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <span 
              :class="getMeetingStatusClass(selectedMeeting.status)"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
            >
              {{ getMeetingStatusText(selectedMeeting.status) }}
            </span>
          </div>
          <div v-if="selectedMeeting.deskripsi">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ selectedMeeting.deskripsi }}</p>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex justify-end">
        <button
          @click="closeMeetingDetail"
          type="button"
          class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors duration-200 text-sm dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200"
        >
          Tutup
        </button>
      </div>
    </div>
  </Modal>

  <!-- Create PKS Submission Modal -->
  <CreatePksSubmissionModal 
    :is-open="showCreatePksModal" 
    @close="closeCreatePksModal" 
  />
  
  <!-- Edit PKS Content Modal -->
  <EditPksContentModal 
    :show="showEditPksModal" 
    :submission="submissionToEdit" 
    @close="closeEditPksModal" 
    @success="handleEditSuccess"
  />
  

  
  <!-- Delete Confirmation Modal -->
  <Modal :show="showDeleteConfirmation" @close="cancelDelete">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
          Konfirmasi Hapus Pengajuan
        </h3>
      </div>
      <div class="px-6 py-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30">
              <Trash2 class="h-6 w-6 text-red-600 dark:text-red-400" />
            </div>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              Hapus Pengajuan PKS?
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Apakah Anda yakin ingin menghapus pengajuan PKS 
                <span class="font-medium text-gray-900 dark:text-white">
                  "{{ submissionToDelete?.title }}"
                </span>? 
                Tindakan ini tidak dapat dibatalkan.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
        <button
          @click="cancelDelete"
          type="button"
          class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        >
          Batal
        </button>
        <button
          @click="confirmDelete"
          type="button"
          class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 active:bg-red-700 transition ease-in-out duration-150"
        >
          Hapus
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import Vue3Avatar from 'vue3-avatar'

// Icons
import { 
  FileText, 
  Calendar, 
  Bell, 
  Plus, 
  User, 
  FilePlus, 
  CheckCircle, 
  X, 
  BadgeCheck, 
  Clock, 
  MapPin, 
  Text, 
  Download, 
  FileSignature,
  ChevronDown,
  Settings,
  LogOut,
  Trash2
} from 'lucide-vue-next'

// Components
import StatusBadge from '@/Components/StatusBadge.vue'
import CreatePksSubmissionModal from '@/Components/mitra/CreatePksSubmissionModal.vue'
import EditPksContentModal from '@/Components/admin/EditPksContentModal.vue'
import { Edit3 } from 'lucide-vue-next'


// Props
const props = defineProps({
  submissions: Object,
  statistics: Object,
  notifications: Array,
  upcomingMeetings: Array
})

// Get user data from page props
const pageProps = usePage().props
const user = computed(() => {
  return pageProps.auth?.user || { name: 'User', profile_picture: null }
})

// Computed properties for username and profile picture
const username = computed(() => {
  return user.value?.name || 'User'
})

const profilePicture = computed(() => {
  return user.value?.profile_picture || null
})

// Reactive data
const showMeetingDetail = ref(false)
const selectedMeeting = ref(null)
const showCreatePksModal = ref(false)
const showEditPksModal = ref(false)
const submissionToEdit = ref(null)

const dropdownOpen = ref(false)
const showDeleteConfirmation = ref(false)
const submissionToDelete = ref(null)

// Computed properties
const infoCards = computed(() => [
  {
    id: 'total',
    title: 'Total Pengajuan',
    value: props.statistics?.total || 0,
    icon: FileText,
    bgColor: 'bg-blue-100 dark:bg-blue-900/30',
    iconColor: 'text-blue-600 dark:text-blue-400'
  },
  {
    id: 'approved',
    title: 'Disetujui',
    value: props.statistics?.approved || 0,
    icon: BadgeCheck,
    bgColor: 'bg-green-100 dark:bg-green-900/30',
    iconColor: 'text-green-600 dark:text-green-400'
  },
  {
    id: 'pending',
    title: 'Proses',
    value: props.statistics?.pending || 0,
    icon: Clock,
    bgColor: 'bg-yellow-100 dark:bg-yellow-900/30',
    iconColor: 'text-yellow-600 dark:text-yellow-400'
  },
  {
    id: 'rejected',
    title: 'Ditolak',
    value: props.statistics?.rejected || 0,
    icon: X,
    bgColor: 'bg-red-100 dark:bg-red-900/30',
    iconColor: 'text-red-600 dark:text-red-400'
  }
])

// Filter active submissions (disetujui and within validity period)
const activeSubmissions = computed(() => {
  if (!props.submissions?.data) return []
  
  return props.submissions.data.filter(submission => 
    submission.status === 'disetujui' && 
    submission.validity_period_start && 
    submission.validity_period_end &&
    new Date(submission.validity_period_end) > new Date() &&
    new Date(submission.validity_period_start) <= new Date()
  )
})

const openMeetingDetail = (meeting) => {
  selectedMeeting.value = meeting
  showMeetingDetail.value = true
}

const closeMeetingDetail = () => {
  showMeetingDetail.value = false
  selectedMeeting.value = null
}

const openCreatePksModal = () => {
  showCreatePksModal.value = true
}

const closeCreatePksModal = () => {
  showCreatePksModal.value = false
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const getMeetingStatusClass = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'selesai':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    case 'dibatalkan':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

const getMeetingStatusText = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'Akan Datang'
    case 'selesai':
      return 'Selesai'
    case 'dibatalkan':
      return 'Dibatalkan'
    default:
      return status
  }
}

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const goToSettings = () => {
  router.visit(route('settings'))
}

const logout = () => {
  router.post(route('logout'))
}

const deleteSubmission = (submission) => {
  submissionToDelete.value = submission
  showDeleteConfirmation.value = true
}

const confirmDelete = () => {
  if (submissionToDelete.value) {
    router.delete(route('pks.destroy', submissionToDelete.value.id), {
      onSuccess: () => {
        router.reload({ only: ['submissions', 'statistics'] })
        showDeleteConfirmation.value = false
        submissionToDelete.value = null
      }
    })
  }
}

const cancelDelete = () => {
  showDeleteConfirmation.value = false
  submissionToDelete.value = null
}

const openEditPksModal = (submission) => {
  submissionToEdit.value = submission
  showEditPksModal.value = true
}

const closeEditPksModal = () => {
  showEditPksModal.value = false
  setTimeout(() => {
    submissionToEdit.value = null
  }, 200) // Small delay to prevent content jump during transition
}

const handleEditSuccess = () => {
  router.reload({ only: ['submissions'] })
  closeEditPksModal()
}



// Fetch notifications periodically
onMounted(() => {
  // Fetch notifications every 30 seconds
  const interval = setInterval(() => {
    router.reload({ only: ['notifications'] })
  }, 30000)
  
  // Clear interval when component is unmounted
  return () => clearInterval(interval)
})
</script>

<style scoped>
.glass-card {
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
}

.glass-icon {
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}
</style>