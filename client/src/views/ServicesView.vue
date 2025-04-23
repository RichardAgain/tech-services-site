<template>
  <div>
    <h1>Servicios de Técnicos</h1>
    <div class="services-list">
      <ServiceCard
        v-for="service in services"
        :key="service.id"
        :service="service"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ServiceCard from '../components/ServiceCard.vue'

const services = ref([
  { id: 1, nombre: 'Reparación de PC', tecnico: 'Juan Pérez', descripcion: 'Solución de problemas de hardware y software.' },
  { id: 2, nombre: 'Mantenimiento', tecnico: 'Ana Gómez', descripcion: 'Limpieza y optimización de equipos.' },
  { id: 3, nombre: 'Armado de PC', tecnico: 'Juan Pérez', descripcion: 'Ensamblaje de computadoras a medida.' },
])
</script>

<style scoped>
.services-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
</style> 