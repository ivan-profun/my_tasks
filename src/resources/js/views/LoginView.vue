<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const form = reactive({ email: '', password: '' })
const fieldErrors = ref({})
const generalError = ref('')
const submitting = ref(false)

async function onSubmit() {
  fieldErrors.value = {}
  generalError.value = ''
  submitting.value = true
  try {
    await auth.login(form)
    router.replace(route.query.redirect || { name: 'tasks' })
  } catch (err) {
    const data = err?.response?.data
    if (err?.response?.status === 422 && data?.errors) {
      fieldErrors.value = data.errors
    } else {
      generalError.value = data?.message || 'Не удалось войти. Попробуйте ещё раз.'
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="auth-shell">
    <div class="auth-card">
      <p class="auth-kicker">Мои задачи</p>
      <h1>С возвращением</h1>

      <form class="auth-form" novalidate @submit.prevent="onSubmit">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="email" required />
          <p v-if="fieldErrors.email" class="field-error">{{ fieldErrors.email[0] }}</p>
        </div>

        <div class="field">
          <label for="password">Пароль</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
          />
          <p v-if="fieldErrors.password" class="field-error">{{ fieldErrors.password[0] }}</p>
        </div>

        <p v-if="generalError" class="form-error">{{ generalError }}</p>

        <button class="btn btn-primary" type="submit" :disabled="submitting">
          {{ submitting ? 'Входим…' : 'Войти' }}
        </button>
      </form>

      <p class="auth-switch">
        Нет аккаунта?
        <RouterLink :to="{ name: 'register' }">Зарегистрироваться</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.auth-shell {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

.auth-card {
  width: 100%;
  max-width: 380px;
  background: var(--ink-800);
  border: 1px solid var(--ink-700);
  border-radius: var(--radius-md);
  padding: 36px 32px;
}

.auth-kicker {
  margin: 0 0 4px;
  color: var(--gold);
  font-size: 13px;
  letter-spacing: 0.04em;
}

.auth-card h1 {
  font-size: 26px;
  margin-bottom: 24px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.auth-form .btn {
  margin-top: 4px;
}

.auth-switch {
  margin: 24px 0 0;
  font-size: 13.5px;
  color: var(--paper-dim);
  text-align: center;
}
</style>
