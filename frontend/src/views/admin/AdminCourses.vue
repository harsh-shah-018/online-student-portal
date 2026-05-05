<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/admin')" />
        <h2 class="text-2xl font-bold text-gray-800">Manage Courses</h2>
      </div>
      <BaseButton label="Add Course" icon="pi pi-plus" @click="showAddForm = !showAddForm" />
    </div>

    <div v-if="showAddForm" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
      <h3 class="text-lg font-semibold mb-4">Create New Course</h3>
      <form @submit.prevent="addCourse" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <BaseInput v-model="form.course_name" label="Course Name (e.g. MCA)" required />
        <BaseInput v-model="form.duration" label="Duration (e.g. 2 Years)" required />
        <BaseInput v-model="form.fees" type="number" label="Fees (₹)" required />
        <div class="md:col-span-2">
          <label class="text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="form.description" class="w-full mt-1 p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" rows="3"></textarea>
        </div>
        <div class="md:col-span-2 flex justify-end gap-2 mt-2">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showAddForm = false" />
          <BaseButton type="submit" label="Save Course" :disabled="loading" />
        </div>
      </form>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <BaseTable :data="courses" :rows="10">
        <Column field="course_id" header="ID"></Column>
        <Column field="course_name" header="Name" sortable></Column>
        <Column field="duration" header="Duration"></Column>
        <Column field="fees" header="Fees (₹)" sortable></Column>
        <Column header="Actions">
          <template #body="{ data }">
            <div class="flex gap-3">
              <button @click="viewCourse(data)" v-tooltip.top="'View Details'" class="text-blue-500 hover:text-blue-700">
                <i class="pi pi-eye"></i>
              </button>
              <button @click="editCourse(data)" v-tooltip.top="'Edit Course'" class="text-orange-500 hover:text-orange-700">
                <i class="pi pi-pencil"></i>
              </button>
              <button @click="deleteCourse(data.course_id)" v-tooltip.top="'Delete Course'" class="text-red-500 hover:text-red-700">
                <i class="pi pi-trash"></i>
              </button>
            </div>
          </template>
        </Column>
      </BaseTable>
    </div>

    <!-- View Modal -->
    <BaseModal v-model:show="showViewModal" header="Course Details">
      <div v-if="selectedCourse" class="space-y-3">
        <p><strong>ID:</strong> {{ selectedCourse.course_id }}</p>
        <p><strong>Name:</strong> {{ selectedCourse.course_name }}</p>
        <p><strong>Duration:</strong> {{ selectedCourse.duration }}</p>
        <p><strong>Fees:</strong> ₹{{ selectedCourse.fees }}</p>
        <p><strong>Description:</strong> {{ selectedCourse.description }}</p>
      </div>
    </BaseModal>

    <!-- Edit Modal -->
    <BaseModal v-model:show="showEditModal" header="Edit Course">
      <form v-if="selectedCourse" @submit.prevent="updateCourse" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <BaseInput v-model="selectedCourse.course_name" label="Course Name (e.g. MCA)" required />
        <BaseInput v-model="selectedCourse.duration" label="Duration (e.g. 2 Years)" required />
        <BaseInput v-model="selectedCourse.fees" type="number" label="Fees (₹)" required />
        <div class="md:col-span-2">
          <label class="text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="selectedCourse.description" class="w-full mt-1 p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" rows="3"></textarea>
        </div>
        <div class="md:col-span-2 flex justify-end gap-2 mt-2">
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
import api from '@/config/api'

const courses = ref([])
const showAddForm = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedCourse = ref(null)
const loading = ref(false)
const toast = useToast()

const form = reactive({
  course_name: '',
  description: '',
  duration: '',
  fees: ''
})

const fetchCourses = async () => {
  try {
    const res = await api.get('/api/admin/courses.php')
    courses.value = res.data
  } catch(e) { console.error(e) }
}

const addCourse = async () => {
  loading.value = true
  try {
    await api.post('/api/admin/courses.php', form)
    showAddForm.value = false
    form.course_name = ''; form.fees = '';
    await fetchCourses()
  } catch(e) { console.error(e) }
  loading.value = false
}

const viewCourse = (course) => {
  selectedCourse.value = { ...course }
  showViewModal.value = true
}

const editCourse = (course) => {
  selectedCourse.value = { ...course }
  showEditModal.value = true
}

const updateCourse = async () => {
  loading.value = true
  try {
    await api.put('/api/admin/courses.php', selectedCourse.value)
    showEditModal.value = false
    await fetchCourses()
    toast.add({ severity: 'success', summary: 'Updated', detail: 'Course updated successfully', life: 3000 })
  } catch(e) { console.error(e) }
  loading.value = false
}

const deleteCourse = async (id) => {
  if(!confirm('Are you sure you want to delete this course?')) return
  try {
    await api.delete('/api/admin/courses.php', { data: { course_id: id } })
    await fetchCourses()
    toast.add({ severity: 'info', summary: 'Deleted', detail: 'Course has been removed', life: 3000 })
  } catch(e) { console.error(e) }
}

onMounted(fetchCourses)
</script>
