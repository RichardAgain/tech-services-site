import axios from 'axios';
import { useAuthStore } from '@/stores/authorization';

const getProfileApplications = async () => {
    const authStore = useAuthStore();
    
    return await axios.get('/api/profile-applications', {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then((response) => {
        return response.data.data;
    })
    .catch((error) => {
        console.error("Error fetching profile applications:", error);
        throw error;
    });
}

const createProfileApplication = async (profileApplication) => {
    const authStore = useAuthStore();
    
    return await axios.post('/api/profile-applications', profileApplication, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then((response) => {
        return response.data;
    })
    .catch((error) => {
        console.error("Error creating profile application:", error);
        throw error;
    });
}

const acceptProfileApplication = async (profileApplicationId) => {
    const authStore = useAuthStore();
    
    return await axios.post(`/api/profile-applications/${profileApplicationId}/accept`, {}, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then((response) => {
        return response.data;
    })
    .catch((error) => {
        console.error("Error accepting profile application:", error);
        throw error;
    });
}

const rejectProfileApplication = async (profileApplicationId) => {
    const authStore = useAuthStore();
    
    return await axios.patch(`/api/profile-applications/${profileApplicationId}/reject`, {}, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then((response) => {
        return response.data;
    })
    .catch((error) => {
        console.error("Error rejecting profile application:", error);
        throw error;
    });
}

export { getProfileApplications, createProfileApplication, acceptProfileApplication, rejectProfileApplication };
