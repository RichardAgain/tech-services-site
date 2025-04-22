import { useAuthStore } from '@/stores/authotization'
import axios from 'axios'

const authGuard = async (to, from) => {
    const authStore = useAuthStore()

    if (!authStore.token) {
        return '/login'
    }

    try {
        await axios.get('/api/auth/user', {
            headers: {
                'Authorization': `Bearer ${authStore.token}`
            }
        })

        return true
    } catch (e) {
        return '/login'
    }
}

export { authGuard }