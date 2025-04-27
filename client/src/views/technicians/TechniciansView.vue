<template>
  <MainLayout>
    <PageTitle title="Perfiles de Tecnicos"></PageTitle>

    <div class="p-8 grid grid-cols-2 gap-6">
      <ProfileCard
      v-for="profile in profiles"
      :key="profile.id"
      :profile="profile"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import { onBeforeMount, ref } from 'vue'
import ProfileCard from '@/components/ProfileCard.vue'
import MainLayout from '@/layouts/MainLayout.vue';
import PageTitle from '@/components/PageTitle.vue';
import { fetchProfiles } from '@/services/profiles';

const profiles = ref([])

onBeforeMount(async () => {
  try {
    const data = await fetchProfiles();

    profiles.value = data;
  } catch (error) {
    console.error('Error fetching technicians:', error);
  }
})
</script>

<style scoped>

</style> 