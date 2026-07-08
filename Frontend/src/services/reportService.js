// src/services/reportService.js

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
  citasPorDia(fecha) {
    return api.get('/reportes/citas-por-dia', { params: { fecha } })
  },
  citasPorDoctor(params) {
    return api.get('/reportes/citas-por-doctor', { params })
  },
  pacientesRegistrados(params) {
    return api.get('/reportes/pacientes-registrados', { params })
  },
}