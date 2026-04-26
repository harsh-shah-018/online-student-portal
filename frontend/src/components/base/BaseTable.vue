<template>
  <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <DataTable :value="data" :paginator="paginator" :rows="rows" class="premium-table" responsiveLayout="scroll" v-bind="$attrs">
      <slot></slot>
    </DataTable>
  </div>
</template>

<script setup>
import DataTable from 'primevue/datatable';

defineProps({
  data: {
    type: Array,
    required: true
  },
  paginator: {
    type: Boolean,
    default: true
  },
  rows: {
    type: Number,
    default: 10
  }
});
</script>

<style>
/* Premium Table Overrides */
.premium-table {
  width: 100%;
}
.premium-table .p-datatable-header {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 1rem;
}
.premium-table .p-datatable-thead > tr > th {
  background: #f1f5f9;
  color: #334155;
  font-weight: 700;
  padding: 1rem 1.5rem;
  border-bottom: 2px solid #e2e8f0;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}
.premium-table .p-datatable-tbody > tr {
  transition: background-color 0.2s, transform 0.2s;
}
.premium-table .p-datatable-tbody > tr:hover {
  background-color: #f8fafc;
}
.premium-table .p-datatable-tbody > tr > td {
  padding: 1rem 1.5rem;
  color: #475569;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.875rem;
}
.premium-table .p-paginator {
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
  padding: 0.75rem;
}
.premium-table .p-paginator .p-paginator-page, 
.premium-table .p-paginator .p-paginator-next, 
.premium-table .p-paginator .p-paginator-last, 
.premium-table .p-paginator .p-paginator-first, 
.premium-table .p-paginator .p-paginator-prev {
  color: #64748b;
  border-radius: 0.375rem;
  transition: all 0.2s;
}
.premium-table .p-paginator .p-paginator-page.p-highlight {
  background: #ebf8ff;
  color: #2b6cb0;
  font-weight: bold;
}
</style>
