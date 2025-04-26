<script setup>
import { ref, computed, reactive } from 'vue';
import { useAuthStore } from '@/stores/authorization';
import authServices from '@/services/auth';
import router from '@/router';
import MainLayout from '@/layouts/MainLayout.vue';
const authStore = useAuthStore();

const userData = reactive({
  email: '',
  password: '',
})

const onSubmit = async (e) => {
  e.preventDefault();
  try {
    const token =  await authServices.logIn(userData)
    authStore.setToken(token);

    router.push('/');
  } catch (error) {
    console.log(error.response.data.message);
  }
}
</script>

<template>
  <MainLayout>
    <input v-model="userData.email" />
    <input v-model="userData.password" type="password" />
  </MainLayout>
</template>

<style scoped>

</style>
