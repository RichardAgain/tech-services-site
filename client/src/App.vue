<script setup>
import { RouterView, RouterLink } from 'vue-router'
</script>

<template>
  <nav class="main-nav">
    <router-link to="/technicians">Perfiles de Técnicos</router-link>
    <router-link to="/services">Servicios</router-link>
  </nav>
  <RouterView />
</template>

<style scoped>
.main-nav {
  display: flex;
  gap: 1.5rem;
  padding: 1rem;
  background: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
  margin-bottom: 2rem;
}
.main-nav a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
}
.main-nav a.router-link-exact-active {
  color: #1976d2;
}
</style>
