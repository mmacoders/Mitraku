<template>
    <Modal :show="show" @close="closeModal">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ title }}
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center">
                    <div :class="iconClasses" class="flex-shrink-0">
                        <svg v-if="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium" :class="textClasses">
                            {{ message }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end">
                <button
                    @click="closeModal"
                    type="button"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition duration-150 ease-in-out"
                >
                    Tutup
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps<{
    show: boolean;
    type: 'success' | 'error';
    title: string;
    message: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const iconClasses = computed(() => {
    return props.type === 'success'
        ? 'text-green-400'
        : 'text-red-400';
});

const textClasses = computed(() => {
    return props.type === 'success'
        ? 'text-green-800 dark:text-green-200'
        : 'text-red-800 dark:text-red-200';
});

const closeModal = () => {
    emit('close');
};
</script>