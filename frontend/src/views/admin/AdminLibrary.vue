<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-2">
        <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/admin')" />
        <h2 class="text-2xl font-bold text-gray-800">Library Management</h2>
      </div>
      <BaseButton label="Add Book" icon="pi pi-plus" @click="showAddModal = true" />
    </div>

    <!-- Books Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <h3 class="text-lg font-semibold mb-4 text-gray-700">Book Inventory</h3>
      <BaseTable :data="library.books" :rows="5">
        <Column field="book_id" header="ID"></Column>
        <Column field="title" header="Title"></Column>
        <Column field="author" header="Author"></Column>
        <Column field="total_copies" header="Total Copies"></Column>
        <Column field="available_copies" header="Available"></Column>
        <Column header="Actions">
          <template #body="{ data }">
            <button @click="deleteBook(data.book_id)" v-tooltip.top="'Delete Book'" class="text-red-500 hover:text-red-700">
              <i class="pi pi-trash"></i>
            </button>
          </template>
        </Column>
      </BaseTable>
    </div>

    <!-- Issues Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mt-6">
      <h3 class="text-lg font-semibold mb-4 text-gray-700">Book Issues</h3>
      <BaseTable :data="library.issues" :rows="5">
        <Column field="student_name" header="Student"></Column>
        <Column field="title" header="Book"></Column>
        <Column field="issue_date" header="Issued On"></Column>
        <Column field="due_date" header="Due Date"></Column>
        <Column field="return_date" header="Returned On">
          <template #body="{ data }">
            <span v-if="data.return_date" class="text-green-600 font-bold">{{ data.return_date }}</span>
            <span v-else class="text-orange-500 font-bold">Pending</span>
          </template>
        </Column>
      </BaseTable>
    </div>

    <!-- Add Book Modal -->
    <BaseModal v-model:show="showAddModal" header="Add New Book">
      <form @submit.prevent="addBook" class="space-y-4">
        <BaseInput v-model="form.title" label="Book Title" required />
        <BaseInput v-model="form.author" label="Author Name" required />
        <BaseInput v-model="form.total_copies" type="number" label="Number of Copies" required />
        <div class="flex justify-end gap-2 mt-4">
          <BaseButton type="button" label="Cancel" variant="secondary" @click="showAddModal = false" />
          <BaseButton type="submit" label="Save Book" />
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

const library = ref({ books: [], issues: [] })
const showAddModal = ref(false)
const toast = useToast()

const form = reactive({ title: '', author: '', total_copies: 1 })

const fetchLibrary = async () => {
  try {
    const res = await fetch('${API_BASE_URL}/api/admin/library.php')
    library.value = await res.json()
  } catch(e) { console.error(e) }
}

const addBook = async () => {
  try {
    await fetch('${API_BASE_URL}/api/admin/library.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form)
    })
    showAddModal.value = false
    form.title = ''; form.author = ''; form.total_copies = 1;
    await fetchLibrary()
    toast.add({ severity: 'success', summary: 'Success', detail: 'Book added to library', life: 3000 })
  } catch(e) { console.error(e) }
}

const deleteBook = async (id) => {
  if(!confirm('Delete this book?')) return
  try {
    await fetch('${API_BASE_URL}/api/admin/library.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ book_id: id })
    })
    await fetchLibrary()
    toast.add({ severity: 'info', summary: 'Deleted', detail: 'Book removed from library', life: 3000 })
  } catch(e) { console.error(e) }
}

onMounted(fetchLibrary)
</script>
