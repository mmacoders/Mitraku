<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ProfileUpdateModal from '@/Components/admin/ProfileUpdateModal.vue';

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