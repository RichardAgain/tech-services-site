import axios from "axios"
import { useAuthStore } from "@/stores/authorization"


const postTaskApplication = (application) => {
    const authStore = useAuthStore()

    return axios.post('/api/task-applications', application, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    .then(res => res.data)
    .catch(err => {
        console.error("Error posting task application:", err.response.data)
        throw err.response.data.message
    })
}

export { postTaskApplication }
