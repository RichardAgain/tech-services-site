<template>
    <div v-if="error" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
      <div class="flex">
      <div class="flex-shrink-0">
          <AlertCircleIcon class="h-5 w-5 text-red-400" />
      </div>
      <div class="ml-3">
          <p class="text-sm text-red-700">{{ error }}</p>
      </div>
      </div>
    </div>
</template>

<script setup>
import { AlertCircleIcon } from 'lucide-vue-next';

defineProps({
  error: {
    type: String,
    default: null
  }
})
</script>