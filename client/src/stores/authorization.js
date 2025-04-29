import { UserRoles } from "@/utils/enums"
import { defineStore } from "pinia"
import { ref } from "vue"

export const useAuthStore = defineStore('auth', () => {
    const token = ref('')

    const user = ref({
        role: UserRoles.CLIENT,
        firstName: '',
        lastName: '',
        username: '',
        email: '',
    })

    const setToken = (newToken) => {
        token.value = newToken.token
        user.value = newToken.user
    }

    const $reset = () => {
        token.value = ''
        user.value = {
            role: UserRoles.CLIENT,
            firstName: '',
            lastName: '',
            username: '',
            email: '',
        }
    }
  
    return { token, user, setToken, $reset }
}, {
    persist: true
})
