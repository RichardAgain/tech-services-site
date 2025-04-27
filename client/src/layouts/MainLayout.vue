<script setup>
import { useAuthStore } from '@/stores/authorization';
import { ref, onMounted, onUnmounted } from 'vue'
import { ClipboardIcon, InboxIcon, UserIcon, LogOutIcon, MenuIcon, XIcon } from 'lucide-vue-next'
import router from '@/router';
import auth from '@/services/auth';
const user = useAuthStore().user;

// State
const isUserMenuOpen = ref(false)
const isMobileMenuOpen = ref(false)
const userMenuRef = ref(null)

// Methods
const navigateTo = (path) => {
    router.push(path)
}

const logout = () => {
    auth.logout()
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
        isUserMenuOpen.value = false
    }
}

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <header class="sticky top-0 z-40 w-full border-b bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <RouterLink to="/" class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-gray-900">Dashboard</span>
                    </RouterLink>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-4">
                    <button @click="navigateTo('/tecnicos')"
                        class="inline-flex items-center justify-center rounded-md hover:cursor-pointer text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background hover:bg-gray-100 hover:text-gray-900 h-10 py-2 px-4">
                        <UserIcon class="h-4 w-4 mr-2" />
                        <span>Tecnicos</span>
                    </button>
                    <button @click="navigateTo('/tareas')"
                        class="inline-flex items-center justify-center rounded-md hover:cursor-pointer text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background hover:bg-gray-100 hover:text-gray-900 h-10 py-2 px-4">
                        <ClipboardIcon class="h-4 w-4 mr-2" />
                        <span>Tareas</span>
                    </button>
                    <button @click="navigateTo('/solicitudes')"
                        class="inline-flex items-center justify-center rounded-md hover:cursor-pointer text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background hover:bg-gray-100 hover:text-gray-900 h-10 py-2 px-4">
                        <InboxIcon class="h-4 w-4 mr-2" />
                        <span>Solicitudes</span>
                    </button>
                    <button @click="navigateTo('/aplicar')"
                        class="inline-flex items-center justify-center rounded-md hover:cursor-pointer text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-10 py-2 px-4">
                        Aplicar
                    </button>
                </nav>

                <!-- User Menu (Desktop) -->
                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative" ref="userMenuRef">
                        <button @click="isUserMenuOpen = !isUserMenuOpen"
                            class="flex items-center space-x-2 rounded-full focus:outline-none focus:ring-2 focus:ring-primary hover:cursor-pointer">
                            <div v-if="user.image" class="h-9 w-9 rounded-full overflow-hidden">
                                <img :src="user.image" :alt="user.name" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200">
                                <UserIcon class="h-5 w-5 text-gray-700" />
                            </div>
                            <span class="sr-only">User menu</span>
                        </button>
                        <transition enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95">
                            <div v-if="isUserMenuOpen"
                                class="absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="p-2">
                                    <div class="flex items-center px-3 py-2">
                                        <div class="flex flex-col space-y-0.5 leading-none">
                                            <p class="font-medium text-sm text-gray-900 ">{{ user.firstName + ' ' +
                                                user.lastName }}</p>
                                            <p class="text-xs text-gray-500 ">{{ user.email }}</p>
                                        </div>
                                    </div>
                                    <div class="h-px bg-gray-200 my-1"></div>
                                    <button @click="navigateTo('/profile')"
                                        class="flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:cursor-pointer">
                                        Perfil
                                    </button>
                                    <div class="h-px bg-gray-200 my-1"></div>
                                    <button @click="logout"
                                        class="flex w-full items-center rounded-md px-3 py-2 text-sm text-red-600  hover:bg-gray-100 hover:cursor-pointer">
                                        <LogOutIcon class="mr-2 h-4 w-4" />
                                        <span>Cerrar sesión</span>
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary md:hidden">
                    <span class="sr-only">Open main menu</span>
                    <MenuIcon v-if="!isMobileMenuOpen" class="h-6 w-6" />
                    <XIcon v-else class="h-6 w-6" />
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-if="isMobileMenuOpen" class="md:hidden">
                <div class="space-y-1 px-4 py-3 sm:px-6">
                    <div v-if="user" class="flex items-center space-x-3 py-3">
                        <div v-if="user.image" class="h-10 w-10 rounded-full overflow-hidden">
                            <img :src="user.image" :alt="user.name" class="h-full w-full object-cover" />
                        </div>
                        <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200">
                            <UserIcon class="h-6 w-6 text-gray-700 " />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">{{ user.firstName + user.lastName }}</span>
                            <span class="text-xs text-gray-500 ">{{ user.email }}</span>
                        </div>
                    </div>
                    <div class="h-px bg-gray-200 my-2"></div>
                    <button @click="navigateTo('/dashboard/tasks')"
                        class="flex w-full items-center rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                        <ClipboardIcon class="mr-3 h-5 w-5" />
                        Tareas
                    </button>
                    <button @click="navigateTo('/dashboard/requests')"
                        class="flex w-full items-center rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                        <InboxIcon class="mr-3 h-5 w-5" />
                        Solicitudes
                    </button>
                    <button @click="navigateTo('/dashboard/apply')"
                        class="flex w-full items-center justify-center rounded-md px-3 py-2 text-base font-medium bg-primary text-primary-foreground hover:bg-primary/90 mt-2">
                        Aplicar
                    </button>
                    <div class="h-px bg-gray-200 my-2"></div>
                    <button @click="navigateTo('/dashboard/profile')"
                        class="flex w-full items-center rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                        Perfil
                    </button>
                    <button @click="navigateTo('/dashboard/settings')"
                        class="flex w-full items-center rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                        Configuración
                    </button>
                    <button @click="logout"
                        class="flex w-full items-center rounded-md px-3 py-2 text-base font-medium text-red-600  hover:bg-gray-100">
                        <LogOutIcon class="mr-3 h-5 w-5" />
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </transition>
    </header>

    <main class="flex-1 bg-gray-50 p-4 sm:p-6 lg:p-8 h-screen">
        <slot></slot>
    </main>

</template>

<style scoped>
/* Custom styles can be added here if needed */
</style>