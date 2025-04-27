<template>
  <div class="bg-white hover:shadow-md hover:cursor-pointer transition-all rounded-lg p-4 flex items-center">
    <!-- <img v-if="technician.foto" :src="technician.foto" alt="Foto del técnico" class="technician-photo" /> -->
    <div class=""
      @click="handleClick"
    >
      <h2>{{ profile.firstName + " " + profile.lastName }}</h2>
      <!-- <p><strong>Especialidad:</strong> {{ profile.especialidad }}</p> -->
      <p>{{ profile.description }}</p>
    </div>
  </div>
</template>

<script setup>
import router from '@/router';

const props = defineProps({
  profile: {
    type: Object,
    required: true
  }
})

const handleClick = () => {
  router.push({
    name: 'technician',
    params: { id: props.profile.user.id }
  })
}
</script>

<style scoped>

</style> 