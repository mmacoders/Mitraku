<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ProfileUpdateModal from '@/Components/ProfileUpdateModal.vue';
import Vue3Avatar from 'vue3-avatar';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user;

// Check if user is admin
const isAdmin = computed(() => user.role === 'admin');

// Form for profile information
const profileForm = useForm({
    name: user.name,
    email: user.email,
});

// Form for password update
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Modal notification
const showModal = ref(false);
const modalType = ref<'success' | 'error'>('success');
const modalTitle = ref('');
const modalMessage = ref('');

const showNotification = (title: string, message: string, type: 'success' | 'error' = 'success') => {
    modalTitle.value = title;
    modalMessage.value = message;
    modalType.value = type;
    showModal.value = true;
};

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        onSuccess: () => {
            showNotification('Berhasil!', 'Profil berhasil diperbarui!');
        },
        onError: () => {
            showNotification('Gagal!', 'Gagal memperbarui profil.', 'error');
        }
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showNotification('Berhasil!', 'Kata sandi berhasil diperbarui!');
        },
        onError: () => {
            showNotification('Gagal!', 'Gagal memperbarui kata sandi.', 'error');
        }
    });
};
</script>

<template>
    <AdminLayout v-if="isAdmin">
        <div class="py-6 sm:py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transition-all duration-300 ease-in-out border border-gray-100 dark:border-gray-800">
                    <div class="flex flex-col sm:flex-row items-center">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/40 dark:to-purple-900/40 flex items-center justify-center shadow-lg border-4 border-white dark:border-gray-800">
                                <Vue3Avatar 
                                    :name="user.name" 
                                    :size="100"
                                    class="rounded-full"
                                />
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-2 text-lg">{{ user.email }}</p>
                            <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200">
                                {{ user.role === 'admin' ? 'Administrator' : user.role === 'mitra' ? 'Mitra' : 'User' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Informasi Pribadi Card -->
                    <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6 border border-gray-100 dark:border-gray-800">
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Informasi Pribadi
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola informasi pribadi Anda</p>
                        </div>

                        <form @submit.prevent="updateProfile" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': profileForm.errors.name }"
                                />
                                <p v-if="profileForm.errors.name" class="text-rose-500 text-xs mt-2">{{ profileForm.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input
                                    v-model="profileForm.email"
                                    type="email"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': profileForm.errors.email }"
                                />
                                <p v-if="profileForm.errors.email" class="text-rose-500 text-xs mt-2">{{ profileForm.errors.email }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="profileForm.processing"
                                class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-70"
                            >
                                <span v-if="profileForm.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </form>
                    </div>

                    <!-- Keamanan Akun Card -->
                    <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6 border border-gray-100 dark:border-gray-800">
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                Keamanan Akun
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ubah kata sandi Anda</p>
                        </div>

                        <form @submit.prevent="updatePassword" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Saat Ini</label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': passwordForm.errors.current_password }"
                                />
                                <p v-if="passwordForm.errors.current_password" class="text-rose-500 text-xs mt-2">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Baru</label>
                                <input
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': passwordForm.errors.password }"
                                />
                                <p v-if="passwordForm.errors.password" class="text-rose-500 text-xs mt-2">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Kata Sandi</label>
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': passwordForm.errors.password_confirmation }"
                                />
                                <p v-if="passwordForm.errors.password_confirmation" class="text-rose-500 text-xs mt-2">{{ passwordForm.errors.password_confirmation }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="w-full py-3 px-4 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-70"
                            >
                                <span v-if="passwordForm.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memperbarui...
                                </span>
                                <span v-else>Perbarui Kata Sandi</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
    
    <AuthenticatedLayout v-else>
        <div class="py-6 sm:py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transition-all duration-300 ease-in-out border border-gray-100 dark:border-gray-800">
                    <div class="flex flex-col sm:flex-row items-center">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/40 dark:to-purple-900/40 flex items-center justify-center shadow-lg border-4 border-white dark:border-gray-800">
                                <Vue3Avatar 
                                    :name="user.name" 
                                    :size="100"
                                    class="rounded-full"
                                />
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-2 text-lg">{{ user.email }}</p>
                            <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200">
                                {{ user.role === 'admin' ? 'Administrator' : user.role === 'mitra' ? 'Mitra' : 'User' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Informasi Pribadi Card -->
                    <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6 border border-gray-100 dark:border-gray-800">
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Informasi Pribadi
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola informasi pribadi Anda</p>
                        </div>

                        <form @submit.prevent="updateProfile" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': profileForm.errors.name }"
                                />
                                <p v-if="profileForm.errors.name" class="text-rose-500 text-xs mt-2">{{ profileForm.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input
                                    v-model="profileForm.email"
                                    type="email"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': profileForm.errors.email }"
                                />
                                <p v-if="profileForm.errors.email" class="text-rose-500 text-xs mt-2">{{ profileForm.errors.email }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="profileForm.processing"
                                class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-70"
                            >
                                <span v-if="profileForm.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </form>
                    </div>

                    <!-- Keamanan Akun Card -->
                    <div class="bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6 border border-gray-100 dark:border-gray-800">
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                Keamanan Akun
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ubah kata sandi Anda</p>
                        </div>

                        <form @submit.prevent="updatePassword" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Saat Ini</label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': passwordForm.errors.current_password }"
                                />
                                <p v-if="passwordForm.errors.current_password" class="text-rose-500 text-xs mt-2">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Baru</label>
                                <input
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': passwordForm.errors.password }"
                                />
                                <p v-if="passwordForm.errors.password" class="text-rose-500 text-xs mt-2">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Kata Sandi</label>
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-500': passwordForm.errors.password_confirmation }"
                                />
                                <p v-if="passwordForm.errors.password_confirmation" class="text-rose-500 text-xs mt-2">{{ passwordForm.errors.password_confirmation }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="w-full py-3 px-4 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-70"
                            >
                                <span v-if="passwordForm.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memperbarui...
                                </span>
                                <span v-else>Perbarui Kata Sandi</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    
    <!-- Profile Update Modal -->
    <ProfileUpdateModal
        :show="showModal"
        :type="modalType"
        :title="modalTitle"
        :message="modalMessage"
        @close="showModal = false"
    />
</template>