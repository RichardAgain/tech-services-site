<script setup>
import { ref, computed, reactive } from 'vue';
import { EyeIcon, EyeOffIcon, AlertCircleIcon, LoaderIcon } from 'lucide-vue-next'

import router from '@/router';
import MainLayout from '@/layouts/MainLayout.vue';
import { useAuthStore } from '@/stores/authorization';
import authServices from '@/services/auth';

const authStore = useAuthStore();
const userData = reactive({
  email: 'admin@example.com',
  password: 'password',
})

// UI state
const showPassword = ref(false)
const isSubmitting = ref(false)
const formError = ref('')
const errors = reactive({
  email: '',
  password: ''
})

// Validation functions
const validateField = (field) => {
  errors[field] = ''
  
  if (field === 'email') {
    if (!userData.email) {
      errors.email = 'Username is required'
    } else if (userData.email.length < 3) {
      errors.username = 'Username must be at least 3 characters'
    }
  }
  
  if (field === 'password') {
    if (!userData.password) {
      errors.password = 'Password is required'
    } else if (userData.password.length < 6) {
      errors.password = 'Password must be at least 6 characters'
    }
  }
}

const validateForm = () => {
  validateField('email')
  validateField('password')
  return !errors.email && !errors.password
}

// Form submission
const handleSubmit = async () => {
  formError.value = ''
  
  if (!validateForm()) {
    return
  }
  
  isSubmitting.value = true
  
  try {
    const token =  await authServices.logIn(userData)
    authStore.setToken(token);

    router.push('/');
  } catch (error) {
    formError.value = 'An error occurred. Please try again.'
    console.error('Login error:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg transition-all duration-300">
      <!-- Logo and Header -->
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Welcome back</h2>
        <p class="mt-2 text-sm text-gray-600">
          Sign in to your account
        </p>
      </div>

      <!-- Form -->
      <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
        <!-- Alert for errors -->
        <div v-if="formError" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
          <div class="flex">
            <div class="flex-shrink-0">
              <AlertCircleIcon class="h-5 w-5 text-red-400" />
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700">{{ formError }}</p>
            </div>
          </div>
        </div>

        <!-- Username field -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email
          </label>
          <div class="mt-1 relative">
            <input
              id="email"
              v-model="userData.email"
              name="email"
              type="text"
              autocomplete="email"
              required
              :class="[
                'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                errors.email ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
              ]"
              @blur="validateField('email')"
            />
            <div v-if="errors.email" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <AlertCircleIcon class="h-5 w-5 text-red-500" />
            </div>
          </div>
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
        </div>

        <!-- Password field -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Password
          </label>
          <div class="mt-1 relative">
            <input
              id="password"
              v-model="userData.password"
              name="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              required
              :class="[
                'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                errors.password ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
              ]"
              @blur="validateField('password')"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <button 
                type="button" 
                class="text-gray-400 hover:text-gray-500 focus:outline-none" 
                @click="showPassword = !showPassword"
              >
                <EyeIcon v-if="showPassword" class="h-5 w-5" />
                <EyeOffIcon v-else class="h-5 w-5" />
              </button>
            </div>
          </div>
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
        </div>

        <!-- Submit button -->
        <div>
          <button
            type="submit"
            @click="handleSubmit"
            :disabled="isSubmitting"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-200 disabled:opacity-70"
          >
            <span v-if="isSubmitting" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <LoaderIcon class="h-5 w-5 text-sky-400 animate-spin" />
            </span>
            {{ isSubmitting ? 'Signing in...' : 'Sign in' }}
          </button>
        </div>

        <!-- Sign up link -->
        <div class="text-center text-sm">
          <span class="text-gray-600">Don't have an account?</span>
          <a href="#" class="font-medium text-sky-600 hover:text-sky-500 ml-1 transition-colors duration-200">
            Sign up
          </a>
        </div>
      </form>
    </div>
  </div>
</template>
<style scoped>

</style>
