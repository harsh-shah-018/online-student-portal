<template>
  <Button
    :label="label"
    :icon="icon"
    :class="computedClass"
    v-bind="$attrs"
  />
</template>

<script setup>
import Button from 'primevue/button';
import { computed } from 'vue';

const props = defineProps({
  label: String,
  icon: String,
  variant: {
    type: String,
    default: 'primary' // primary, secondary, danger, success
  }
});

const computedClass = computed(() => {
  const base = 'px-4 py-2 font-semibold rounded shadow-sm focus:outline-none transition-colors duration-200';
  switch (props.variant) {
    case 'secondary':
      return `${base} bg-gray-500 hover:bg-gray-600 text-white`;
    case 'danger':
      return `${base} bg-red-600 hover:bg-red-700 text-white`;
    case 'success':
      return `${base} bg-green-600 hover:bg-green-700 text-white`;
    case 'primary':
    default:
      return `${base} bg-blue-600 hover:bg-blue-700 text-white`;
  }
});
</script>
