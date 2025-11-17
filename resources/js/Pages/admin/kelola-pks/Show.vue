<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import SignaturePad from '@/Components/SignaturePad.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
  submission: Object,
  canEdit: Boolean
})

// Status form
const statusForm = useForm({
  status: props.submission.status,
  notes: props.submission.notes || '',
  digital_signature: props.submission.digital_signature || ''
})

// Edit status form
const editStatusForm = useForm({
  status: '',
  notes: '',
  digital_signature: ''
})

// Show edit status modal
const showEditStatusModal = ref(false)

// Open edit status modal
const openEditStatusModal = (history) => {
  editStatusForm.status = history.status
  editStatusForm.notes = history.notes || ''
  editStatusForm.digital_signature = history.digital_signature || ''
  showEditStatusModal.value = true
}

// Close edit status modal
const closeEditStatusModal = () => {
  showEditStatusModal.value = false
  editStatusForm.reset()
}

// Update status
const updateStatus = () => {
  statusForm.put(route('pks-submission.update-status', props.submission.id), {
    onSuccess: () => {
      // Reload the page to show updated status
      window.location.reload()
    }
  })
}

// Edit history
const editHistory = (id) => {
  editStatusForm.put(route('pks-submission.edit-history', id), {
    onSuccess: () => {
      closeEditStatusModal()
      // Reload the page to show updated history
      window.location.reload()
    }
  })
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const formatDateTime = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const getStatusClass = (status) => {
  switch (status) {
    case 'disetujui':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    case 'proses':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'ditolak':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    case 'revisi':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'disetujui':
      return 'Disetujui'
    case 'proses':
      return 'Proses'
    case 'ditolak':
      return 'Ditolak'
    case 'revisi':
      return 'Revisi'
    default:
      return status
  }
}

const downloadDocument = (url, filename) => {
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  link.click()
}
</script>