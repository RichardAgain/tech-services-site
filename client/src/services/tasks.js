import axios from "axios"
import { useAuthStore } from "@/stores/authorization"

const fetchUserTasks = () => {
    const authStore = useAuthStore()

    return axios.get('api/tasks', {
        headers: { "Authorization": `Bearer ${authStore.token}` }
    })
    .then(response => response.data)
    .catch((error) => {
        console.error("Error fetching tasks:", error);
        throw error
    })
}

export { fetchUserTasks }
