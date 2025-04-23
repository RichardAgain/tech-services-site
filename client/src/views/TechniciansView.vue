<template>
  <div>
    <h1>Perfiles de Técnicos</h1>
    <div class="technicians-list">
      <TechnicianCard
        v-for="technician in technicians"
        :key="technician.id"
        :technician="technician"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import TechnicianCard from '../components/TechnicianCard.vue'

const technicians = ref([
  { id: 1, nombre: 'Juan Pérez', especialidad: 'Reparación de PC', foto: '', descripcion: 'Experto en hardware y software.' },
  { id: 2, nombre: 'Ana Gómez', especialidad: 'Mantenimiento', foto: '', descripcion: 'Mantenimiento preventivo y correctivo.' },
])
</script>

<style scoped>
.technicians-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
</style> 