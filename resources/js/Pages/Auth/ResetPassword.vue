<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Lock, Mail, Key, Eye, EyeOff, CheckCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Reset Password" />

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
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center mb-4 shadow-lg shadow-green-500/30">
                        <Lock class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                        Buat Password Baru
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Silakan masukkan password baru yang aman untuk akun Anda.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email Field (Readonlyish) -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="pl-10 pr-4 py-3 w-full rounded-xl border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm cursor-not-allowed"
                                v-model="form.email"
                                required
                                readonly
                            />
                        </div>
                        <InputError class="mt-1 text-xs" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Key class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="pl-10 pr-10 py-3 w-full rounded-xl border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/70 dark:bg-gray-700/70 text-gray-900 dark:text-white transition-all duration-300 text-sm shadow-sm focus:shadow-md"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.password }"
                            />
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                            >
                                <Eye v-if="!showPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError class="mt-1 text-xs" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <CheckCircle class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="pl-10 pr-10 py-3 w-full rounded-xl border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/70 dark:bg-gray-700/70 text-gray-900 dark:text-white transition-all duration-300 text-sm shadow-sm focus:shadow-md"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password baru"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.password_confirmation }"
                            />
                            <button 
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                            >
                                <Eye v-if="!showConfirmPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError class="mt-1 text-xs" :message="form.errors.password_confirmation" />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
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
                                Memproses...
                            </span>
                            <span v-else>
                                Reset Password
                            </span>
                        </PrimaryButton>
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
