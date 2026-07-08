// src/services/authService.js

import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

export default {
  login(credentials) {
    return api.post('/login', credentials)
  },
  forgotPassword(email) {
    return api.post('/forgot-password', { email })
  },
  me() {
    return api.get('/me')
  },
}