<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div class="flex flex-wrap items-center gap-2">
        <h2 class="text-2xl font-bold text-gray-800 mr-4">Student Dashboard</h2>
        <BaseButton label="Enroll in Course" icon="pi pi-book" @click="$router.push('/student/enroll')" />
        <BaseButton label="Pay Fees" icon="pi pi-wallet" variant="success" @click="$router.push('/student/fees')" />
        <BaseButton label="Academics" icon="pi pi-chart-line" variant="secondary" @click="$router.push('/student/academic')" />
        <BaseButton label="Library" icon="pi pi-book" variant="secondary" @click="$router.push('/student/library')" />
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-600 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
        <i class="pi pi-user text-blue-500"></i>
        <span class="font-medium">{{ stats?.profile?.name || 'Loading...' }}</span>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-md text-white">
        <p class="text-blue-100 text-sm font-medium mb-1">Enrolled Courses</p>
        <h3 class="text-3xl font-bold">{{ stats?.enrollments?.length || 0 }}</h3>
      </div>
      <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-xl shadow-md text-white">
        <p class="text-indigo-100 text-sm font-medium mb-1">Fees Paid</p>
        <h3 class="text-3xl font-bold">₹{{ stats?.fees_paid || 0 }}</h3>
      </div>
      <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-6 rounded-xl shadow-md text-white">
        <p class="text-teal-100 text-sm font-medium mb-1">Upcoming Exams</p>
        <h3 class="text-3xl font-bold">1</h3>
      </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">My Enrollments</h3>
        <BaseTable v-if="stats?.enrollments" :data="stats.enrollments" :rows="5" :paginator="false">
          <Column field="course_name" header="Course"></Column>
          <Column field="status" header="Status">
            <template #body="{ data }">
              <span :class="data.status === 'Approved' ? 'text-green-600 bg-green-50 px-2 py-1 rounded text-xs' : 'text-orange-600 bg-orange-50 px-2 py-1 rounded text-xs'">
                {{ data.status }}
              </span>
            </template>
          </Column>
        </BaseTable>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Latest Announcements</h3>
        <div class="space-y-4">
          <div v-for="notice in stats?.notices" :key="notice.date_posted" class="border-l-4 border-blue-500 pl-4 py-1">
            <h4 class="font-semibold text-gray-800">{{ notice.title }}</h4>
            <p class="text-sm text-gray-500 mt-1">{{ notice.content }}</p>
            <span class="text-xs text-gray-400 mt-2 block">{{ notice.date_posted }}</span>
          </div>
          <p v-if="!stats?.notices?.length" class="text-gray-500 text-sm">No new announcements.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import Column from 'primevue/column'
import api from '@/config/api'

const router = useRouter()
const stats = ref(null)

onMounted(async () => {
  const role = localStorage.getItem('role')
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (role !== 'student' || !user.student_id) {
    router.push('/')
    return
  }
  
  try {
    const response = await api.get(`/api/student/dashboard.php?student_id=${user.student_id}`)
    stats.value = response.data
  } catch (err) {
    console.error("Failed to load dashboard data", err)
  }
})
</script>
