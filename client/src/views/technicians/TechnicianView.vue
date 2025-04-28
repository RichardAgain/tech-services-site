<script setup>
import PageTitle from '@/components/PageTitle.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import Spinner from '@/components/Spinner.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { fetchProfile } from '@/services/profiles';
import { sleep } from '@/utils/sleep';
import { computed, onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const profile = ref(null)

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
};

// Render markdown description
// const renderedDescription = computed(() => {
//   return marked(profile.description || '');
// });

onBeforeMount(async () => {
    try {
        await sleep(1000); // Simulate a delay

        const data = await fetchProfile(route.params.id);

        console.log(data.data)
        profile.value = data.data;
    } catch (error) {
        console.error('Error fetching profile:', error);
    }
})

</script>

<template>
    <MainLayout>
        <div v-if="!profile">
            <Spinner />
        </div>

        <div v-else>
            <!-- Page title and button -->
            <PageTitle :title="`Perfil de ${profile.firstName + ' ' + profile.lastName}`">
                <PrimaryButton onClick=""> Solicitar </PrimaryButton>
            </PageTitle>

            <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto my-6">
                <!-- Header with name and rating -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">
                            {{ profile.firstName }} {{ profile.lastName }}
                        </h2>
                        <p class="text-sm text-gray-500">@{{ profile.user.username }}</p>
                    </div>

                    <div class="flex items-center mt-2 md:mt-0">
                        <div class="flex mr-2">
                            <StarIcon v-for="i in 5" :key="i" :class="[
                                'w-5 h-5',
                                i <= profile.rating ? 'text-yellow-400' : 'text-gray-300'
                            ]" />
                        </div>
                        <span class="text-gray-700">{{ profile.rating }}/5</span>
                    </div>
                </div>

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <span v-for="tag in profile.tags" :key="tag.id"
                        class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded-full">
                        {{ tag.name }}
                    </span>
                </div>

                <!-- Contact information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <MailIcon class="w-5 h-5 text-gray-500 mt-0.5 mr-2" />
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-gray-800">{{ profile.email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <PhoneIcon class="w-5 h-5 text-gray-500 mt-0.5 mr-2" />
                            <div>
                                <p class="text-sm text-gray-500">Teléfono</p>
                                <p class="text-gray-800">{{ profile.phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <MapPinIcon class="w-5 h-5 text-gray-500 mt-0.5 mr-2" />
                            <div>
                                <p class="text-sm text-gray-500">Dirección</p>
                                <p class="text-gray-800 whitespace-pre-line">{{ profile.address }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <CalendarIcon class="w-5 h-5 text-gray-500 mt-0.5 mr-2" />
                            <div>
                                <p class="text-sm text-gray-500">Técnico desde</p>
                                <p class="text-gray-800">{{ formatDate(profile.verifiedAt) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Descripción</h3>
                    <div class="prose prose-sm max-w-none text-gray-700" v-html="profile.description"></div>
                </div>

                <!-- Action button -->
                <div class="flex justify-end">
                    <button
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition-colors">
                        Solicitar
                    </button>
                </div>
            </div>
        </div>

    </MainLayout>
</template>