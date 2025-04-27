import { useAuthStore } from "@/stores/authorization"
import axios from "axios"

const register = (body) => 
    axios.post('/api/auth/register', body)
    .then(response => response.data)

const logIn = (body) => 
    axios.post('/api/auth/login', body)
    .then(response => response.data)


const logOut = () => 
    axios.post('/api/auth/logout')
    .then(response => response.data)    
   

export default { register, logIn, logOut }
