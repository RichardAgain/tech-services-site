<script setup>
import PageTitle from '@/components/PageTitle.vue';
import MainLayout from '@/layouts/MainLayout.vue';

import { ref, reactive, onBeforeMount } from 'vue';
import { LoaderIcon, CheckCircleIcon } from 'lucide-vue-next';
import { getAvaliableTags } from '@/services/tags';
import TagCards from '@/components/TagCards.vue';
import { createProfileApplication } from '@/services/profile-applications';
import { useRouter } from 'vue-router';
import { sleep } from '@/utils/sleep';

const router = useRouter();

const availableTags = ref([])

onBeforeMount(async () => {
  try {
    const data = await getAvaliableTags();

    availableTags.value = data
    console.log(availableTags.value)
  } finally {

  }
})

const emit = defineEmits(['submit', 'cancel']);

const form = reactive({
  description: '',
  tagIds: [],
  acceptTerms: false
});

const errors = reactive({
  description: '',
  tags: '',
  acceptTerms: ''
});

const isSubmitting = ref(false);
const showSuccess = ref(false);

const toggleTag = (tagId) => {
  const index = form.tagIds.indexOf(tagId);
  if (index === -1) {
    form.tagIds.push(tagId);
  } else {
    form.tagIds.splice(index, 1);
  }
  // Clear error when user selects tags
  if (form.tagIds.length > 0) {
    errors.tags = '';
  }
};

const isTagSelected = (tagId) => {
  return form.tagIds.includes(tagId);
};

const validateForm = () => {
  let isValid = true;
  
  // Reset errors
  errors.description = '';
  errors.tags = '';
  errors.acceptTerms = '';
  
  // Validate description
  if (!form.description.trim()) {
    errors.description = 'La descripción es requerida';
    isValid = false;
  } else if (form.description.length < 100) {
    errors.description = 'La descripción debe tener al menos 100 caracteres';
    isValid = false;
  } else if (form.description.length > 1000) {
    errors.description = 'La descripción no puede exceder los 1000 caracteres';
    isValid = false;
  }
  
  // Validate tags
  if (form.tagIds.length === 0) {
    errors.tags = 'Selecciona al menos una especialidad';
    isValid = false;
  }
  
  // Validate terms acceptance
  if (!form.acceptTerms) {
    errors.acceptTerms = 'Debes aceptar los términos y condiciones';
    isValid = false;
  }
  
  return isValid;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }
  
  try {
    isSubmitting.value = true;
    
    // Simulate API call
    await sleep(1000);
    
    // Get tag names for selected IDs
    const selectedTags = availableTags.value
      .filter(tag => form.tagIds.includes(tag.id))
      .map(tag => tag.id);
    
    // Prepare data for submission
    const applicationData = {
      description: form.description,
      tagIds: form.tagIds,
      tags: selectedTags
    };

    console.log(applicationData)
    
    const response = await createProfileApplication(applicationData);

    router.push({ name: 'applications' })
  } catch (error) {
    console.error('Error submitting application:', error);
  } finally {
    isSubmitting.value = false;
  }
};

const resetForm = () => {
  form.description = '';
  form.tagIds = [];
  form.acceptTerms = false;
  
  // Clear all errors
  errors.description = '';
  errors.tags = '';
  errors.acceptTerms = '';
  
  emit('cancel');
};
</script>

<template>
    <MainLayout>
        <PageTitle
            title="Aplicar a Tecnico"
        ></PageTitle>

        <div class="max-w-4xl mx-auto my-6">
    <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow-sm p-6">
      
      <!-- Description Field -->
      <div class="mb-6">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
          Descripción profesional
        </label>
        <div class="relative">
          <textarea
            id="description"
            v-model="form.description"
            rows="6"
            :class="[
              'w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition-all',
              errors.description ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-emerald-500'
            ]"
            placeholder="Describe tus habilidades, experiencia y especialidades como técnico..."
          ></textarea>
          <div v-if="errors.description" class="text-red-500 text-sm mt-1">
            {{ errors.description }}
          </div>
          <div class="text-gray-500 text-xs mt-1 flex justify-between">
            <span>Mínimo 100 caracteres</span>
            <span>{{ form.description.length }} / 1000</span>
          </div>
        </div>
      </div>
      
      <!-- Tag Selector -->
      <div class="mb-8">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Especialidades
        </label>
        <div class="mb-2">
          <p class="text-sm text-gray-500">Selecciona las áreas en las que tienes experiencia</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
          <button
            v-for="tag in availableTags"
            :key="tag.id"
            type="button"
            @click="toggleTag(tag.id)"
            :class="[
              'px-4 py-2 text-sm rounded-full transition-colors',
              isTagSelected(tag.id)
                ? 'bg-emerald-100 text-emerald-800 border-emerald-200 border'
                : 'bg-gray-100 text-gray-800 border-gray-200 border hover:bg-gray-200'
            ]"
          >
            {{ tag.name }}
          </button>
        </div>
        
        <div v-if="errors.tags" class="text-red-500 text-sm mt-2">
          {{ errors.tags }}
        </div>
      </div>
      
      <!-- Terms and Conditions -->
      <div class="mb-8">
        <div class="flex items-start">
          <div class="flex items-center h-5">
            <input
              id="terms"
              v-model="form.acceptTerms"
              type="checkbox"
              class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
            />
          </div>
          <div class="ml-3 text-sm">
            <label for="terms" class="text-gray-700">
              Acepto los <a href="#" class="text-emerald-600 hover:underline">términos y condiciones</a> y la <a href="#" class="text-emerald-600 hover:underline">política de privacidad</a>
            </label>
            <div v-if="errors.acceptTerms" class="text-red-500 text-sm mt-1">
              {{ errors.acceptTerms }}
            </div>
          </div>
        </div>
      </div>
      
      <!-- Form Actions -->
      <div class="flex justify-end gap-3">
        <button
          type="button"
          @click="resetForm"
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
        >
          Cancelar
        </button>
        <button
          type="submit"
          :disabled="isSubmitting"
          class="px-6 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <LoaderIcon v-if="isSubmitting" class="w-4 h-4 animate-spin" />
          <span>{{ isSubmitting ? 'Enviando...' : 'Enviar aplicación' }}</span>
        </button>
      </div>
    </form>
    
    <!-- Success Message -->
    <div 
      v-if="showSuccess" 
      class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 flex items-start"
    >
      <CheckCircleIcon class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" />
      <div>
        <h3 class="font-medium">Aplicación enviada con éxito</h3>
        <p class="text-sm mt-1">Tu aplicación ha sido recibida y será revisada por nuestro equipo.</p>
      </div>
    </div>
  </div>
    </MainLayout>
</template>

