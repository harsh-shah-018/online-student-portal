<template>
  <Dialog 
    v-model:visible="visible" 
    :header="header" 
    :style="{ width: width || '50vw' }" 
    :breakpoints="{ '960px': '75vw', '641px': '90vw' }" 
    modal 
    class="p-fluid premium-modal"
    @hide="$emit('close')"
  >
    <div class="py-4">
      <slot></slot>
    </div>
    <template #footer v-if="$slots.footer">
      <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
        <slot name="footer"></slot>
      </div>
    </template>
  </Dialog>
</template>

<script setup>
import { computed } from 'vue'
import Dialog from 'primevue/dialog'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  header: {
    type: String,
    default: ''
  },
  width: {
    type: String,
    default: '50vw'
  }
})

const emit = defineEmits(['update:show', 'close'])

const visible = computed({
  get: () => props.show,
  set: (val) => emit('update:show', val)
})
</script>

<style>
.premium-modal .p-dialog-header {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.25rem 1.5rem;
}
.premium-modal .p-dialog-title {
  color: #1e293b;
  font-weight: 700;
  font-size: 1.25rem;
}
.premium-modal .p-dialog-content {
  background: #ffffff;
  padding: 1.5rem;
  color: #475569;
}
</style>
