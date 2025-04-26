<script setup>
import { useAuthStore } from '@/stores/authorization';
const user = useAuthStore().user;

</script>

<template>
    <!-- {{ authStore }} -->

    <div>
        <header>
            <nav class="main-nav">
                <router-link to="/technicians">Perfiles de Técnicos</router-link>
                <router-link to="/services">Servicios</router-link>
            </nav>
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