<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/admin')" />
        <h2 class="text-2xl font-bold text-gray-800">Manage Students</h2>
      </div>
      <BaseButton label="Add Student" icon="pi pi-user-plus" @click="showAddForm = !showAddForm" />
    </div>

    <!-- Add Form -->
    <div v-if="showAddForm" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
      <h3 class="text-lg font-semibold mb-4">Add New Student</h3>
      <form @submit.prevent="addStudent" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <BaseInput v-model="form.name" label="Full Name" required />
        <BaseInput v-model="form.email" type="email" label="Email Address" required />
        <BaseInput v-model="form.phone" label="Phone Number" />
        <BaseInput v-model="form.dob" type="date" label="Date of Birth" />
        <BaseInput v-model="form.gender" label="Gender (Male/Female/Other)" />
        <BaseInput v-model="form.password" type="password" label="Initial Password" required />
        
        <div class="md:col-span-2 flex justify-end gap-2 mt-4">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showAddForm = false" />
          <BaseButton type="submit" label="Save Student" :disabled="loading" />
        </div>
      </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <BaseTable :data="students" :rows="10">
        <Column field="student_id" header="ID" sortable></Column>
        <Column field="name" header="Name" sortable></Column>
        <Column field="email" header="Email" sortable></Column>
        <Column field="phone" header="Phone"></Column>
        <Column header="Actions">
          <template #body="{ data }">
            <div class="flex gap-3">
              <button @click="viewStudent(data)" v-tooltip.top="'View Profile'" class="text-blue-500 hover:text-blue-700">
                <i class="pi pi-eye"></i>
              </button>
              <button @click="editStudent(data)" v-tooltip.top="'Edit Student'" class="text-orange-500 hover:text-orange-700">
                <i class="pi pi-pencil"></i>
              </button>
              <button @click="deleteStudent(data.student_id)" v-tooltip.top="'Delete Student'" class="text-red-500 hover:text-red-700">
                <i class="pi pi-trash"></i>
              </button>
            </div>
          </template>
        </Column>
      </BaseTable>
    </div>

    <!-- View Modal -->
    <BaseModal v-model:show="showViewModal" header="Student Details">
      <div v-if="selectedStudent" class="space-y-3">
        <p><strong>ID:</strong> {{ selectedStudent.student_id }}</p>
        <p><strong>Name:</strong> {{ selectedStudent.name }}</p>
        <p><strong>Email:</strong> {{ selectedStudent.email }}</p>
        <p><strong>Phone:</strong> {{ selectedStudent.phone }}</p>
        <p><strong>DOB:</strong> {{ selectedStudent.dob }}</p>
        <p><strong>Gender:</strong> {{ selectedStudent.gender }}</p>
        <p><strong>Address:</strong> {{ selectedStudent.address }}</p>
      </div>
    </BaseModal>

    <!-- Edit Modal -->
    <BaseModal v-model:show="showEditModal" header="Edit Student">
      <form v-if="selectedStudent" @submit.prevent="updateStudent" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <BaseInput v-model="selectedStudent.name" label="Full Name" required />
        <BaseInput v-model="selectedStudent.email" type="email" label="Email Address" required />
        <BaseInput v-model="selectedStudent.phone" label="Phone Number" />
        <BaseInput v-model="selectedStudent.dob" type="date" label="Date of Birth" />
        <BaseInput v-model="selectedStudent.gender" label="Gender (Male/Female/Other)" />
        <BaseInput v-model="selectedStudent.address" label="Address" />
        
        <div class="md:col-span-2 flex justify-end gap-2 mt-4">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showEditModal = false" />
          <BaseButton type="submit" label="Save Changes" :disabled="loading" />
        </div>
      </form>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import BaseModal from '@/components/base/BaseModal.vue'
import Column from 'primevue/column'
import { useToast } from 'primevue/usetoast'
import API_BASE_URL from '@/config/api'

const students = ref([])
const showAddForm = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedStudent = ref(null)
const loading = ref(false)
const toast = useToast()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  dob: '',
  gender: '',
  address: '',
  password: ''
})

const fetchStudents = async () => {
  try {
    const res = await fetch('${API_BASE_URL}/api/admin/students.php')
    students.value = await res.json()
  } catch(e) { console.error(e) }
}

const addStudent = async () => {
  loading.value = true
  try {
    await fetch('${API_BASE_URL}/api/admin/students.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form)
    })
    showAddForm.value = false
    form.name = ''; form.email = ''; form.password = '';
    await fetchStudents()
  } catch(e) { console.error(e) }
  loading.value = false
}

const viewStudent = (student) => {
  selectedStudent.value = { ...student }
  showViewModal.value = true
}

const editStudent = (student) => {
  selectedStudent.value = { ...student }
  showEditModal.value = true
}

const updateStudent = async () => {
  loading.value = true
  try {
    await fetch('${API_BASE_URL}/api/admin/students.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(selectedStudent.value)
    })
    showEditModal.value = false
    await fetchStudents()
    toast.add({ severity: 'success', summary: 'Updated', detail: 'Student details updated', life: 3000 })
  } catch(e) { console.error(e) }
  loading.value = false
}

const deleteStudent = async (id) => {
  if(!confirm('Are you sure?')) return
  try {
    await fetch('${API_BASE_URL}/api/admin/students.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ student_id: id })
    })
    await fetchStudents()
    toast.add({ severity: 'info', summary: 'Deleted', detail: 'Student has been removed', life: 3000 })
  } catch(e) { console.error(e) }
}

onMounted(fetchStudents)
</script>
