<template>
  <div class="space-y-6">
    <div class="flex items-center gap-2">
      <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/student')" />
      <h2 class="text-2xl font-bold text-gray-800">Library</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- My Borrowed Books -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">My Borrowed Books</h3>
        <BaseTable :data="data.my_issues" :rows="5">
          <Column field="title" header="Book"></Column>
          <Column field="issue_date" header="Issued"></Column>
          <Column field="due_date" header="Due"></Column>
          <Column header="Status">
            <template #body="{ data }">
              <span v-if="data.return_date" class="text-green-600 font-bold text-xs">Returned</span>
              <span v-else class="text-orange-500 font-bold text-xs">Pending</span>
            </template>
          </Column>
        </BaseTable>
      </div>

      <!-- Available Books -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Available Books</h3>
        <BaseTable :data="data.books" :rows="5">
          <Column field="title" header="Title"></Column>
          <Column field="author" header="Author"></Column>
          <Column field="available_copies" header="Available">
            <template #body="{ data }">
              <span :class="data.available_copies > 0 ? 'text-green-600 font-bold' : 'text-red-500 font-bold'">
                {{ data.available_copies }}
              </span>
            </template>
          </Column>
        </BaseTable>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import Column from 'primevue/column'
import API_BASE_URL from '@/config/api'

const data = ref({ books: [], my_issues: [] })

const fetchLibrary = async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    const res = await fetch(`${API_BASE_URL}/api/student/library.php?student_id=${user.student_id}`)
    data.value = await res.json()
  } catch(e) { console.error(e) }
}

onMounted(fetchLibrary)
</script>
