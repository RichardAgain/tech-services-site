<template>

    <button
        :class="[
            'flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md shadow-sm hover:opacity-90 focus:outline-none hover:cursor-pointer',
            {
                'opacity-50 cursor-not-allowed': disabled,
                'active:opacity-95': !disabled,
            },
        ]"
        :disabled="disabled"
        @click="onClick"
    >
        <slot></slot>
    </button>
</template>

<script setup>
defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
    onClick: {
        type: Function,
        default: () => {},
    },
});

</script>