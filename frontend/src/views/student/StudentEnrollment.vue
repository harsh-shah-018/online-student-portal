<template>
  <div class="space-y-6">
    <div class="flex items-center gap-2">
      <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/student')" />
      <h2 class="text-2xl font-bold text-gray-800">Course Enrollment</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <h3 class="text-lg font-semibold mb-4 text-gray-700">Available Courses</h3>
      
      <div v-if="loading" class="text-center py-8 text-gray-500">Loading courses...</div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="course in courses" :key="course.course_id" class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
          <div>
            <div class="flex justify-between items-start mb-2">
              <h4 class="font-bold text-lg text-blue-700">{{ course.course_name }}</h4>
              <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-2 py-1 rounded">{{ course.duration }}</span>
            </div>
            <p class="text-gray-600 text-sm mb-4">{{ course.description || 'No description provided.' }}</p>
            <p class="text-gray-900 font-bold mb-4">Fees: ₹{{ course.fees }}</p>
          </div>
          
          <div v-if="course.enrollment_status">
            <BaseButton :label="course.enrollment_status" disabled class="w-full opacity-75" />
          </div>
          <div v-else>
            <BaseButton label="Enroll Now" @click="enroll(course.course_id)" class="w-full" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useToast } from 'primevue/usetoast'
import API_BASE_URL from '@/config/api'

const courses = ref([])
const loading = ref(true)
const toast = useToast()

const fetchCourses = async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    const res = await fetch(`${API_BASE_URL}/api/student/enrollments.php?student_id=${user.student_id}`)
    courses.value = await res.json()
  } catch(e) { console.error(e) }
  loading.value = false
}

const enroll = async (course_id) => {
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    await fetch('${API_BASE_URL}/api/student/enrollments.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ student_id: user.student_id, course_id: course_id })
    })
    toast.add({ severity: 'success', summary: 'Enrollment Requested', detail: 'Admin will review your request.', life: 3000 })
    await fetchCourses()
  } catch(e) { console.error(e) }
}

onMounted(fetchCourses)
</script>
