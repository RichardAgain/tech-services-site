import axios from "axios"
import { useAuthStore } from "@/stores/authorization"


const postTaskApplication = (application) => {
    const authStore = useAuthStore()

    return axios.post('/api/task-applications', application, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then(res => res.data)
    .catch(err => {
        console.error("Error posting task application:", err)
        throw err.response.data.message
    })
}

const acceptTaskApplication = (applicationId) => {
    const authStore = useAuthStore()

    return axios.post(`/api/task-applications/${applicationId}/accept`, {}, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then(res => res.data)
    .catch(err => {
        console.error("Error accepting task application:", err.response.data)
        throw err.response.data.message
    })
}

const rejectTaskApplication = (applicationId) => {
    const authStore = useAuthStore()

    return axios.patch(`/api/task-applications/${applicationId}/reject`, {}, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then(res => res.data)
    .catch(err => {
        console.error("Error rejecting task application:", err.response.data)
        throw err.response.data.message
    })
}

export { postTaskApplication, acceptTaskApplication, rejectTaskApplication }
