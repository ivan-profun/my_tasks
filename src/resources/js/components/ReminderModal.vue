<script setup>
import { ref } from 'vue'

const props = defineProps({
  task: { type: Object, required: true },
  onSave: { type: Function, required: true },
  onClear: { type: Function, required: true },
})
const emit = defineEmits(['close'])

function toLocalInputValue(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const value = ref(toLocalInputValue(props.task.reminder_at))
const error = ref('')
const saving = ref(false)
const clearing = ref(false)

async function save() {
  if (!value.value) {
    error.value = 'Выберите дату и время.'
    return
  }
  error.value = ''
  saving.value = true
  try {
    await props.onSave(new Date(value.value).toISOString())
    emit('close')
  } catch (err) {
    error.value = err?.response?.data?.message || 'Не удалось установить напоминание.'
  } finally {
    saving.value = false
  }
}

async function clear() {
  error.value = ''
  clearing.value = true
  try {
    await props.onClear()
    emit('close')
  } catch (err) {
    error.value = err?.response?.data?.message || 'Не удалось удалить напоминание.'
  } finally {
    clearing.value = false
  }
}
</script>

<template>
  <div class="overlay" @click.self="emit('close')">
    <div class="modal">
      <h3>Напоминание</h3>
      <p class="modal-subtitle">«{{ task.title }}»</p>

      <div class="field">
        <label for="reminder-at">Дата и время</label>
        <input id="reminder-at" v-model="value" type="datetime-local" />
      </div>

      <p v-if="error" class="form-error">{{ error }}</p>

      <div class="modal-actions">
        <button class="btn btn-primary" :disabled="saving || clearing" @click="save">
          {{ saving ? 'Сохраняется…' : 'Сохранить' }}
        </button>
        <button
          v-if="task.reminder_at"
          class="btn btn-danger"
          :disabled="saving || clearing"
          @click="clear"
        >
          {{ clearing ? 'Удаляется…' : 'Удалить напоминание' }}
        </button>
        <button class="btn btn-ghost" :disabled="saving || clearing" @click="emit('close')">
          Закрыть
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(10, 16, 19, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  z-index: 20;
}

.modal {
  width: 100%;
  max-width: 340px;
  background: var(--ink-800);
  border: 1px solid var(--ink-700);
  border-radius: var(--radius-md);
  padding: 24px;
}

.modal h3 {
  font-size: 20px;
}

.modal-subtitle {
  margin: 4px 0 18px;
  color: var(--paper-dim);
  font-size: 13.5px;
}

.modal-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 18px;
}
</style>
