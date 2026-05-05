<template>
  <div class="space-y-6">
    <div class="flex items-center gap-2">
      <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/admin')" />
      <h2 class="text-2xl font-bold text-gray-800">Manage Enrollments</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <BaseTable :data="enrollments" :rows="10">
        <Column field="enrollment_id" header="ID"></Column>
        <Column field="student_name" header="Student" sortable></Column>
        <Column field="course_name" header="Course" sortable></Column>
        <Column field="enrollment_date" header="Request Date" sortable></Column>
        <Column field="status" header="Status" sortable>
          <template #body="{ data }">
            <span :class="getStatusClass(data.status)">
              {{ data.status }}
            </span>
          </template>
        </Column>
        <Column header="Actions">
          <template #body="{ data }">
            <div class="flex gap-2" v-if="data.status === 'Pending'">
              <button @click="updateStatus(data.enrollment_id, 'Approved')" v-tooltip.top="'Approve Request'" class="bg-green-100 text-green-600 hover:bg-green-200 px-3 py-1 rounded text-sm font-semibold transition-colors">
                <i class="pi pi-check mr-1"></i> Approve
              </button>
              <button @click="updateStatus(data.enrollment_id, 'Rejected')" v-tooltip.top="'Reject Request'" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded text-sm font-semibold transition-colors">
                <i class="pi pi-times mr-1"></i> Reject
              </button>
            </div>
            <span v-else class="text-gray-400 text-sm">Actioned</span>
          </template>
        </Column>
      </BaseTable>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import Column from 'primevue/column'
import { useToast } from 'primevue/usetoast'
import api from '@/config/api'

const enrollments = ref([])
const toast = useToast()

const fetchEnrollments = async () => {
  try {
    const res = await api.get('/api/admin/enrollments.php')
    enrollments.value = res.data
  } catch(e) { console.error(e) }
}

const updateStatus = async (id, status) => {
  if(!confirm(`Are you sure you want to mark this as ${status}?`)) return
  try {
    await api.put('/api/admin/enrollments.php', { enrollment_id: id, status: status })
    await fetchEnrollments()
    toast.add({ severity: status === 'Approved' ? 'success' : 'warn', summary: 'Status Updated', detail: `Enrollment marked as ${status}`, life: 3000 })
  } catch(e) { console.error(e) }
}

const getStatusClass = (status) => {
  if(status === 'Approved') return 'text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-semibold'
  if(status === 'Rejected') return 'text-red-600 bg-red-50 px-2 py-1 rounded text-xs font-semibold'
  return 'text-orange-600 bg-orange-50 px-2 py-1 rounded text-xs font-semibold'
}

onMounted(fetchEnrollments)
</script>
