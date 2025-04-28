<template>
    <div>
        <!-- Trigger Button -->
        <PrimaryButton @click="isOpen = true">
            <Send size="16" />
            Solicitar
        </PrimaryButton>

        <!-- Overlay -->
        <Teleport to="body">
            <Transition name="overlay">
                <div v-if="isOpen" class="fixed inset-0 bg-black/50 z-40" @click="isOpen = false"></div>
            </Transition>

            <!-- Sliding Sheet -->
            <Transition name="sheet">
                <div v-if="isOpen"
                    class="fixed inset-y-0 right-0 w-full sm:w-[480px] bg-white shadow-xl z-50 flex flex-col">
                    <!-- Sheet Header -->
                    <div class="flex items-center justify-between p-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">Solicitud a {{ profile.fullName }}</h2>
                        <button @click="isOpen = false" class="p-1 rounded-full hover:bg-gray-100 transition-colors"
                            aria-label="Close">
                            <XIcon class="w-5 h-5 text-gray-500 cursor-pointer" />
                        </button>
                    </div>

                    <!-- Form Content -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <ErrorAlert :error="formError" />
                            <!-- Title Field -->
                            <div class="space-y-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Título de la tarea
                                </label>
                                <input id="title" v-model="form.title" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="Ingrese un título" />
                            </div>

                            <!-- Description Field -->
                            <div class="space-y-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Descripción
                                </label>
                                <textarea id="description" v-model="form.description" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="Ingrese una descripción detallada"></textarea>
                            </div>

                            <!-- Tag Toggle -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Etiquetas
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button v-for="tag in availableTags" :key="tag.id" type="button"
                                        @click="toggleTag(tag.id)" :class="[
                                            'px-3 py-1 text-sm rounded-full transition-colors cursor-pointer',
                                            isTagSelected(tag.id)
                                                ? 'bg-emerald-100 text-emerald-800 border-emerald-200 border'
                                                : 'bg-gray-100 text-gray-800 border-gray-200 border hover:bg-gray-200'
                                        ]">
                                        {{ tag.name }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Form Actions -->
                    <div class="p-4 border-t flex justify-end gap-3">
                        <button @click="isOpen = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <PrimaryButton @click="handleSubmit" :disabled="isSubmitting"
                            class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <LoaderIcon v-if="isSubmitting" class="w-4 h-4 animate-spin" />
                            <span>{{ isSubmitting ? 'Enviando...' : 'Guardar' }}</span>
                        </PrimaryButton>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { XIcon, PlusIcon, LoaderIcon, Send } from 'lucide-vue-next';
import PrimaryButton from '@/components/PrimaryButton.vue';
import { postTaskApplication } from '@/services/task-applications';
import { useRouter } from 'vue-router';
import ErrorAlert from '@/components/ErrorAlert.vue';

const router = useRouter();

const props = defineProps({
    profile: {
        type: Object,
        required: true
    },
    buttonText: {
        type: String,
        default: 'Abrir formulario'
    },
    initialData: {
        type: Object,
        default: () => ({
            title: '',
            description: '',
            tagIds: []
        })
    },
    availableTags: {
        type: Array,
        default: () => []
    }
});

const isOpen = ref(false);
const isSubmitting = ref(false);
const formError = ref(null);

const form = reactive({
    title: props.initialData.title,
    description: props.initialData.description,
    tagIds: [...props.initialData.tagIds]
});

const toggleTag = (tagId) => {
    const index = form.tagIds.indexOf(tagId);
    if (index === -1) {
        form.tagIds.push(tagId);
    } else {
        form.tagIds.splice(index, 1);
    }
};

const isTagSelected = (tagId) => {
    return form.tagIds.includes(tagId);
};

const handleSubmit = async () => {
    formError.value = null;
    isSubmitting.value = true;

    try {
        const response = await postTaskApplication({
            title: form.title,
            description: form.description,
            tags: form.tagIds,
            required_id: props.profile.user.id
        });

        router.push({ name: 'tasks' });
    } catch (err) {
        formError.value = err;
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<style scoped>
.overlay-enter-active,
.overlay-leave-active {
    transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
    opacity: 0;
}

.sheet-enter-active,
.sheet-leave-active {
    transition: transform 0.3s ease;
}

.sheet-enter-from,
.sheet-leave-to {
    transform: translateX(100%);
}
</style>