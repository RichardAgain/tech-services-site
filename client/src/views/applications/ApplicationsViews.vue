<script setup>
import PageTitle from '@/components/PageTitle.vue';
import TagCards from '@/components/TagCards.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { acceptProfileApplication, getProfileApplications } from '@/services/profile-applications';
import { useAuthStore } from '@/stores/authorization';
import { computed, onBeforeMount, ref } from 'vue';
import { rejectProfileApplication } from '../../services/profile-applications';

const user = useAuthStore().user

const applications = ref([])
const showModal = ref(false);
const selectedApplication = ref({});
const actionInProgress = ref(false);
const currentAction = ref(null);
const actionStatus = ref({
  show: false,
  success: false,
  message: ''
});

onBeforeMount(async () => {
    try {
        const data = await getProfileApplications()

        applications.value = data
        console.log(data)
    } finally {
        console.log(applications.value)
    }
})

// Status priority for sorting (lower number = higher priority)
const statusPriority = {
  'pending': 1,
  'approved': 2,
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
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

// Get status text helper
const getStatusText = (status) => {
  const statusMap = {
    'approved': 'Aprobado',
    'pending': 'Pendiente',
    'rejected': 'Rechazado'
  };
  
  return statusMap[status] || status;
};

// Modal functions
const openApplicationModal = (application) => {
  selectedApplication.value = { ...application };
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

// Application action handler
const handleApplicationAction = async (action) => {
  actionInProgress.value = true;
  currentAction.value = action;

  console.log(selectedApplication.value.id)
  
  try {
    // Simulate API call with delay
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Update application status based on action
    const userId = selectedApplication.value.user.id;
    
    // Find and update the application
    const appIndex = applications.value.findIndex(app => app.user.id === userId);
    if (appIndex !== -1) {
      if (action === 'approve') {
        const response = await acceptProfileApplication(selectedApplication.value.id);

        applications.value[appIndex].status = 'completed';
        actionStatus.value = {
          show: true,
          success: true,
          message: 'La solicitud ha sido aprobada exitosamente.'
        };
      } else if (action === 'reject') {
        const response = await rejectProfileApplication(selectedApplication.value.id);

        applications.value[appIndex].status = 'rejected';
        actionStatus.value = {
          show: true,
          success: true,
          message: 'La solicitud ha sido rechazada.'
        };
      }
      // Update the selected application to reflect changes
      selectedApplication.value = { ...applications.value[appIndex] };
    }
  } catch (error) {
    console.error('Error updating application:', error);
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

</script>

<template>
    <MainLayout>
        <PageTitle title="Solicitudes" />

        <div class="profile-applications-container p-6">
            <h1 class="text-2xl font-bold mb-8">Solicitudes de Perfil</h1>

            <!-- Applications Table -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <span class="mr-2">Solicitudes</span>
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
                                        Solicitante</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Etiquetas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha de Envío</th>
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
                                <tr v-for="app in sortedApplications" :key="app.user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{
                                        app.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="getStatusClass(app.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ getStatusText(app.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.user.username
                                        }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex flex-wrap gap-1">
                                            <TagCards :tags="app.tags" />
                                            <span v-if="app.tags.length === 0" class="text-gray-400 text-xs italic">Sin
                                                etiquetas</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                        formatDate(app.createdAt) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button @click="openApplicationModal(app)"
                                            class="text-emerald-600 hover:text-emerald-900 font-medium text-sm px-3 py-1 rounded-md hover:bg-emerald-50 transition-colors hover:cursor-pointer">
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Application Detail Modal -->
            <div v-if="showModal"
                class="fixed inset-0 bg-black/40 bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            Detalles de la Solicitud
                            <span :class="getStatusClass(selectedApplication.status)"
                                class="ml-3 px-2.5 py-0.5 text-xs font-medium rounded-full">
                                {{ getStatusText(selectedApplication.status) }}
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
                                    <p class="text-base font-medium">#{{ selectedApplication.user?.id }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Nombre Completo</h4>
                                    <p class="text-base">{{ selectedApplication.firstName }} {{
                                        selectedApplication.lastName }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Usuario</h4>
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-2">
                                            {{ selectedApplication.user?.username?.charAt(0).toUpperCase() || '?' }}
                                        </div>
                                        <span>{{ selectedApplication.user?.username || 'N/A' }}</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                                    <p class="text-base">{{ selectedApplication.email }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Teléfono</h4>
                                    <p class="text-base">{{ selectedApplication.phone }}</p>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Fecha de Creación</h4>
                                    <p class="text-base">{{ formatDateLong(selectedApplication.createdAt) }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Dirección</h4>
                                    <p class="text-base">{{ selectedApplication.address }}</p>
                                </div>

                                <div v-if="selectedApplication.tags && selectedApplication.tags.length > 0">
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Etiquetas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="tag in selectedApplication.tags" :key="tag.id"
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ tag.name }}
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
                                {{ selectedApplication.description || 'Sin descripción' }}
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
                        <div v-if="user.role.id === 1 && selectedApplication.status === 'pending'"
                            class="flex space-x-2">
                            <button @click="handleApplicationAction('approve')"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
                                :disabled="actionInProgress">
                                <span v-if="actionInProgress && currentAction === 'approve'" class="flex items-center">
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
                            <button @click="handleApplicationAction('reject')"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
                                :disabled="actionInProgress">
                                <span v-if="actionInProgress && currentAction === 'reject'" class="flex items-center">
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
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { transform: translateY(-20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.fixed {
  animation: fadeIn 0.3s ease-out;
}

.fixed > div {
  animation: slideIn 0.3s ease-out;
}
</style>