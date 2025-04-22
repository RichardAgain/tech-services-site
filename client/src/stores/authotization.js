import { defineStore } from "pinia"
import { ref } from "vue"

export const useAuthStore = defineStore('auth', () => {
    const token = ref('')

    const user = ref({
        first_name: '',
        last_name: '',
        email: '',
    })

    const setToken = (newToken) => {
        token.value = newToken.key
        user.value = newToken.user
    }
  
    return { token, user, setToken }
  })