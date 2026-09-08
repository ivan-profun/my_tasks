import http from './http'

async function fetchAll() {
  const { data } = await http.get('/api/tasks')
  return data.data
}

async function create(payload) {
  const { data } = await http.post('/api/tasks', payload)
  return data.data
}

async function update(id, payload) {
  const { data } = await http.put(`/api/tasks/${id}`, payload)
  return data.data
}

async function remove(id) {
  await http.delete(`/api/tasks/${id}`)
}

async function setReminder(id, reminderAt) {
  const { data } = await http.post(`/api/tasks/${id}/reminder`, {
    reminder_at: reminderAt,
  })
  return data.data
}

async function deleteReminder(id) {
  const { data } = await http.delete(`/api/tasks/${id}/reminder`)
  return data.data
}

export default { fetchAll, create, update, remove, setReminder, deleteReminder }
