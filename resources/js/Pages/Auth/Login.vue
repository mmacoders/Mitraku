<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { AlertCircle } from 'lucide-vue-next';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-100 dark:from-gray-900 dark:via-purple-900 dark:to-indigo-900 relative overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 w-40 h-40 rounded-full bg-blue-300 opacity-20 dark:bg-blue-600"></div>
            <div class="absolute top-1/3 right-20 w-32 h-32 rounded-full bg-purple-300 opacity-20 dark:bg-purple-600"></div>
            <div class="absolute bottom-20 left-1/3 w-24 h-24 rounded-full bg-indigo-300 opacity-20 dark:bg-indigo-600"></div>
        </div>

        <!-- Desktop split layout - hidden on mobile -->
        <div class="hidden md:flex min-h-screen ">
            <!-- Bagian kiri (gambar ilustrasi) -->
            <div class="flex w-1/2 items-center justify-center p-8">
                <img src="assets/login.png" alt="Login Illustration" class="max-w-md w-full h-auto rounded-lg" />
            </div>
            <div class="w-1/2 flex items-center justify-start">
                <div class="w-full max-w-md ml-8 mr-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 backdrop-blur-sm bg-opacity-90 dark:bg-opacity-90 transition-all duration-300">
                        <div class="text-center mb-8">
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Selamat Datang</h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-2">Silahkan Login ke Akun anda</p>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="pl-10 pr-4 py-3 w-full border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                                    />
                                    <label for="email" class="absolute -top-2 left-10 bg-white dark:bg-gray-800 px-1 text-xs text-gray-500 dark:text-gray-400 transition-all duration-200">Email</label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mb-6">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="password"
                                        type="password"
                                        class="pl-10 pr-4 py-3 w-full border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        v-model="form.password"
                                        required
                                        autocomplete="current-password"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.password }"
                                    />
                                    <label for="password" class="absolute -top-2 left-10 bg-white dark:bg-gray-800 px-1 text-xs text-gray-500 dark:text-gray-400 transition-all duration-200">Password</label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <!-- Link ke Register -->
                                <Link
                                    :href="route('register')"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline"
                                >
                                    Belum punya akun? Daftar
                                </Link>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline"
                                >
                                    Lupa Password?
                                </Link>
                            </div>

                            <PrimaryButton
                                class="w-full py-3 px-4 text-white font-medium rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform transition-all duration-200 hover:scale-102 hover:shadow-lg"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Log in
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile full layout - hidden on desktop -->
        <div class="md:hidden flex items-center justify-center w-full px-4 py-8 min-h-screen">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 backdrop-blur-sm bg-opacity-90 dark:bg-opacity-90 transition-all duration-300">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Selamat Datang</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">Silahkan Login ke Akun anda</p>
                    </div>

                    <!-- Error message for invalid credentials -->

                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="pl-10 pr-4 py-3 w-full border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Massukkan Email Anda"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.email }"
                                />
                                <label for="email" 
                                class="absolute -top-2 left-10 bg-white dark:bg-gray-800 px-1 text-xs text-gray-500 dark:text-gray-400 transition-all duration-200">
                                Email
                            </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mb-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="pl-10 pr-4 py-3 w-full border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    :class="{ 'border-red-500 focus:ring-red-500': form.errors.password }"
                                />
                                <label for="password" class="absolute -top-2 left-10 bg-white dark:bg-gray-800 px-1 text-xs text-gray-500 dark:text-gray-400 transition-all duration-200">Password</label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center cursor-pointer">
                                <Checkbox name="remember" v-model:checked="form.remember" class="rounded-full" />
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline"
                            >
                                Lupa Password?
                            </Link>
                        </div>

                        <PrimaryButton
                            class="w-full py-3 px-4 text-white font-medium rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform transition-all duration-200 hover:scale-102 hover:shadow-lg"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Log in
                        </PrimaryButton>
                        
                        <!-- Register link for mobile -->
                        <div class="mt-4 text-center">
                            <Link
                                :href="route('register')"
                                class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline"
                            >
                                Belum punya akun? Daftar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>