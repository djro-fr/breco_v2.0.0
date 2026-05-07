// frontend/breco/src/shared/api/axiosInstance.ts

import axios from 'axios'
import type { AxiosInstance } from 'axios'

const getApiUrl = () => {
  return import.meta.env.VITE_API_URL || 'http://localhost:8081/api'
}
const apiUrl = getApiUrl()
//console.log('✅ API URL:', apiUrl)

const axiosInstance: AxiosInstance = axios.create({
  baseURL: apiUrl,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 10000,
})

// Adds token
axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

// Handle errors
axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && localStorage.getItem('token')) {
      // Token expired, disconnect
      localStorage.removeItem('token')
      globalThis.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

export default axiosInstance
