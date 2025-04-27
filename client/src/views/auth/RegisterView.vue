<script setup>
import { ref, reactive } from 'vue'
import { EyeIcon, EyeOffIcon, AlertCircleIcon, LoaderIcon } from 'lucide-vue-next'
import authService from '@/services/auth'
import { useAuthStore } from '@/stores/authorization'
import router from '@/router'

const authStore = useAuthStore()

const form = reactive({
  firstName: 'Balatro',
  lastName: 'Balatrez',
  username: 'balatreo',
  email: 'balatrobalatrez@example.com',
  address: 'Valencia, Carabobo',
  phone: '0414-1234567',
  password: 'password',
  password_confirmation: 'password',
})

const showPassword = ref(false)
const showpass_cordConfirmation = ref(false)
const isSubmitting = ref(false)
const formError = ref('')
const errors = reactive({
  firstName: '',
  lastName: '',
  username: '',
  email: '',
  address: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

const validateField = (field) => {
  errors[field] = ''
  
  if (field === 'firstName') {
    if (!form.firstName) {
      errors.firstName = 'First name is required'
    } else if (form.firstName.length < 2) {
      errors.firstName = 'First name must be at least 2 characters'
    }
  }
  
  if (field === 'lastName') {
    if (!form.lastName) {
      errors.lastName = 'Last name is required'
    } else if (form.lastName.length < 2) {
      errors.lastName = 'Last name must be at least 2 characters'
    }
  }
  
  if (field === 'username') {
    if (!form.username) {
      errors.username = 'Username is required'
    } else if (form.username.length < 3) {
      errors.username = 'Username must be at least 3 characters'
    } else if (!/^[a-zA-Z0-9_]+$/.test(form.username)) {
      errors.username = 'Username can only contain letters, numbers, and underscores'
    }
  }
  
  if (field === 'email') {
    if (!form.email) {
      errors.email = 'Email is required'
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
      errors.email = 'Please enter a valid email address'
    }
  }
  
  if (field === 'address') {
    if (!form.address) {
      errors.address = 'Address is required'
    } else if (form.address.length < 5) {
      errors.address = 'Please enter a complete address'
    }
  }
  
  if (field === 'phone') {
    if (!form.phone) {
      errors.phone = 'Phone number is required'
    }
  }
  
  if (field === 'password') {
    if (!form.password) {
      errors.password = 'Password is required'
    }
    // } else if (form.password.length < 8) {
    //   errors.password = 'Password must be at least 8 characters'
    // } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(form.password)) {
    //   errors.password = 'Password must contain at least one uppercase letter, one lowercase letter, and one number'
    // }
    
    // If confirm password is already filled, validate it again
    if (form.password_confirmation) {
      validateField('password_confirmation')
    }
  }
  
  if (field === 'password_confirmation') {
    if (!form.password_confirmation) {
      errors.password_confirmation = 'Please confirm your password'
    } else if (form.password !== form.password_confirmation) {
      errors.password_confirmation = 'Passwords do not match'
    }
  }
}

const validateForm = () => {
  validateField('firstName')
  validateField('lastName')
  validateField('username')
  validateField('email')
  validateField('address')
  validateField('phone')
  validateField('password')
  validateField('password_confirmation')
  validateField('acceptTerms')
  
  // Check if there are any errors
  return !Object.values(errors).some(error => error)
}

// Form submission
const handleSubmit = async () => {
  formError.value = ''
  
  if (!validateForm()) {
    formError.value = 'Please correct the errors in the form'
    return
  }
  
  isSubmitting.value = true

  try {
    const token = await authService.register(form)
    authStore.setToken(token)

    router.push('/')
    
  } catch (error) {
    formError.value = error.response.data.message || 'An error occurred during registration'
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
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Create an account</h2>
        <p class="mt-2 text-sm text-gray-600">
          Sign up to get started
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

        <!-- First name and Last name -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <!-- First name field -->
          <div>
            <label for="firstName" class="block text-sm font-medium text-gray-700">
              First Name
            </label>
            <div class="mt-1 relative">
              <input
                id="firstName"
                v-model="form.firstName"
                name="firstName"
                type="text"
                required
                :class="[
                  'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                  errors.firstName ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
                ]"
                @blur="validateField('firstName')"
              />
              <div v-if="errors.firstName" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <AlertCircleIcon class="h-5 w-5 text-red-500" />
              </div>
            </div>
            <p v-if="errors.firstName" class="mt-1 text-sm text-red-600">{{ errors.firstName }}</p>
          </div>

          <!-- Last name field -->
          <div>
            <label for="lastName" class="block text-sm font-medium text-gray-700">
              Last Name
            </label>
            <div class="mt-1 relative">
              <input
                id="lastName"
                v-model="form.lastName"
                name="lastName"
                type="text"
                required
                :class="[
                  'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                  errors.lastName ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
                ]"
                @blur="validateField('lastName')"
              />
              <div v-if="errors.lastName" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <AlertCircleIcon class="h-5 w-5 text-red-500" />
              </div>
            </div>
            <p v-if="errors.lastName" class="mt-1 text-sm text-red-600">{{ errors.lastName }}</p>
          </div>
        </div>

        <!-- Username field -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">
            Username
          </label>
          <div class="mt-1 relative">
            <input
              id="username"
              v-model="form.username"
              name="username"
              type="text"
              autocomplete="username"
              required
              :class="[
                'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                errors.username ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
              ]"
              @blur="validateField('username')"
            />
            <div v-if="errors.username" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <AlertCircleIcon class="h-5 w-5 text-red-500" />
            </div>
          </div>
          <p v-if="errors.username" class="mt-1 text-sm text-red-600">{{ errors.username }}</p>
        </div>

        <!-- Email field -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email
          </label>
          <div class="mt-1 relative">
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
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

        <!-- Address field -->
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700">
            Address
          </label>
          <div class="mt-1 relative">
            <input
              id="address"
              v-model="form.address"
              name="address"
              type="text"
              required
              :class="[
                'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                errors.address ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
              ]"
              @blur="validateField('address')"
            />
            <div v-if="errors.address" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <AlertCircleIcon class="h-5 w-5 text-red-500" />
            </div>
          </div>
          <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
        </div>

        <!-- Phone field -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">
            Phone
          </label>
          <div class="mt-1 relative">
            <input
              id="phone"
              v-model="form.phone"
              name="phone"
              type="tel"
              required
              :class="[
                'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                errors.phone ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
              ]"
              @blur="validateField('phone')"
            />
            <div v-if="errors.phone" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <AlertCircleIcon class="h-5 w-5 text-red-500" />
            </div>
          </div>
          <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
        </div>

        <!-- Password field -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Password
          </label>
          <div class="mt-1 relative">
            <input
              id="password"
              v-model="form.password"
              name="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="new-password"
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

        <!-- Confirm Password field -->
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
            Confirm Password
          </label>
          <div class="mt-1 relative">
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              :type="showpass_cordConfirmation ? 'text' : 'password'"
              autocomplete="new-password"
              required
              :class="[
                'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-200',
                errors.password_confirmation ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-sky-500'
              ]"
              @blur="validateField('password_confirmation')"
            />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <button 
                type="button" 
                class="text-gray-400 hover:text-gray-500 focus:outline-none" 
                @click="showpass_cordConfirmation = !showpass_cordConfirmation"
              >
                <EyeIcon v-if="showpass_cordConfirmation" class="h-5 w-5" />
                <EyeOffIcon v-else class="h-5 w-5" />
              </button>
            </div>
          </div>
          <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
        </div>

        <!-- Submit button -->
        <div>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-200 disabled:opacity-70"
          >
            <span v-if="isSubmitting" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <LoaderIcon class="h-5 w-5 text-sky-400 animate-spin" />
            </span>
            {{ isSubmitting ? 'Creating account...' : 'Create account' }}
          </button>
        </div>

        <!-- Sign in link -->
        <div class="text-center text-sm">
          <span class="text-gray-600">Already have an account?</span>
          <RouterLink to="login" class="font-medium text-sky-600 hover:text-sky-500 ml-1 transition-colors duration-200">
            Sign in
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>
