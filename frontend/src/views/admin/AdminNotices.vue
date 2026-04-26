<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/admin')" />
        <h2 class="text-2xl font-bold text-gray-800">Manage Notices</h2>
      </div>
      <BaseButton label="Create Notice" icon="pi pi-bell" @click="showAddForm = !showAddForm" />
    </div>

    <div v-if="showAddForm" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
      <h3 class="text-lg font-semibold mb-4">Broadcast New Notice</h3>
      <form @submit.prevent="addNotice" class="grid grid-cols-1 gap-4">
        <BaseInput v-model="form.title" label="Notice Title" required />
        <div>
          <label class="text-sm font-medium text-gray-700">Notice Content</label>
          <textarea v-model="form.content" required class="w-full mt-1 p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" rows="4"></textarea>
        </div>
        <div class="flex justify-end gap-2 mt-2">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showAddForm = false" />
          <BaseButton type="submit" label="Publish Notice" :disabled="loading" />
        </div>
      </form>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <BaseTable :data="notices" :rows="10">
        <Column field="title" header="Title"></Column>
        <Column field="content" header="Content"></Column>
        <Column field="date_posted" header="Date Posted" sortable></Column>
        <Column field="author_name" header="Author"></Column>
        <Column header="Actions">
          <template #body="{ data }">
            <button @click="deleteNotice(data.notice_id)" v-tooltip.top="'Delete Notice'" class="text-red-500 hover:text-red-700">
              <i class="pi pi-trash"></i>
            </button>
          </template>
        </Column>
      </BaseTable>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import Column from 'primevue/column'
import { useToast } from 'primevue/usetoast'
import API_BASE_URL from '@/config/api'

const notices = ref([])
const showAddForm = ref(false)
const loading = ref(false)
const toast = useToast()

const form = reactive({
  title: '',
  content: '',
  author_admin_id: null
})

const fetchNotices = async () => {
  try {
    const res = await fetch('${API_BASE_URL}/api/admin/notices.php')
    notices.value = await res.json()
  } catch(e) { console.error(e) }
}

const addNotice = async () => {
  loading.value = true
  const user = JSON.parse(localStorage.getItem('user'))
  form.author_admin_id = user.admin_id

  try {
    await fetch('${API_BASE_URL}/api/admin/notices.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form)
    })
    showAddForm.value = false
    form.title = ''; form.content = '';
    await fetchNotices()
    toast.add({ severity: 'success', summary: 'Success', detail: 'Notice posted successfully', life: 3000 })
  } catch(e) { console.error(e) }
  loading.value = false
}

const deleteNotice = async (id) => {
  if(!confirm('Delete this notice?')) return
  try {
    await fetch('${API_BASE_URL}/api/admin/notices.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ notice_id: id })
    })
    await fetchNotices()
    toast.add({ severity: 'info', summary: 'Deleted', detail: 'Notice deleted', life: 3000 })
  } catch(e) { console.error(e) }
}

onMounted(fetchNotices)
</script>
