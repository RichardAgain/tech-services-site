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

const updateUserTask = (taskId, status) => {
    return axios.patch(`api/tasks/${taskId}`, { status }, {
        headers: { "Authorization": `Bearer ${useAuthStore().token}` }
    })
    .then(response => response.data)
    .catch((error) => {
        console.error("Error updating task:", error);
        throw error
    })
}

export { fetchUserTasks, updateUserTask }
