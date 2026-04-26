<template>
  <div class="space-y-6">
    <div class="flex items-center gap-2">
      <BaseButton icon="pi pi-arrow-left" variant="secondary" @click="$router.push('/student')" />
      <h2 class="text-2xl font-bold text-gray-800">Fee Payments</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Pay Form -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-1">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Make a Payment</h3>
        <form @submit.prevent="payFee" class="space-y-4">
          <BaseInput v-model="form.amount" type="number" label="Amount to Pay (₹)" required />
          <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Payment Method</label>
            <select v-model="form.payment_method" class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
              <option value="Online">Credit/Debit Card (Online)</option>
              <option value="UPI">UPI Transfer</option>
              <option value="Bank Transfer">Bank Transfer</option>
            </select>
          </div>
          <BaseButton type="submit" label="Process Payment" class="w-full mt-4" :disabled="loading" />
        </form>
      </div>

      <!-- History -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Payment History</h3>
        <BaseTable :data="payments" :rows="10">
          <Column field="payment_id" header="Receipt No."></Column>
          <Column field="amount" header="Amount (₹)"></Column>
          <Column field="payment_method" header="Method"></Column>
          <Column field="payment_date" header="Date"></Column>
          <Column field="status" header="Status">
            <template #body="{ data }">
              <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-semibold">
                {{ data.status }}
              </span>
            </template>
          </Column>
        </BaseTable>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseTable from '@/components/base/BaseTable.vue'
import Column from 'primevue/column'
import { useToast } from 'primevue/usetoast'
import API_BASE_URL from '@/config/api'

const payments = ref([])
const loading = ref(false)
const toast = useToast()

const form = reactive({
  amount: '',
  payment_method: 'Online'
})

const fetchPayments = async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    const res = await fetch(`${API_BASE_URL}/api/student/fees.php?student_id=${user.student_id}`)
    payments.value = await res.json()
  } catch(e) { console.error(e) }
}

const payFee = async () => {
  loading.value = true
  const user = JSON.parse(localStorage.getItem('user'))
  try {
    await fetch('${API_BASE_URL}/api/student/fees.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ student_id: user.student_id, amount: form.amount, payment_method: form.payment_method })
    })
    toast.add({ severity: 'success', summary: 'Payment Successful', detail: 'Fee receipt generated.', life: 3000 })
    form.amount = ''
    await fetchPayments()
  } catch(e) { console.error(e) }
  loading.value = false
}

onMounted(fetchPayments)
</script>
