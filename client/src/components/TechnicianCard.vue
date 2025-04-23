<template>
  <div class="technician-card">
    <img v-if="technician.foto" :src="technician.foto" alt="Foto del técnico" class="technician-photo" />
    <div class="technician-info">
      <h2>{{ technician.nombre }}</h2>
      <p><strong>Especialidad:</strong> {{ technician.especialidad }}</p>
      <p>{{ technician.descripcion }}</p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  technician: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.technician-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  width: 250px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.technician-photo {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 6px;
  margin-bottom: 0.5rem;
}
.technician-info h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.2rem;
}
</style> 