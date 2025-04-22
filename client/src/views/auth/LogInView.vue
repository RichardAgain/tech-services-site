<script setup>
import { ref, computed, reactive } from 'vue';
import { useAuthStore } from '@/stores/authotization';
import authServices from '@/services/auth';
import router from '@/router';
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

</template>

<style scoped>

</style>
