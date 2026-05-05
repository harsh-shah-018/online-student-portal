<template>
  <div class="space-y-6">
    <div class="flex items-center gap-2">
      <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/admin')" />
      <h2 class="text-2xl font-bold text-gray-800">Manage Academic Records</h2>
    </div>

    <!-- Student Selection -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1">
          <label class="text-sm font-semibold text-gray-700 mb-1 block">Select Student</label>
          <select v-model="selectedStudentId" @change="fetchStudentData" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
            <option value="" disabled>-- Select a Student --</option>
            <option v-for="student in students" :key="student.student_id" :value="student.student_id">
              {{ student.name }} (ID: {{ student.student_id }})
            </option>
          </select>
        </div>
        <div v-if="selectedStudentId" class="flex gap-2">
          <BaseButton label="Add Mark" icon="pi pi-plus" @click="openAddModal('mark')" />
          <BaseButton label="Add Attendance" icon="pi pi-plus" variant="secondary" @click="openAddModal('attendance')" />
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8 text-gray-500">Loading student records...</div>

    <div v-else-if="selectedStudentId" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Marks Management -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="pi pi-file-edit text-blue-500"></i> Marks Management
        </h3>
        <BaseTable :data="academicData.marks" :rows="5">
          <Column field="subject" header="Subject"></Column>
          <Column field="exam_type" header="Exam"></Column>
          <Column field="marks" header="Score"></Column>
          <Column header="Actions">
            <template #body="{ data }">
              <div class="flex gap-2">
                <button @click="editItem('mark', data)" v-tooltip.top="'Edit'" class="text-orange-500 hover:text-orange-700">
                  <i class="pi pi-pencil"></i>
                </button>
                <button @click="deleteItem('mark', data.mark_id)" v-tooltip.top="'Delete'" class="text-red-500 hover:text-red-700">
                  <i class="pi pi-trash"></i>
                </button>
              </div>
            </template>
          </Column>
        </BaseTable>
      </div>

      <!-- Attendance Management -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="pi pi-calendar text-green-500"></i> Attendance Logs
        </h3>
        <BaseTable :data="academicData.attendance" :rows="5">
          <Column field="date" header="Date"></Column>
          <Column field="status" header="Status"></Column>
          <Column header="Actions">
            <template #body="{ data }">
              <div class="flex gap-2">
                <button @click="editItem('attendance', data)" v-tooltip.top="'Edit'" class="text-orange-500 hover:text-orange-700">
                  <i class="pi pi-pencil"></i>
                </button>
                <button @click="deleteItem('attendance', data.attendance_id)" v-tooltip.top="'Delete'" class="text-red-500 hover:text-red-700">
                  <i class="pi pi-trash"></i>
                </button>
              </div>
            </template>
          </Column>
        </BaseTable>
      </div>

    </div>

    <div v-else class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300 text-gray-400">
      <i class="pi pi-user text-4xl mb-2"></i>
      <p>Please select a student to manage their academic records</p>
    </div>

    <!-- Modals -->
    <BaseModal v-model:show="showMarkModal" :header="isEdit ? 'Edit Mark' : 'Add Mark'">
      <form @submit.prevent="saveItem('mark')" class="space-y-4">
        <BaseInput v-model="form.subject" label="Subject" required />
        <BaseInput v-model="form.exam_type" label="Exam Type (e.g. Mid-term, Final)" required />
        <BaseInput v-model="form.marks" type="number" label="Marks" required />
        <BaseInput v-model="form.exam_date" type="date" label="Exam Date" required />
        <div class="flex justify-end gap-2">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showMarkModal = false" />
          <BaseButton type="submit" :label="isEdit ? 'Update' : 'Save'" />
        </div>
      </form>
    </BaseModal>

    <BaseModal v-model:show="showAttendanceModal" :header="isEdit ? 'Edit Attendance' : 'Add Attendance'">
      <form @submit.prevent="saveItem('attendance')" class="space-y-4">
        <BaseInput v-model="form.date" type="date" label="Date" required />
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1 block">Status</label>
          <select v-model="form.status" class="w-full p-2 border border-gray-300 rounded outline-none focus:ring-2 focus:ring-blue-500">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
            <option value="Late">Late</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showAttendanceModal = false" />
          <BaseButton type="submit" :label="isEdit ? 'Update' : 'Save'" />
        </div>
      </form>
    </BaseModal>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import Column from 'primevue/column'
import { useToast } from 'primevue/usetoast'
import api from '@/config/api'

const students = ref([])
const selectedStudentId = ref('')
const academicData = ref({ marks: [], attendance: [] })
const loading = ref(false)
const toast = useToast()

const showMarkModal = ref(false)
const showAttendanceModal = ref(false)
const isEdit = ref(false)
const form = reactive({})

const fetchStudents = async () => {
  try {
    const res = await api.get('/api/admin/academics.php')
    students.value = res.data
  } catch(e) { console.error(e) }
}

const fetchStudentData = async () => {
  if (!selectedStudentId.value) return
  loading.value = true
  try {
    const res = await api.get(`/api/admin/academics.php?student_id=${selectedStudentId.value}`)
    academicData.value = res.data
  } catch(e) { console.error(e) }
  loading.value = false
}

const openAddModal = (type) => {
  isEdit.value = false
  Object.keys(form).forEach(k => delete form[k])
  if (type === 'mark') {
    form.subject = ''; form.exam_type = ''; form.marks = 0; form.exam_date = new Date().toISOString().split('T')[0]
    showMarkModal.value = true
  } else {
    form.date = new Date().toISOString().split('T')[0]; form.status = 'Present'
    showAttendanceModal.value = true
  }
}

const editItem = (type, item) => {
  isEdit.value = true
  Object.assign(form, item)
  if (type === 'mark') showMarkModal.value = true
  else showAttendanceModal.value = true
}

const saveItem = async (type) => {
  const method = isEdit.value ? 'PUT' : 'POST'
  try {
    const payload = { ...form, type, student_id: selectedStudentId.value }
    const res = await api[method.toLowerCase()]('/api/admin/academics.php', payload)
    if (res.data) {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Record saved', life: 3000 })
      showMarkModal.value = false
      showAttendanceModal.value = false
      await fetchStudentData()
    }
  } catch(e) { console.error(e) }
}

const deleteItem = async (type, id) => {
  if (!confirm('Are you sure?')) return
  try {
    const res = await api.delete('/api/admin/academics.php', { data: { type, [type === 'mark' ? 'mark_id' : 'attendance_id']: id } })
    if (res.ok) {
      toast.add({ severity: 'info', summary: 'Deleted', detail: 'Record removed', life: 3000 })
      await fetchStudentData()
    }
  } catch(e) { console.error(e) }
}

onMounted(fetchStudents)
</script>
