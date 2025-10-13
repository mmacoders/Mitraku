<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user;

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

// Toast notification
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

const showNotification = (message: string, type: 'success' | 'error' = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
    
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        onSuccess: () => {
            showNotification('Profil berhasil diperbarui!');
        },
        onError: () => {
            showNotification('Gagal memperbarui profil.', 'error');
        }
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showNotification('Kata sandi berhasil diperbarui!');
        },
        onError: () => {
            showNotification('Gagal memperbarui kata sandi.', 'error');
        }
    });
};

// Logout function
const logout = () => {
    // This would typically use Inertia.post(route('logout'))
    // but I'll leave it as a placeholder since we're focusing on the UI
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-6 sm:py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur-sm rounded-2xl shadow-sm p-6 mb-8 transition-all duration-300 ease-in-out">
                    <div class="flex flex-col sm:flex-row items-center">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center shadow-md border-4 border-white dark:border-gray-800">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <button class="absolute bottom-0 right-0 bg-white dark:bg-gray-800 rounded-full p-1.5 shadow-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 ease-in-out">
                                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ user.name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ user.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Informasi Pribadi Card -->
                    <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 ease-in-out p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">🟢 Informasi Pribadi</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola informasi pribadi Anda</p>
                        </div>

                        <form @submit.prevent="updateProfile" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-400 transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-400': profileForm.errors.name }"
                                />
                                <p v-if="profileForm.errors.name" class="text-rose-400 text-xs mt-1">{{ profileForm.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input
                                    v-model="profileForm.email"
                                    type="email"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-400 transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-400': profileForm.errors.email }"
                                />
                                <p v-if="profileForm.errors.email" class="text-rose-400 text-xs mt-1">{{ profileForm.errors.email }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="profileForm.processing"
                                class="w-full py-2.5 px-4 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                <span v-if="profileForm.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </form>
                    </div>

                    <!-- Keamanan Akun Card -->
                    <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 ease-in-out p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">🔒 Keamanan Akun</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ubah kata sandi Anda</p>
                        </div>

                        <form @submit.prevent="updatePassword" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Saat Ini</label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-400 transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-400': passwordForm.errors.current_password }"
                                />
                                <p v-if="passwordForm.errors.current_password" class="text-rose-400 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Baru</label>
                                <input
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-400 transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-400': passwordForm.errors.password }"
                                />
                                <p v-if="passwordForm.errors.password" class="text-rose-400 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Kata Sandi</label>
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-400 transition-all duration-300 ease-in-out"
                                    :class="{ 'border-rose-300 focus:ring-rose-400': passwordForm.errors.password_confirmation }"
                                />
                                <p v-if="passwordForm.errors.password_confirmation" class="text-rose-400 text-xs mt-1">{{ passwordForm.errors.password_confirmation }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="w-full py-2.5 px-4 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                <span v-if="passwordForm.processing">Memperbarui...</span>
                                <span v-else>Perbarui Kata Sandi</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>