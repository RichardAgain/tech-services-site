<template>
  <MainLayout>
    <PageTitle title="Perfiles de Tecnicos"></PageTitle>


    <div class="grid grid-cols-4 gap-4">

      <div class="p-8 col-span-3 grid grid-cols-2 gap-6">
        <div v-if="isLoading" class="col-span-2">
          <Spinner/>
        </div>

        <div v-else-if="profiles.length === 0" class="col-span-2">
          <h2 class="text-xl text-center mb-4 text-zinc-800">No hay perfiles disponibles</h2>
        </div>

        <ProfileCard
          v-for="profile in profiles"
          :key="profile.id"
          :profile="profile"
        />
      </div>
      <div class="p-4">
        <h2 class="text-xl font-bold mb-4 text-zinc-800">Filtros</h2>
          <h3 class="text-lg font-semibold mb-2">Especialidades</h3>
          <ul>
            <li v-for="tag in tags" :key="tag.id" class="mb-2">
              <input type="checkbox" @click="handleTagClick" :id="tag.name" :value="tag.id" class="mr-2" />
              <label :for="tag.name">{{ tag.name }}</label>
            </li>
          </ul>
      </div>
    </div>

  </MainLayout>
</template>

<script setup>
import { onBeforeMount, ref } from 'vue'
import ProfileCard from '@/components/ProfileCard.vue'
import MainLayout from '@/layouts/MainLayout.vue';
import PageTitle from '@/components/PageTitle.vue';
import { fetchProfiles } from '@/services/profiles';
import Spinner from '@/components/Spinner.vue';

const isLoading = ref(true)

const profiles = ref([])
const tags = ref([])

const filters = ref({
  tags: []
})

const getProfileData = async () => {
  profiles.value = []
  isLoading.value = true

  try {
    const data = await fetchProfiles(
      filters.value.tags.join(',')
    );

    profiles.value = data.profiles;
    tags.value = data.tags;
  } catch (error) {
    console.error('Error fetching technicians:', error);
  } finally {
    isLoading.value = false;
  }
}

onBeforeMount(async () => {
  getProfileData();
})

const handleTagClick = (e) => {
  const tagId = e.target.value;
  console.log(tagId)

  if (filters.value.tags.includes(tagId)) {
    filters.value.tags = filters.value.tags.filter(id => id !== tagId);
  } else {
    filters.value.tags.push(tagId);
  }

  getProfileData();
}

</script>

<style scoped>

</style> 