<template>
  <AuthenticatedLayout>
    <div>
      <!-- This page is now just a wrapper that immediately opens the modal -->
      <PksSubmissionDetailModal 
        :show="true" 
        :submission="submission" 
        context="admin"
        @close="handleClose" 
      />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import PksSubmissionDetailModal from '@/Components/admin/PksSubmissionDetailModal.vue'

const props = defineProps({
  submission: Object,
  auth: Object
})

// Redirect back to the index page when modal is closed
const handleClose = () => {
  router.visit(route('kelola.pks'))
}
</script>