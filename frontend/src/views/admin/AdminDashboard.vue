<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Administrator Dashboard</h2>
      <BaseButton label="Create Notice" icon="pi pi-plus" variant="primary" @click="$router.push('/admin/notices')" />
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i class="pi pi-users text-xl"></i></div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Total Students</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats?.total_students || 0 }}</h3>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-purple-100 text-purple-600 rounded-lg"><i class="pi pi-book text-xl"></i></div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Active Courses</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats?.total_courses || 0 }}</h3>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-orange-100 text-orange-600 rounded-lg"><i class="pi pi-clock text-xl"></i></div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Pending Enrollments</p>
          <h3 class="text-2xl font-bold text-gray-900">{{ stats?.pending_enrollments || 0 }}</h3>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i class="pi pi-wallet text-xl"></i></div>
        <div>
          <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
          <h3 class="text-2xl font-bold text-gray-900">₹{{ stats?.total_revenue || 0 }}</h3>
        </div>
      </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Notices</h3>
        <BaseTable v-if="stats?.recent_notices" :data="stats.recent_notices" :rows="5" :paginator="false">
          <Column field="title" header="Title"></Column>
          <Column field="date_posted" header="Date"></Column>
        </BaseTable>
        <p v-else class="text-gray-500 text-sm">Loading notices...</p>
      </div>
      
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
         <i class="pi pi-chart-bar text-4xl text-gray-300 mb-2"></i>
         <h3 class="text-lg font-bold text-gray-600">Quick Actions</h3>
         <p class="text-gray-400 text-sm mb-4">Manage students and courses from here</p>
         <div class="flex flex-wrap justify-center gap-2">
           <BaseButton label="Manage Enrollments" variant="primary" icon="pi pi-check-circle" @click="$router.push('/admin/enrollments')" />
           <BaseButton label="Manage Students" variant="secondary" icon="pi pi-users" @click="$router.push('/admin/students')" />
           <BaseButton label="View Courses" variant="secondary" icon="pi pi-book" @click="$router.push('/admin/courses')" />
           <BaseButton label="Library" variant="secondary" icon="pi pi-book" @click="$router.push('/admin/library')" />
           <BaseButton label="Academics" variant="secondary" icon="pi pi-chart-line" @click="$router.push('/admin/academics')" />
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
import API_BASE_URL from '@/config/api'

const router = useRouter()
const stats = ref(null)

onMounted(async () => {
  const role = localStorage.getItem('role')
  if (role !== 'admin') {
    router.push('/')
    return
  }
  
  try {
    const response = await fetch('${API_BASE_URL}/api/admin/dashboard.php')
    stats.value = await response.json()
  } catch (err) {
    console.error("Failed to load dashboard data", err)
  }
})
</script>
