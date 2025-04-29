<script setup>
import PageTitle from '@/components/PageTitle.vue';
import TagCards from '@/components/TagCards.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { fetchUserTasks } from '@/services/tasks';
import { useAuthStore } from '@/stores/authorization';
import { computed, onBeforeMount, ref } from 'vue';

const authStore = useAuthStore()
const user = authStore.user

const applications = ref([])
const tasks = ref([])

const showModal = ref(false);
const selectedTask = ref({});

const actionInProgress = ref(false);
const currentAction = ref(null);
const actionStatus = ref({
  show: false,
  success: false,
  message: ''
});

onBeforeMount(async () => {
    try {
        const response = await fetchUserTasks()

        console.log(user.role.id)

        tasks.value = response.tasks
        applications.value = response.applications
    } finally {

    }
})

const handleTaskAction = async (action) => {
  actionInProgress.value = true;
  currentAction.value = action;
  
  try {
    // Simulate API call with delay
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Update task status based on action
    const taskId = selectedTask.value.id;
    const taskType = selectedTask.value.type;
    
    if (taskType === 'application') {
      // Find and update the application
      const appIndex = applications.value.findIndex(app => app.id === taskId);
      if (appIndex !== -1) {
        if (action === 'approve') {
          applications.value[appIndex].status = 'approved';
          actionStatus.value = {
            show: true,
            success: true,
            message: 'La solicitud ha sido aprobada exitosamente.'
          };
        } else if (action === 'reject') {
          applications.value[appIndex].status = 'rejected';
          actionStatus.value = {
            show: true,
            success: true,
            message: 'La solicitud ha sido rechazada.'
          };
        }
        // Update the selected task to reflect changes
        selectedTask.value = { ...applications.value[appIndex], type: 'application' };
      }
    } else if (taskType === 'task') {
      // Find and update the task
      const taskIndex = tasks.value.findIndex(task => task.id === taskId);
      if (taskIndex !== -1) {
        if (action === 'complete') {
          tasks.value[taskIndex].status = 'completed';
          actionStatus.value = {
            show: true,
            success: true,
            message: 'La tarea ha sido completada exitosamente.'
          };
        } else if (action === 'cancel') {
          tasks.value[taskIndex].status = 'cancelled';
          actionStatus.value = {
            show: true,
            success: true,
            message: 'La tarea ha sido cancelada.'
          };
        }
        // Update the selected task to reflect changes
        selectedTask.value = { ...tasks.value[taskIndex], type: 'task' };
      }
    }
  } catch (error) {
    console.error('Error updating task:', error);
    actionStatus.value = {
      show: true,
      success: false,
      message: 'Ha ocurrido un error al procesar la acción. Por favor, inténtelo de nuevo.'
    };
  } finally {
    actionInProgress.value = false;
    currentAction.value = null;
  }
};

// Computed property for approved tasks from both sources
const approvedTasks = computed(() => {
    const approvedApplications = applications.value
        .filter(app => app.status === 'approved')
        .map(app => ({ ...app, type: 'application' }));

    const approvedCreatedTasks = tasks.value
        .filter(task => task.status === 'approved')
        .map(task => ({ ...task, type: 'task' }));

    // Combine and sort by date (newest first)
    return [...approvedApplications, ...approvedCreatedTasks]
        .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
});

// Status priority for sorting (lower number = higher priority)
const statusPriority = {
    'pending': 1,
    'approved': 2,
    'completed': 4,
    'canceled': 5,
    'rejected': 3
};

// Computed properties for sorted data
const sortedApplications = computed(() => {
    return [...applications.value].sort((a, b) => {
        // First sort by status priority
        const statusA = statusPriority[a.status] || 999;
        const statusB = statusPriority[b.status] || 999;

        if (statusA !== statusB) {
            return statusA - statusB;
        }

        // Then sort by date (newest first)
        return new Date(b.createdAt) - new Date(a.createdAt);
    });
});

const sortedTasks = computed(() => {
    return [...tasks.value].sort((a, b) => {
        // First sort by status priority
        const statusA = statusPriority[a.status] || 999;
        const statusB = statusPriority[b.status] || 999;

        if (statusA !== statusB) {
            return statusA - statusB;
        }

        // Then sort by date (newest first)
        return new Date(b.createdAt) - new Date(a.createdAt);
    });
});

// Format date helper for table display
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Format date helper for modal display (more detailed)
const formatDateLong = (dateString) => {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }).format(date);
};

// Get status class helper
const getStatusClass = (status) => {
    switch (status) {
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        case 'canceled':
            return 'bg-red-100 text-red-800';
        case 'completed':
            return 'bg-purple-100 text-purple-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Get status text helper
const getStatusText = (status) => {
    const statusMap = {
        'approved': 'Aprobado',
        'pending': 'Pendiente',
        'rejected': 'Rechazado',
        'canceled': 'En Progreso',
        'completed': 'Completado'
    };

    return statusMap[status] || status;
};

// Modal functions
const openTaskModal = (task) => {
  selectedTask.value = { ...task };
  showModal.value = true;
  // Reset action status when opening modal
  actionStatus.value = {
    show: false,
    success: false,
    message: ''
  };
  // Prevent scrolling on the body when modal is open
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  showModal.value = false;
  // Re-enable scrolling on the body
  document.body.style.overflow = 'auto';
};

</script>

<template>
    <MainLayout>
        <PageTitle title="Tareas" />

        <div class="tareas-container p-6">
            <!-- Approved Tasks Section -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <span class="mr-2">Tareas Aprobadas</span>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ approvedTasks.length }}
                    </span>
                </h2>

                <div v-if="approvedTasks.length === 0"
                    class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                    No hay tareas aprobadas disponibles
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="task in approvedTasks" :key="`approved-${task.type}-${task.id}`"
                        class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-xs font-medium text-gray-500">#{{ task.id }}</span>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">
                                    {{ getStatusText(task.status) }}
                                </span>
                            </div>

                            <h3 class="text-lg font-semibold text-emerald-700 mb-2 line-clamp-2">
                                {{ task.title }}
                            </h3>

                            <div class="flex items-center mb-3">
                                <div
                                    class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-2 text-xs">
                                    {{ task.requester?.username?.charAt(0).toUpperCase() || '?' }}
                                </div>
                                <span class="text-sm text-gray-600">{{ task.requester?.username }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">{{ formatDate(task.createdAt) }}</span>
                                <button @click="openTaskModal(task)"
                                    class="hover:cursor-pointer text-emerald-600 hover:text-emerald-900 font-medium text-sm px-3 py-1 rounded-md hover:bg-emerald-50 transition-colors">
                                    Ver
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <span class="mr-2">Solicitudes de Tareas</span>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ applications.length }}
                    </span>
                </h2>
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Título</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tags</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Solicitante</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Solicitado</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha de Creación</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="sortedApplications.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No hay
                                        solicitudes disponibles</td>
                                </tr>
                                <tr v-for="app in sortedApplications" :key="app.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ app.id
                                        }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="getStatusClass(app.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ getStatusText(app.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ app.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <TagCards :tags="app.tags" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        app.requester.username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        app.requested.username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        formatDate(app.createdAt) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button @click="openTaskModal(app)"
                                            class="text-emerald-600 hover:text-emerald-900 font-medium text-sm px-3 py-1 rounded-md hover:bg-emerald-50 transition-colors">
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tasks Table -->
            <div>
                <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <span class="mr-2">Tareas Creadas</span>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ tasks.length }}
                    </span>
                </h2>
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Título</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Solicitante</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha de Creación</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="sortedTasks.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No hay tareas
                                        disponibles</td>
                                </tr>
                                <tr v-for="task in sortedTasks" :key="task.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{
                                        task.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="getStatusClass(task.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ getStatusText(task.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ task.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        task.requester.username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        formatDate(task.createdAt) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button @click="openTaskModal(task)"
                                            class="text-emerald-600 hover:text-emerald-900 font-medium text-sm px-3 py-1 rounded-md hover:bg-emerald-50 transition-colors">
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="showModal"
                class="fixed inset-0 bg-black/40 bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            Detalles de la Tarea
                            <span :class="getStatusClass(selectedTask.status)"
                                class="ml-3 px-2.5 py-0.5 text-xs font-medium rounded-full">
                                {{ getStatusText(selectedTask.status) }}
                            </span>
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto flex-grow">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">ID</h4>
                                    <p class="text-base font-medium">#{{ selectedTask.id }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Título</h4>
                                    <p class="text-base"
                                        :class="{ 'font-semibold text-emerald-700': selectedTask.status === 'approved' }">
                                        <span v-if="selectedTask.status === 'approved'"
                                            class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        {{ selectedTask.title }}
                                    </p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Solicitante</h4>
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-2">
                                            {{ selectedTask.requester?.username?.charAt(0).toUpperCase() || '?' }}
                                        </div>
                                        <span>{{ selectedTask.requester?.username || 'N/A' }}</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Asignado a</h4>
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-2">
                                            {{ selectedTask.requested?.username?.charAt(0).toUpperCase() || '?' }}
                                        </div>
                                        <span>{{ selectedTask.requested?.username || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Fecha de Creación</h4>
                                    <p class="text-base">{{ formatDateLong(selectedTask.createdAt) }}</p>
                                </div>

                                <div v-if="selectedTask.tags && selectedTask.tags.length > 0">
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Etiquetas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="(tag, index) in selectedTask.tags" :key="index"
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ tag }}
                                        </span>
                                    </div>
                                </div>

                                <div v-else>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Etiquetas</h4>
                                    <p class="text-sm text-gray-500 italic">Sin etiquetas</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section (Full Width) -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Descripción</h4>
                            <div class="bg-gray-50 p-4 rounded-lg text-gray-700">
                                {{ selectedTask.description || 'Sin descripción' }}
                            </div>
                        </div>

                        <!-- Action Status Message -->
                        <div v-if="actionStatus.show" class="mt-6">
                            <div :class="[
                                'p-4 rounded-lg text-sm flex items-center',
                                actionStatus.success ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'
                            ]">
                                <span v-if="actionStatus.success" class="mr-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                                <span v-else class="mr-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                                {{ actionStatus.message }}
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-between sticky bottom-0 bg-white">
                        <!-- Role-based action buttons -->
                        <div v-if="user.role.id === 2" class="flex space-x-2">
                            <!-- Application-specific buttons -->
                            <template v-if="selectedTask.status === 'pending'">
                                <button @click="handleTaskAction('approve')"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
                                    :disabled="actionInProgress">
                                    <span v-if="actionInProgress && currentAction === 'approve'"
                                        class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Procesando...
                                    </span>
                                    <span v-else>Aceptar</span>
                                </button>
                                <button @click="handleTaskAction('reject')"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
                                    :disabled="actionInProgress">
                                    <span v-if="actionInProgress && currentAction === 'reject'"
                                        class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Procesando...
                                    </span>
                                    <span v-else>Rechazar</span>
                                </button>
                            </template>

                            <!-- Task-specific buttons -->
                            <template v-if="selectedTask.status === 'approved'">
                                <button @click="handleTaskAction('complete')"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
                                    :disabled="actionInProgress">
                                    <span v-if="actionInProgress && currentAction === 'complete'"
                                        class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Procesando...
                                    </span>
                                    <span v-else>Completar</span>
                                </button>
                                <button @click="handleTaskAction('cancel')"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
                                    :disabled="actionInProgress">
                                    <span v-if="actionInProgress && currentAction === 'cancel'"
                                        class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Procesando...
                                    </span>
                                    <span v-else>Cancelar</span>
                                </button>
                            </template>
                        </div>

                        <button @click="closeModal"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors ml-auto">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}

/* Modal animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.fixed {
    animation: fadeIn 0.3s ease-out;
}

.fixed>div {
    animation: slideIn 0.3s ease-out;
}
</style>