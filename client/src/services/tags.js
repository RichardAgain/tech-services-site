import axios from "axios"

const getAvaliableTags = async () => 
    await axios.get("/api/tags")
        .then((response) => response.data)
        .catch((error) => {
            console.error("Error fetching tags:", error)
            throw error
        })

export { getAvaliableTags }
