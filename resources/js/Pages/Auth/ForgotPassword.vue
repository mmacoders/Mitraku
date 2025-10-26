<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Mail, Lock, ArrowLeft, AlertCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-100 dark:from-gray-900 dark:via-purple-900 dark:to-indigo-900 p-4 relative overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 w-40 h-40 rounded-full bg-blue-300 opacity-20 dark:bg-blue-600"></div>
            <div class="absolute top-1/3 right-20 w-32 h-32 rounded-full bg-purple-300 opacity-20 dark:bg-purple-600"></div>
            <div class="absolute bottom-20 left-1/3 w-24 h-24 rounded-full bg-indigo-300 opacity-20 dark:bg-indigo-600"></div>
        </div>
        
        <div class="w-full max-w-md">
            <!-- Animated form container with enhanced glassmorphism -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl p-6 transition-all duration-300 hover:shadow-2xl border border-white/30 dark:border-gray-700/50">
                <!-- Header with enhanced styling -->
                <div class="text-center mb-6">
                    <div class="mx-auto w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center mb-4">
                        <Lock class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                        Lupa Password?
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password Anda.
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mb-6 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-200 dark:border-green-800/30"
                >
                    {{ status }}
                </div>

                <!-- Error message for invalid credentials -->
                <div
                    v-if="form.errors.email && form.errors.email.includes('tidak ditemukan')"
                    class="mb-6 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-200 dark:border-red-800/30 flex items-start"
                >
                    <AlertCircle class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" />
                    <div>
                        <p class="font-medium">Akun tidak ditemukan</p>
                        <p class="font-normal mt-1">Email "{{ form.email }}" tidak terdaftar dalam sistem kami. Silakan periksa kembali email Anda atau daftar akun baru.</p>
                    </div>
                </div>

                <!-- Error message for wrong password -->
                <div
                    v-else-if="form.errors.email && form.errors.email.includes('password')"
                    class="mb-6 text-sm font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800/30 flex items-start"
                >
                    <AlertCircle class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" />
                    <div>
                        <p class="font-medium">Password salah</p>
                        <p class="font-normal mt-1">Email ditemukan tetapi password yang dimasukkan salah. Pastikan Anda memasukkan password yang benar atau gunakan fitur lupa password.</p>
                    </div>
                </div>

                <!-- Generic error message -->
                <div
                    v-else-if="form.errors.email"
                    class="mb-6 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-200 dark:border-red-800/30 flex items-start"
                >
                    <AlertCircle class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" />
                    <div>
                        <p class="font-medium">Validasi gagal</p>
                        <p class="font-normal mt-1">{{ form.errors.email }}</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email field with enhanced styling -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="pl-10 pr-4 py-3 w-full rounded-xl border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/70 dark:bg-gray-700/70 text-gray-900 dark:text-white transition-all duration-300 text-sm shadow-sm focus:shadow-md"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="masukkan@email.com"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                            />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Kami akan mengirimkan tautan reset password ke email ini
                        </p>
                        <InputError class="mt-1 text-xs" :message="form.errors.email" />
                    </div>

                    <!-- Submit Button with enhanced styling -->
                    <div class="pt-2">
                        <PrimaryButton
                            class="w-full py-3 px-4 rounded-xl text-white font-semibold shadow-lg bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl text-sm"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim Tautan...
                            </span>
                            <span v-else class="flex items-center justify-center">
                                <Mail class="h-4 w-4 mr-2" />
                                Kirim Tautan Reset Password
                            </span>
                        </PrimaryButton>
                    </div>

                    <!-- Enhanced link to Login -->
                    <div class="flex items-center justify-center pt-2">
                        <a
                            :href="route('login')"
                            class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-300 group"
                        >
                            <ArrowLeft class="h-4 w-4 mr-1 group-hover:-translate-x-1 transition-transform duration-200" />
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Footer note -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    © 2025 SI-Huyula. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>