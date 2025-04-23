<template>
  <div class="service-card">
    <h2>{{ service.nombre }}</h2>
    <p><strong>Técnico:</strong> {{ service.tecnico }}</p>
    <p>{{ service.descripcion }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  service: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.service-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  width: 250px;
  background: #fafbfc;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.service-card h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
}
</style> 