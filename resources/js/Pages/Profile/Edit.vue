<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Camera, User as UserIcon, Mail, Phone, Building, Lock, Save } from 'lucide-vue-next';
import type { User } from '@/types';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user as User;

// Check if user is mitra
const isMitra = computed(() => user.role === 'mitra');

// Forms for profile information and password update
const profileForm = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    company: user.company || '',
    profile_pict: null as File | null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Avatar state
const avatarPreview = ref<string | null>(null);
const avatarFile = ref<File | null>(null);

// Handle avatar file selection
const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        avatarFile.value = file;
        profileForm.profile_pict = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

// Submit profile update
const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        forceFormData: true,
        onSuccess: () => {
            // Reset avatar state after successful update
            avatarFile.value = null;
            avatarPreview.value = null;
        },
        onError: () => {
            console.error('Failed to update profile');
        }
    });
};

// Submit password update
const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
        onError: () => {
            console.error('Failed to update password');
        }
    });
};
</script>

<template>
    <Head title="Edit Profil Anda" />
    
    <AdminLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4 sm:px-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Profile Information Section -->
                    <div class="lg:w-1/2">
                        <div class="bg-white/80 dark:bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 dark:border-white/10 overflow-hidden transition-all duration-300 h-full">
                            <div class="p-6 md:p-8">
                                <!-- Header -->
                                <div class="mb-8">
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profil Anda</h1>
                                    <p class="mt-1 text-gray-600 dark:text-gray-400">Perbarui informasi akun dan identitas Anda.</p>
                                </div>
                                
                                <!-- Avatar Section -->
                                <div class="flex flex-col items-center mb-8">
                                    <div class="relative">
                                        <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg">
                                            <img 
                                                v-if="avatarPreview" 
                                                :src="avatarPreview" 
                                                alt="Preview" 
                                                class="w-full h-full object-cover"
                                            />
                                            <img 
                                                v-else-if="user.profile_pict" 
                                                :src="`/storage/${user.profile_pict}`" 
                                                alt="Profile" 
                                                class="w-full h-full object-cover"
                                            />
                                            <UserIcon v-else class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                                        </div>
                                        <label 
                                            for="avatar-upload" 
                                            class="absolute bottom-0 right-0 bg-white dark:bg-gray-800 rounded-full p-2 shadow-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-700"
                                        >
                                            <Camera class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                                        </label>
                                        <input 
                                            id="avatar-upload" 
                                            type="file" 
                                            class="hidden" 
                                            accept="image/*" 
                                            @change="handleAvatarChange"
                                        />
                                    </div>
                                    <button 
                                        v-if="avatarPreview || user.profile_pict"
                                        type="button" 
                                        class="mt-3 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200"
                                        @click="() => { avatarPreview = null; avatarFile = null; profileForm.profile_pict = null; }"
                                    >
                                        Hapus foto
                                    </button>
                                </div>
                                
                                <!-- Profile Information Form -->
                                <form @submit.prevent="updateProfile" class="space-y-6">
                                    <div class="space-y-6">
                                        <!-- Name Field -->
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Nama Lengkap
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <UserIcon class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <input
                                                    id="name"
                                                    v-model="profileForm.name"
                                                    type="text"
                                                    required
                                                    class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                    :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.name }"
                                                />
                                            </div>
                                            <div v-if="profileForm.errors.name" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                                <span>⚠️ {{ profileForm.errors.name }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Email Field -->
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Email
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <Mail class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <input
                                                    id="email"
                                                    v-model="profileForm.email"
                                                    type="email"
                                                    required
                                                    class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                    :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.email }"
                                                />
                                            </div>
                                            <div v-if="profileForm.errors.email" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                                <span>⚠️ {{ profileForm.errors.email }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Phone Field -->
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Nomor Telepon
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <Phone class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <input
                                                    id="phone"
                                                    v-model="profileForm.phone"
                                                    type="tel"
                                                    class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                    :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.phone }"
                                                />
                                            </div>
                                            <div v-if="profileForm.errors.phone" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                                <span>⚠️ {{ profileForm.errors.phone }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Company Field (Mitra only) -->
                                        <div v-if="isMitra">
                                            <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Alamat / Instansi
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <Building class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <input
                                                    id="company"
                                                    v-model="profileForm.company"
                                                    type="text"
                                                    class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                    :class="{ 'border-red-500 focus:ring-red-500': profileForm.errors.company }"
                                                />
                                            </div>
                                            <div v-if="profileForm.errors.company" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                                <span>⚠️ {{ profileForm.errors.company }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Profile Form Actions -->
                                    <div class="flex justify-end pt-4">
                                        <button
                                            type="submit"
                                            :disabled="profileForm.processing"
                                            class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-70"
                                        >
                                            <Save class="w-4 h-4 inline mr-2" />
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Update Section -->
                    <div class="lg:w-1/2">
                        <div class="bg-white/80 dark:bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 dark:border-white/10 overflow-hidden transition-all duration-300 h-full">
                            <div class="p-6 md:p-8">
                                <div class="mb-6">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ubah Password</h2>
                                    <p class="mt-1 text-gray-600 dark:text-gray-400">Perbarui password Anda secara berkala.</p>
                                </div>
                                
                                <form @submit.prevent="updatePassword" class="space-y-6">
                                    <!-- Current Password -->
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Password Saat Ini
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <Lock class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input
                                                id="current_password"
                                                v-model="passwordForm.current_password"
                                                type="password"
                                                required
                                                class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                :class="{ 'border-red-500 focus:ring-red-500': passwordForm.errors.current_password }"
                                            />
                                        </div>
                                        <div v-if="passwordForm.errors.current_password" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                            <span>⚠️ {{ passwordForm.errors.current_password }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- New Password -->
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Password Baru
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <Lock class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input
                                                id="password"
                                                v-model="passwordForm.password"
                                                type="password"
                                                required
                                                class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                :class="{ 'border-red-500 focus:ring-red-500': passwordForm.errors.password }"
                                            />
                                        </div>
                                        <div v-if="passwordForm.errors.password" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                            <span>⚠️ {{ passwordForm.errors.password }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Konfirmasi Password
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <Lock class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input
                                                id="password_confirmation"
                                                v-model="passwordForm.password_confirmation"
                                                type="password"
                                                required
                                                class="block w-full pl-10 pr-3 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                :class="{ 'border-red-500 focus:ring-red-500': passwordForm.errors.password_confirmation }"
                                            />
                                        </div>
                                        <div v-if="passwordForm.errors.password_confirmation" class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400">
                                            <span>⚠️ {{ passwordForm.errors.password_confirmation }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Password Form Actions -->
                                    <div class="flex justify-end pt-4">
                                        <button
                                            type="submit"
                                            :disabled="passwordForm.processing"
                                            class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-70"
                                        >
                                            <Save class="w-4 h-4 inline mr-2" />
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>