<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useTasksStore } from '../stores/tasks'
import TaskForm from '../components/TaskForm.vue'
import TaskCard from '../components/TaskCard.vue'
import ReminderModal from '../components/ReminderModal.vue'

const auth = useAuthStore()
const tasks = useTasksStore()
const router = useRouter()

const editingId = ref(null)
const reminderTaskId = ref(null)
const togglingId = ref(null)
const listError = ref('')

const reminderTask = computed(() => tasks.tasks.find((t) => t.id === reminderTaskId.value) || null)

onMounted(() => {
  tasks.fetchTasks()
})

async function handleCreate(payload) {
  await tasks.addTask(payload)
}

async function handleEditSave(id, payload) {
  await tasks.updateTask(id, payload)
  editingId.value = null
}

async function toggleStatus(task) {
  listError.value = ''
  togglingId.value = task.id
  const nextStatus = task.status === 'completed' ? 'pending' : 'completed'
  try {
    await tasks.updateTask(task.id, { status: nextStatus })
  } catch (err) {
    listError.value = err?.response?.data?.message || 'Не удалось изменить статус задачи.'
  } finally {
    togglingId.value = null
  }
}

async function handleDelete(task) {
  listError.value = ''
  try {
    await tasks.deleteTask(task.id)
  } catch (err) {
    listError.value = err?.response?.data?.message || 'Не удалось удалить задачу.'
  }
}

async function handleLogout() {
  await auth.logout()
  router.replace({ name: 'login' })
}
</script>

<template>
  <div class="tasks-shell">
    <header class="tasks-header">
      <div>
        <p class="tasks-kicker">Мои задачи</p>
        <h1>{{ auth.user ? `Привет, ${auth.user.name}` : 'Задачи' }}</h1>
      </div>
      <button class="btn btn-ghost" type="button" @click="handleLogout">Выйти</button>
    </header>

    <section class="tasks-new">
      <TaskForm :initial="null" :on-submit="handleCreate" />
    </section>

    <p v-if="listError" class="form-error">{{ listError }}</p>

    <section class="tasks-list">
      <p v-if="tasks.loading" class="tasks-status">Загружаем задачи…</p>
      <p v-else-if="tasks.tasks.length === 0" class="tasks-status">
        Пока нет ни одной задачи — добавьте первую выше.
      </p>

      <template v-else>
        <div v-for="task in tasks.tasks" :key="task.id" class="tasks-list__item">
          <TaskForm
            v-if="editingId === task.id"
            :initial="task"
            :on-submit="(payload) => handleEditSave(task.id, payload)"
            @cancel="editingId = null"
          />
          <TaskCard
            v-else
            :task="task"
            :toggling="togglingId === task.id"
            @toggle-status="toggleStatus(task)"
            @edit="editingId = task.id"
            @delete="handleDelete(task)"
            @open-reminder="reminderTaskId = task.id"
          />
        </div>
      </template>
    </section>

    <ReminderModal
      v-if="reminderTask"
      :task="reminderTask"
      :on-save="(iso) => tasks.setReminder(reminderTask.id, iso)"
      :on-clear="() => tasks.deleteReminder(reminderTask.id)"
      @close="reminderTaskId = null"
    />
  </div>
</template>

<style scoped>
.tasks-shell {
  max-width: 640px;
  margin: 0 auto;
  padding: 40px 24px 80px;
}

.tasks-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 28px;
}

.tasks-kicker {
  margin: 0 0 4px;
  color: var(--gold);
  font-size: 13px;
  letter-spacing: 0.04em;
}

.tasks-header h1 {
  font-size: 26px;
}

.tasks-new {
  background: var(--ink-800);
  border: 1px solid var(--ink-700);
  border-radius: var(--radius-md);
  padding: 20px;
  margin-bottom: 28px;
}

.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.tasks-list__item .task-form {
  background: var(--ink-800);
  border: 1px solid var(--ink-700);
  border-radius: var(--radius-md);
  padding: 16px;
}

.tasks-status {
  color: var(--paper-dim);
  font-size: 14px;
  padding: 8px 2px;
}
</style>
