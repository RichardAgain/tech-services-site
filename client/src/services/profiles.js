import axios from "axios";

const fetchProfiles = () => 
    axios.get("/api/profiles")
    .then((response) => response.data.data)

const fetchProfile = (id) =>
    axios.get(`/api/profiles/${id}`)
    .then((response) => response.data)

export { fetchProfiles, fetchProfile };
