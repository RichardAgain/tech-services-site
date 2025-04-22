<script setup>
import { useAuthStore } from '@/stores/authotization';
const user = useAuthStore().user;

</script>

<template>
    <!-- {{ authStore }} -->

    <div>
        <header>

        </header>

        <main class="min-h-screen">
            <slot></slot>
        </main>

        <footer>
            
        </footer>
    </div>

</template>

<style>

</style>