// src/services/appointmentService.js

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
  list(params) {
    return api.get('/citas', { params })
  },
  create(data) {
    return api.post('/citas', data)
  },
  get(id) {
    return api.get(`/citas/${id}`)
  },
  cancelar(id) {
    return api.put(`/citas/${id}/cancelar`)
  },
  reagendar(id, appointment_date) {
    return api.put(`/citas/${id}/reagendar`, { appointment_date })
  },
  historial(patientId) {
    return api.get(`/citas/historial/${patientId}`)
  },
  notificar(id) {
  return api.post(`/citas/${id}/notificar`)
  },
}