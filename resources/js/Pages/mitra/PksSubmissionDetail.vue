<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import StatusBadge from '@/Components/StatusBadge.vue'

// Icons
import { 
  Home, 
  FileText, 
  Calendar, 
  User, 
  Clock, 
  CheckCircle, 
  X, 
  FileSignature,
  Download,
  Eye,
  Edit3,
  Trash2
} from 'lucide-vue-next'

const props = defineProps({
  submission: Object,
  canEdit: Boolean
})

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