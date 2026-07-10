// src/services/doctorService.js

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
    return api.get('/doctores', { params })
  },
  get(id) {
    return api.get(`/doctores/${id}`)
  },
  create(data) {
    return api.post('/doctores', data)
  },
  update(id, data) {
    return api.put(`/doctores/${id}`, data)
  },
  remove(id) {
    return api.delete(`/doctores/${id}`)
  },
  horarios(id, fecha) {
    return api.get(`/doctores/${id}/horarios`, { params: { fecha } })
  },
}