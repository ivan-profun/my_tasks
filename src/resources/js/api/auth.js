import http from './http'

function csrf() {
  return http.get('/sanctum/csrf-cookie')
}

async function register(payload) {
  await csrf()
  const { data } = await http.post('/api/register', payload)
  return data.user
}

async function login(payload) {
  await csrf()
  const { data } = await http.post('/api/login', payload)
  return data.user
}

async function logout() {
  await http.post('/api/logout')
}

async function me() {
  const { data } = await http.get('/api/user')
  return data.user
}

export default { register, login, logout, me }
