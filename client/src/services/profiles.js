import axios from "axios";

const fetchProfiles = (tags = "") => 
    axios.get(`/api/profiles?tags=${tags}`)
    .then((response) => response.data)

const fetchProfile = (id) =>
    axios.get(`/api/profiles/${id}`)
    .then((response) => response.data)

export { fetchProfiles, fetchProfile };
