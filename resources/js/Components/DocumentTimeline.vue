<template>
  <div class="flow-root">
    <ul role="list" class="-mb-8">
      <li v-for="(event, index) in timelineEvents" :key="event.id">
        <div class="relative pb-8">
          <span v-if="index !== timelineEvents.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
          <div class="relative flex space-x-3">
            <div>
              <span class="flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white dark:ring-gray-800" :class="getEventIconBgClass(event.type)">
                <svg class="h-5 w-5" :class="getEventIconColorClass(event.type)" :fill="getEventIconFill(event.type)" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path :d="getEventIconPath(event.type)" />
                </svg>
              </span>
            </div>
            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ event.description }}
                  <span v-if="event.user" class="font-medium text-gray-900 dark:text-white">
                    oleh {{ event.user }}
                  </span>
                </p>
                <div v-if="event.notes" class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                  <p>{{ event.notes }}</p>
                </div>
                <div v-if="event.document_path" class="mt-2">
                  <a :href="`/storage/${event.document_path}`" target="_blank" class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lihat Dokumen
                  </a>
                </div>
              </div>
              <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                <time :datetime="event.datetime">{{ formatDate(event.datetime) }}</time>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  submission: Object,
  meetings: Array
})

const timelineEvents = computed(() => {
  const events = []
  
  // Initial submission
  events.push({
    id: 'submission-' + props.submission.id,
    type: 'submission',
    description: 'Dokumen PKS diajukan',
    user: props.submission.user?.name,
    datetime: props.submission.created_at,
    document_path: props.submission.document_path
  })
  
  // Status history events
  if (props.submission.status_histories) {
    props.submission.status_histories.forEach(history => {
      let type = 'status'
      let description = ''
      
      switch (history.status) {
        case 'proses':
          description = 'Status diubah menjadi Proses'
          type = 'status'
          break
        case 'revisi':
          description = 'Status diubah menjadi Revisi'
          type = 'revision'
          break
        case 'ditolak':
          description = 'Status diubah menjadi Ditolak'
          type = 'rejected'
          break
        case 'disetujui':
          description = 'Status diubah menjadi Disetujui'
          type = 'approved'
          break
      }
      
      events.push({
        id: 'history-' + history.id,
        type: type,
        description: description,
        notes: history.notes,
        datetime: history.created_at
      })
    })
  }
  
  // Meeting events
  if (props.meetings) {
    props.meetings.forEach(meeting => {
      events.push({
        id: 'meeting-' + meeting.id,
        type: 'meeting',
        description: 'Rapat dijadwalkan',
        user: meeting.creator?.name,
        notes: meeting.deskripsi,
        datetime: meeting.tanggal_waktu
      })
    })
  }
  
  // Final document event
  if (props.submission.final_document_path) {
    events.push({
      id: 'final-doc-' + props.submission.id,
      type: 'document',
      description: 'Dokumen final diunggah',
      datetime: props.submission.updated_at,
      document_path: props.submission.final_document_path
    })
  }
  
  // Digital signature event
  if (props.submission.digital_signature) {
    events.push({
      id: 'signature-' + props.submission.id,
      type: 'signature',
      description: 'Dokumen ditandatangani secara digital',
      user: props.submission.signed_by,
      datetime: props.submission.signed_at
    })
  }
  
  // Sort events by datetime
  return events.sort((a, b) => new Date(b.datetime) - new Date(a.datetime))
})

const getEventIconBgClass = (type) => {
  switch (type) {
    case 'submission':
      return 'bg-blue-500'
    case 'status':
      return 'bg-gray-500'
    case 'revision':
      return 'bg-yellow-500'
    case 'rejected':
      return 'bg-red-500'
    case 'approved':
      return 'bg-green-500'
    case 'meeting':
      return 'bg-purple-500'
    case 'document':
      return 'bg-indigo-500'
    case 'signature':
      return 'bg-teal-500'
    default:
      return 'bg-gray-500'
  }
}

const getEventIconColorClass = (type) => {
  return 'text-white'
}

const getEventIconFill = (type) => {
  return 'currentColor'
}

const getEventIconPath = (type) => {
  switch (type) {
    case 'submission':
      // Document icon
      return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
    case 'status':
      // Status icon
      return 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
    case 'revision':
      // Revision icon
      return 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
    case 'rejected':
      // Rejected icon
      return 'M6 18L18 6M6 6l12 12'
    case 'approved':
      // Approved icon
      return 'M5 13l4 4L19 7'
    case 'meeting':
      // Meeting icon
      return 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    case 'document':
      // Document icon
      return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
    case 'signature':
      // Signature icon
      return 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'
    default:
      // Default document icon
      return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  }
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}
</script>