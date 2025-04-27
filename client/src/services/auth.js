import axios from "axios"

const register = (body) => 
    axios.post('/api/auth/register', body)
    .then(response => response.data)

const logIn = (body) => 
    axios.post('/api/auth/login', body)
    .then(response => response.data)


const logOut = () => {
    // logic to the api

    localStorage.removeItem('token')
    localStorage.removeItem('user')
    window.location.reload()
}

export default { register, logIn }
