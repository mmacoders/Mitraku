<template>
  <div class="mt-4">
    <div class="relative">
      <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200 dark:bg-gray-700">
        <!-- Progress Bar Background/Fill -->
        <div
          v-if="currentStep >= 1"
          class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center transition-all duration-500"
          :class="progressBarClass"
          :style="{ width: progressWidth }"
        ></div>
      </div>
      
      <!-- Steps Labels -->
      <div class="flex justify-between text-xs sm:text-sm">
        <!-- Step 1: Pengajuan -->
        <div class="flex flex-col items-center w-1/3">
          <div 
            class="rounded-full h-6 w-6 flex items-center justify-center mb-1 transition-colors duration-300"
            :class="getStepCircleClass(1)"
          >
            <span v-if="currentStep > 1" class="text-white font-bold">✓</span>
            <span v-else class="text-white font-bold">1</span>
          </div>
          <span :class="getStepTextClass(1)">Pengajuan</span>
        </div>

        <!-- Step 2: Pembahasan -->
        <div class="flex flex-col items-center w-1/3">
          <div 
            class="rounded-full h-6 w-6 flex items-center justify-center mb-1 transition-colors duration-300"
            :class="getStepCircleClass(2)"
          >
            <span v-if="currentStep > 2" class="text-white font-bold">✓</span>
            <span v-else class="text-white font-bold">2</span>
          </div>
          <span :class="getStepTextClass(2)">Pembahasan</span>
        </div>

        <!-- Step 3: Selesai -->
        <div class="flex flex-col items-center w-1/3">
          <div 
            class="rounded-full h-6 w-6 flex items-center justify-center mb-1 transition-colors duration-300"
            :class="getStepCircleClass(3)"
          >
            <span v-if="currentStep === 3" class="text-white font-bold">✓</span>
            <span v-else class="text-white font-bold">3</span>
          </div>
          <span :class="getStepTextClass(3)">{{ finalStepLabel }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  submission: {
    type: Object,
    required: true
  },
  type: {
    type: String, // 'pks' or 'mou'
    default: 'pks'
  }
});

// Determine current step based on submission state
const currentStep = computed(() => {
  const status = props.submission.status;
  const hasRapat = !!props.submission.rapat; // Check if rapat relationship exists
  
  if (status === 'disetujui') return 3;
  if (status === 'ditolak') return 3; // Finished but rejected
  
  // If status is 'proses'
  // Check if meeting scheduled
  if (hasRapat) return 2;
  
  return 1;
});

const isRejected = computed(() => props.submission.status === 'ditolak');

const progressWidth = computed(() => {
  if (currentStep.value === 1) return '33%';
  if (currentStep.value === 2) return '66%';
  return '100%';
});

const progressBarClass = computed(() => {
  if (isRejected.value) return 'bg-red-500';
  return 'bg-blue-500';
});

const finalStepLabel = computed(() => {
  if (isRejected.value) return 'Ditolak';
  return 'Disetujui';
});

const getStepCircleClass = (step) => {
  if (step < currentStep.value) {
    if (isRejected.value && step === 3) return 'bg-red-500'; // Should not happen for < currentStep if max is 3
    return 'bg-green-500'; // Completed steps are green
  }
  if (step === currentStep.value) {
    if (isRejected.value) return 'bg-red-500';
    if (step === 3 && props.submission.status === 'disetujui') return 'bg-green-500'; // Final success
    return 'bg-blue-500'; // Current active step
  }
  return 'bg-gray-300 dark:bg-gray-600'; // Pending steps
};

const getStepTextClass = (step) => {
  if (step <= currentStep.value) {
    if (isRejected.value && step === 3) return 'text-red-600 font-semibold';
    if (step === 3 && props.submission.status === 'disetujui') return 'text-green-600 font-semibold';
    return 'text-blue-600 font-semibold';
  }
  return 'text-gray-500 dark:text-gray-400';
};
</script>
