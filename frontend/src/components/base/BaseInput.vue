<template>
  <div class="flex flex-col gap-1.5 w-full">
    <label v-if="label" :for="id" class="text-sm font-semibold text-gray-700 tracking-wide">{{ label }}</label>
    <InputText
      :id="id"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @keypress="handleKeypress"
      :type="type"
      :placeholder="placeholder"
      class="w-full px-4 py-2.5 text-gray-800 bg-gray-50 border border-gray-200 rounded-lg shadow-inner focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 outline-none"
      v-bind="$attrs"
    />
  </div>
</template>

<script setup>
import InputText from 'primevue/inputtext';

const props = defineProps({
  modelValue: [String, Number],
  label: String,
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substring(2, 9)}`
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: String
});

defineEmits(['update:modelValue']);

const handleKeypress = (event) => {
  if (props.type === 'number') {
    const charCode = (event.which) ? event.which : event.keyCode;
    // Allow digits (48-57), dot (46), backspace (8)
    if (charCode !== 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
      event.preventDefault();
    }
  }
}
</script>
