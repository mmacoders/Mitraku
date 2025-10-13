<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { User, Mail, Phone, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'mitra',
    phone: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
</script>

<template>
    <Head title="Register" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <!-- Glassmorphism background effect -->
        <div class="absolute inset-0 bg-white/30 dark:bg-gray-800/30 backdrop-blur-sm -z-10"></div>
        
        <div class="w-full max-w-xl">
            <!-- Animated form container -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-lg p-5 sm:p-6 transition-all duration-300 hover:shadow-xl">
                <!-- Header with title -->
                <div class="text-center mb-5 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Daftar Akun Mitra
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-300 text-sm">
                        Bergabunglah dengan kami dan mulai kerjasama
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Grid layout for Name and Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Mitra -->
                        <div class="space-y-1">
                            <InputLabel for="name" value="Nama Mitra" class="text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <User class="h-4 w-4 text-gray-400 " />
                                </div>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white transition-all duration-300 text-sm"
                                    placeholder="Nama lengkap Anda"
                                />
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Masukkan nama lengkap Anda sebagai mitra
                            </p>
                            <InputError :message="form.errors.name" class="mt-1 text-xs" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Mail class="h-4 w-4 text-gray-400 " />
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white transition-all duration-300 text-sm"
                                    placeholder="email@contoh.com"
                                />
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Alamat email aktif yang dapat dihubungi
                            </p>
                            <InputError :message="form.errors.email" class="mt-1 text-xs" />
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1">
                        <InputLabel for="phone" value="Nomor HP" class="text-gray-700 dark:text-gray-300 text-sm" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Phone class="h-4 w-4 text-gray-400 " />
                            </div>
                            <input
                                id="phone"
                                type="tel"
                                v-model="form.phone"
                                required
                                class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white transition-all duration-300 text-sm"
                                placeholder="081234567890"
                            />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Nomor handphone yang dapat dihubungi untuk verifikasi
                        </p>
                        <InputError :message="form.errors.phone" class="mt-1 text-xs" />
                    </div>

                    <!-- Grid layout for Password and Password Confirmation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div class="space-y-1">
                            <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Lock class="h-4 w-4 text-gray-400 " />
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    class="w-full pl-9 pr-9 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white transition-all duration-300 text-sm"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <Eye v-if="!showPassword" class="h-4 w-4 text-gray-400 hover:text-gray-600 mt" />
                                    <EyeOff v-else class="h-4 w-4 text-gray-400 hover:text-gray-600 mt" />
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Minimal 8 karakter dengan kombinasi huruf dan angka
                            </p>
                            <InputError :message="form.errors.password" class="mt-1 text-xs" />
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="space-y-1">
                            <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Lock class="h-4 w-4 text-gray-400" />
                                </div>
                                <input
                                    id="password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    v-model="form.password_confirmation"
                                    required
                                    class="w-full pl-9 pr-9 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white transition-all duration-300 text-sm"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordConfirmationVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <Eye v-if="!showPasswordConfirmation" class="h-4 w-4 text-gray-400 hover:text-gray-600 " />
                                    <EyeOff v-else class="h-4 w-4 text-gray-400 hover:text-gray-600" />
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Masukkan kembali password yang sama untuk konfirmasi
                            </p>
                            <InputError :message="form.errors.password_confirmation" class="mt-1 text-xs" />
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full py-2.5 px-4 rounded-lg text-white font-semibold shadow-md bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg text-sm"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                            <span v-if="form.processing" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                            <span v-else>Daftar</span>
                        </button>
                    </div>

                    <!-- Link ke Login -->
                    <div class="flex items-center justify-center ">
                        <Link
                            :href="route('login')"
                            class="text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline transition-colors duration-300"
                        >
                            Sudah punya akun? Login
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Inter', sans-serif;
}

/* Reduce padding on mobile for better fit */
@media (max-width: 640px) {
    .backdrop-blur-lg {
        padding: 1.25rem;
    }
    
    .text-xl {
        font-size: 1.25rem;
    }
    
    .space-y-4 {
        gap: 0.75rem;
    }
}

/* Ensure proper grid behavior on small screens */
@media (max-width: 768px) {
    .grid-cols-1 {
        grid-template-columns: 1fr;
    }
    
    .max-w-xl {
        max-width: 28rem;
    }
}
</style>