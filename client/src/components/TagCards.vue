<template>
    <div v-for="tag in tags" class="flex flex-wrap gap-2 mb-6">
        <span :key="tag.id" class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded-full">
            {{ tag.name }}
        </span>
    </div>

</template>

<script setup>
defineProps({
    tags: {
        type: Array,
        required: true,
    },
});
</script>