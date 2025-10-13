<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Buat Pengajuan PKS Baru
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 gap-6">
                <div>
                  <InputLabel for="title" value="Judul PKS" />
                  <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="title"
                  />
                  <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div>
                  <InputLabel for="description" value="Deskripsi" />
                  <textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                    rows="4"
                    required
                  ></textarea>
                  <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div>
                  <InputLabel for="document" value="Dokumen PKS" />
                  <input
                    id="document"
                    type="file"
                    class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-md file:border-0
                      file:text-sm file:font-semibold
                      file:bg-indigo-50 file:text-indigo-700
                      hover:file:bg-indigo-100
                      dark:file:bg-indigo-900 dark:file:text-indigo-200
                      dark:hover:file:bg-indigo-800"
                    @change="handleFileChange"
                    accept=".pdf,.doc,.docx"
                  />
                  <InputError class="mt-2" :message="form.errors.document" />
                  <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Format yang diterima: PDF, DOC, DOCX. Maksimal 2MB.
                  </p>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Link
                        :href="route('kelola.pks')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Kembali
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Simpan Pengajuan
                    </PrimaryButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  description: '',
  document: null,
})

const handleFileChange = (event) => {
  form.document = event.target.files[0]
}

const submit = () => {
  const formData = new FormData()
  formData.append('title', form.title)
  formData.append('description', form.description)
  if (form.document) {
    formData.append('document', form.document)
  }

  form.post(route('pks.store'), {
    data: formData,
    forceFormData: true,
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>