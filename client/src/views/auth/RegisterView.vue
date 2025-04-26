<script setup>
import { ref, computed, reactive } from 'vue';
import { useAuthStore } from '@/stores/authorization';
import authServices from '@/services/auth';
import router from '@/router';
const authStore = useAuthStore();

const userData = reactive({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  confirmPassword: '',
})

const onSubmit = async (e) => {
  e.preventDefault();
  try {
    const token =  await authServices.register(userData)
    authStore.setToken(token);

    router.push('/');
  } catch (error) {
    console.log(error.data);
  }
}

</script>


<template>

</template>

<style scoped>

</style>