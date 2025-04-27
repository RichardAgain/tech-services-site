<template>
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">{{ title }}</h1>
        <slot></slot>
    </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  }
})
</script>
