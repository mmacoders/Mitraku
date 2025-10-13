<template>
  <AuthenticatedLayout>
    <div>
      <!-- This page now just opens the modal -->
      <EditPksStatusModal 
        :show="true" 
        :submission="submission" 
        @close="handleClose" 
        @success="handleSuccess"
      />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import EditPksStatusModal from '@/Components/EditPksStatusModal.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  submission: Object
})

// Redirect back to the index page when modal is closed
const handleClose = () => {
  router.visit(route('kelola.pks'))
}

// Handle successful update
const handleSuccess = () => {
  router.visit(route('kelola.pks'))
}
</script>