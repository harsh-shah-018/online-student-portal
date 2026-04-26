<template>
  <div class="space-y-6">
    <div class="flex items-center gap-2">
      <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/student')" />
      <h2 class="text-2xl font-bold text-gray-800">Academic Profile</h2>
    </div>

    <div v-if="loading" class="text-center text-gray-500 py-8">Loading academic records...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Marks Table -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="pi pi-file-edit text-blue-500"></i> Exam Marks
        </h3>
        <BaseTable :data="data.marks" :rows="5">
          <Column field="subject" header="Subject"></Column>
          <Column field="exam_type" header="Exam"></Column>
          <Column field="marks" header="Score"></Column>
        </BaseTable>
      </div>

      <!-- Attendance Table -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="pi pi-calendar text-green-500"></i> Attendance
        </h3>
        <BaseTable :data="data.attendance" :rows="5">
          <Column field="date" header="Date"></Column>
          <Column field="status" header="Status">
            <template #body="{ data }">
              <span :class="data.status === 'Present' ? 'text-green-600 font-bold' : 'text-red-600 font-bold'">
                {{ data.status }}
              </span>
            </template>
          </Column>
        </BaseTable>
      </div>

      <!-- Skills Table -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="pi pi-star text-yellow-500"></i> Technical Skills
          </h3>
          <BaseButton icon="pi pi-plus" label="Add" @click="openAddModal('skill')" size="small" />
        </div>
        <BaseTable :data="data.skills" :rows="5">
          <Column field="skill_name" header="Skill"></Column>
          <Column field="level" header="Level"></Column>
          <Column header="Actions">
            <template #body="{ data }">
              <div class="flex gap-2">
                <button @click="editItem('skill', data)" v-tooltip.top="'Edit'" class="text-orange-500 hover:text-orange-700">
                  <i class="pi pi-pencil text-sm"></i>
                </button>
                <button @click="deleteItem('skill', data.skill_id)" v-tooltip.top="'Delete'" class="text-red-500 hover:text-red-700">
                  <i class="pi pi-trash text-sm"></i>
                </button>
              </div>
            </template>
          </Column>
        </BaseTable>
      </div>

      <!-- Physical Activities -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="pi pi-heart text-red-500"></i> Physical Activities
          </h3>
          <BaseButton icon="pi pi-plus" label="Add" @click="openAddModal('activity')" size="small" />
        </div>
        <BaseTable :data="data.physical_activities" :rows="5">
          <Column field="activity_name" header="Activity"></Column>
          <Column field="performance" header="Performance"></Column>
          <Column field="date_recorded" header="Date"></Column>
          <Column header="Actions">
            <template #body="{ data }">
              <div class="flex gap-2">
                <button @click="editItem('activity', data)" v-tooltip.top="'Edit'" class="text-orange-500 hover:text-orange-700">
                  <i class="pi pi-pencil text-sm"></i>
                </button>
                <button @click="deleteItem('activity', data.activity_id)" v-tooltip.top="'Delete'" class="text-red-500 hover:text-red-700">
                  <i class="pi pi-trash text-sm"></i>
                </button>
              </div>
            </template>
          </Column>
        </BaseTable>
      </div>
    </div>

    <!-- Modals -->
    <BaseModal v-model:show="showSkillModal" :header="isEdit ? 'Edit Skill' : 'Add Skill'">
      <form @submit.prevent="saveItem('skill')" class="space-y-4">
        <BaseInput v-model="form.skill_name" label="Skill Name" required />
        <BaseInput v-model="form.level" label="Level (e.g. Beginner, Expert)" required />
        <div class="flex justify-end gap-2">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showSkillModal = false" />
          <BaseButton type="submit" :label="isEdit ? 'Update' : 'Save'" />
        </div>
      </form>
    </BaseModal>

    <BaseModal v-model:show="showActivityModal" :header="isEdit ? 'Edit Activity' : 'Add Activity'">
      <form @submit.prevent="saveItem('activity')" class="space-y-4">
        <BaseInput v-model="form.activity_name" label="Activity Name" required />
        <BaseInput v-model="form.performance" label="Performance" required />
        <BaseInput v-model="form.date_recorded" type="date" label="Date" required />
        <div class="flex justify-end gap-2">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showActivityModal = false" />
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
import API_BASE_URL from '@/config/api'

const data = ref({ marks: [], attendance: [], skills: [], physical_activities: [] })
const loading = ref(true)
const toast = useToast()

const showSkillModal = ref(false)
const showActivityModal = ref(false)
const isEdit = ref(false)
const form = reactive({})

const fetchAcademicData = async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    const res = await fetch(`${API_BASE_URL}/api/student/academic.php?student_id=${user.student_id}`)
    data.value = await res.json()
  } catch(e) { console.error(e) }
  loading.value = false
}

const openAddModal = (type) => {
  isEdit.value = false
  Object.keys(form).forEach(k => delete form[k])
  if (type === 'skill') {
    form.skill_name = ''; form.level = ''
    showSkillModal.value = true
  } else {
    form.activity_name = ''; form.performance = ''; form.date_recorded = new Date().toISOString().split('T')[0]
    showActivityModal.value = true
  }
}

const editItem = (type, item) => {
  isEdit.value = true
  Object.assign(form, item)
  if (type === 'skill') showSkillModal.value = true
  else showActivityModal.value = true
}

const saveItem = async (type) => {
  const user = JSON.parse(localStorage.getItem('user'))
  const method = isEdit.value ? 'PUT' : 'POST'
  try {
    const res = await fetch(`${API_BASE_URL}/api/student/academic.php`, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...form, type, student_id: user.student_id })
    })
    if (res.ok) {
      toast.add({ severity: 'success', summary: 'Success', detail: `Record ${isEdit.value ? 'updated' : 'added'}`, life: 3000 })
      showSkillModal.value = false
      showActivityModal.value = false
      await fetchAcademicData()
    }
  } catch(e) { console.error(e) }
}

const deleteItem = async (type, id) => {
  if (!confirm('Are you sure you want to delete this?')) return
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    const res = await fetch(`${API_BASE_URL}/api/student/academic.php`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type, student_id: user.student_id, [type === 'skill' ? 'skill_id' : 'activity_id']: id })
    })
    if (res.ok) {
      toast.add({ severity: 'info', summary: 'Deleted', detail: 'Record removed', life: 3000 })
      await fetchAcademicData()
    }
  } catch(e) { console.error(e) }
}

onMounted(fetchAcademicData)
</script>
