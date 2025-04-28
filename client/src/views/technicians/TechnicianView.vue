<script setup>
import PageTitle from '@/components/PageTitle.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import Spinner from '@/components/Spinner.vue';
import TagCards from '@/components/TagCards.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import router from '@/router';
import { fetchProfile } from '@/services/profiles';
import { sleep } from '@/utils/sleep';
import { Paperclip, Send, SendHorizonal, StarIcon, UserIcon } from 'lucide-vue-next';
import { computed, onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import TechnicianRequestForm from './TechnicianRequestForm.vue';

const route = useRoute();
const profile = ref(null)
const reviews = ref([])

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

        profile.value = data.profile;
        reviews.value = data.reviews;
    } catch (error) {
        console.error('Error fetching profile:', error);
    }
})

const averageRating = computed(() => {
    if (!reviews.value || reviews.value.length === 0) return 0;
    const sum = reviews.value.reduce((acc, review) => acc + review.rating, 0);
    return sum / reviews.value.length;
});

// Calculate rating counts (how many reviews for each star rating)
const ratingCounts = computed(() => {
    const counts = [0, 0, 0, 0, 0]; // Index 0 = 1 star, index 4 = 5 stars

    if (!reviews.value || reviews.value.length === 0) return counts;

    reviews.value.forEach(review => {
        if (review.rating >= 1 && review.rating <= 5) {
            counts[review.rating - 1]++;
        }
    });

    return counts;
});

// Calculate rating percentages for the progress bars
const ratingPercentages = computed(() => {
    const percentages = [0, 0, 0, 0, 0]; // Index 0 = 1 star, index 4 = 5 stars

    if (!reviews.value || reviews.value.length === 0) return percentages;

    const totalReviews = reviews.value.length;

    ratingCounts.value.forEach((count, index) => {
        percentages[index] = (count / totalReviews) * 100;
    });

    return percentages;
});
</script>

<template>
    <MainLayout>
        <div v-if="!profile">
            <Spinner />
        </div>

        <div v-else>
            <!-- Page title and button -->
            <PageTitle :title="`Perfil de ${profile.firstName + ' ' + profile.lastName}`">
                <TechnicianRequestForm :profile="profile" :availableTags="profile.tags" />
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
                </div>

                <!-- Tags -->
                <TagCards :tags="profile.tags" />

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
                <!-- <div class="flex justify-end">
                    <button
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-md transition-colors">
                        Solicitar
                    </button>
                </div> -->
            </div>

            <!-- Rating Summary Card -->
            <div v-if="reviews && reviews.length > 0" class="bg-gray-50 rounded-lg p-4 px-8 mb-6 shadow-sm max-w-4xl mx-auto my-6">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <!-- Average Rating Display -->
                    <div class="text-center md:border-r md:border-gray-200 md:pr-6">
                        <div class="text-4xl font-bold text-gray-800">{{ averageRating.toFixed(1) }}</div>
                        <div class="flex justify-center my-1">
                            <StarIcon v-for="i in 5" :key="i" :class="[
                                'w-4 h-4',
                                i <= Math.round(averageRating) ? 'text-yellow-400' : 'text-gray-300'
                            ]" />
                        </div>
                        <div class="text-sm text-gray-500">{{ reviews.length }} reseñas</div>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="flex-1">
                        <div v-for="i in 5" :key="i" class="flex items-center mb-1 last:mb-0">
                            <div class="flex items-center w-16">
                                <span class="text-sm text-gray-600 mr-1">{{ 6 - i }}</span>
                                <StarIcon class="w-4 h-4 text-yellow-400" />
                            </div>

                            <div class="flex-1 h-4 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full"
                                    :style="{ width: `${ratingPercentages[5 - i]}%` }"></div>
                            </div>

                            <div class="w-12 text-right">
                                <span class="text-xs text-gray-500">{{ ratingCounts[5 - i] || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Reseñas</h3>

                <div v-if="reviews && reviews.length > 0">
                    <div v-for="(review, index) in reviews" :key="index"
                        class="mb-6 pb-6 border-b border-gray-100 last:border-b-0 last:pb-0 bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-start">
                            <div class="bg-gray-100 rounded-full p-2 mr-3">
                                <UserIcon class="w-6 h-6 text-gray-500" />
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                    <div>
                                        <h4 class="font-medium text-gray-800">
                                            {{ review.reviewer.firtName }} {{ review.reviewer.lastName }}
                                        </h4>
                                        <p class="text-xs text-gray-500">@{{ review.reviewer.username }}</p>
                                    </div>

                                    <div class="flex items-center mt-1 sm:mt-0">
                                        <div class="flex mr-2">
                                            <StarIcon v-for="i in 5" :key="i" :class="[
                                                'w-4 h-4',
                                                i <= review.rating ? 'text-yellow-400' : 'text-gray-300'
                                            ]" />
                                        </div>
                                        <span class="text-sm text-gray-600">{{ review.created_at }}</span>
                                    </div>
                                </div>

                                <h5 class="font-medium text-gray-800 mb-1">{{ review.title }}</h5>
                                <p class="text-gray-600 text-sm">{{ review.content }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                    <MessageSquareIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-500">No hay reseñas disponibles</p>
                </div>
            </div>
        </div>


    </MainLayout>
</template>