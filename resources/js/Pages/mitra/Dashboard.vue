<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'

// Icons
import { 
  FileText, 
  Calendar, 
  Bell, 
  Plus, 
  User, 
  FilePlus, 
  CheckCircle, 
  X, 
  BadgeCheck, 
  Clock, 
  MapPin, 
  Text, 
  Download, 
  FileSignature 
} from 'lucide-vue-next'

// Components
import StatusBadge from '@/Components/StatusBadge.vue'
import CreatePksSubmissionModal from '@/Components/mitra/CreatePksSubmissionModal.vue'

// Props
const props = defineProps({
  submissions: Object,
  statistics: Object,
  notifications: Array,
  upcomingMeetings: Array
})

// Reactive data
const showMeetingDetail = ref(false)
const selectedMeeting = ref(null)
const showCreatePksModal = ref(false)

// Computed properties
const username = computed(() => {
  return props.submissions?.data?.length > 0 ? props.submissions.data[0].user?.name || '' : ''
})

const profilePicture = computed(() => {
  return props.submissions?.data?.length > 0 ? props.submissions.data[0].user?.profile_photo_url || '' : ''
})

const infoCards = computed(() => [
  {
    id: 'total',
    title: 'Total Pengajuan',
    value: props.statistics?.total || 0,
    icon: FileText,
    bgColor: 'bg-blue-100 dark:bg-blue-900/30',
    iconColor: 'text-blue-600 dark:text-blue-400'
  },
  {
    id: 'approved',
    title: 'Disetujui',
    value: props.statistics?.approved || 0,
    icon: BadgeCheck,
    bgColor: 'bg-green-100 dark:bg-green-900/30',
    iconColor: 'text-green-600 dark:text-green-400'
  },
  {
    id: 'pending',
    title: 'Proses',
    value: props.statistics?.pending || 0,
    icon: Clock,
    bgColor: 'bg-yellow-100 dark:bg-yellow-900/30',
    iconColor: 'text-yellow-600 dark:text-yellow-400'
  },
  {
    id: 'rejected',
    title: 'Ditolak',
    value: props.statistics?.rejected || 0,
    icon: X,
    bgColor: 'bg-red-100 dark:bg-red-900/30',
    iconColor: 'text-red-600 dark:text-red-400'
  }
])

const openMeetingDetail = (meeting) => {
  selectedMeeting.value = meeting
  showMeetingDetail.value = true
}

const closeMeetingDetail = () => {
  showMeetingDetail.value = false
  selectedMeeting.value = null
}

const openCreatePksModal = () => {
  showCreatePksModal.value = true
}

const closeCreatePksModal = () => {
  showCreatePksModal.value = false
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

const getMeetingStatusClass = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    case 'selesai':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    case 'dibatalkan':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

const getMeetingStatusText = (status) => {
  switch (status) {
    case 'akan_datang':
      return 'Akan Datang'
    case 'selesai':
      return 'Selesai'
    case 'dibatalkan':
      return 'Dibatalkan'
    default:
      return status
  }
}

// Fetch notifications periodically
onMounted(() => {
  // Fetch notifications every 30 seconds
  const interval = setInterval(() => {
    router.reload({ only: ['notifications'] })
  }, 30000)
  
  // Clear interval when component is unmounted
  return () => clearInterval(interval)
})
</script>