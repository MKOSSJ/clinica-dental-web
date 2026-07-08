// src/services/patientService.js

import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

export default {
  list(params) {
    return api.get('/pacientes', { params })
  },
  get(id) {
    return api.get(`/pacientes/${id}`)
  },
  create(data) {
    return api.post('/pacientes', data)
  },
  update(id, data) {
    return api.put(`/pacientes/${id}`, data)
  },
  remove(id) {
    return api.delete(`/pacientes/${id}`)
  },
}