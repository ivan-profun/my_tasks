<script setup>
import { computed } from 'vue'

const props = defineProps({
  task: { type: Object, required: true },
  toggling: { type: Boolean, default: false },
})

const emit = defineEmits(['toggle-status', 'edit', 'delete', 'open-reminder'])

const isCompleted = computed(() => props.task.status === 'completed')

const shortDescription = computed(() => {
  const text = props.task.description || ''
  if (text.length <= 50) return text
  return text.slice(0, 50).trimEnd() + '…'
})

const reminderLabel = computed(() => {
  if (!props.task.reminder_at) return null
  const d = new Date(props.task.reminder_at)
  return d.toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})
</script>

<template>
  <article class="task-card" :class="{ 'task-card--done': isCompleted }">
    <button
      class="task-toggle"
      type="button"
      :disabled="toggling"
      :aria-pressed="isCompleted"
      :title="isCompleted ? 'Отметить как не выполненную' : 'Отметить как выполненную'"
      @click="emit('toggle-status')"
    >
      <span class="task-toggle__dot" />
    </button>

    <div class="task-body">
      <button class="task-title" type="button" @click="emit('edit')">
        {{ task.title }}
      </button>
      <p v-if="shortDescription" class="task-description">{{ shortDescription }}</p>

      <div class="task-meta">
        <button
          class="reminder-button"
          type="button"
          :disabled="isCompleted"
          :title="isCompleted ? 'Недоступно для завершённых задач' : ''"
          @click="emit('open-reminder')"
        >
          {{ task.reminder_at ? 'Изменить напоминание' : 'Напомнить' }}
        </button>
        <span v-if="reminderLabel" class="reminder-date">Напоминание: {{ reminderLabel }}</span>
      </div>
    </div>

    <div class="task-footer">
      <button class="task-delete" type="button" @click="emit('delete')">Удалить</button>
    </div>
  </article>
</template>

<style scoped>
.task-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 16px;
  background: var(--ink-800);
  border: 1px solid var(--ink-700);
  border-radius: var(--radius-md);
}

.task-card--done {
  opacity: 0.6;
}

.task-toggle {
  flex-shrink: 0;
  width: 22px;
  height: 22px;
  margin-top: 2px;
  border-radius: 50%;
  border: 1.5px solid var(--paper-dim);
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
}

.task-card--done .task-toggle {
  border-color: var(--sage);
  background: var(--sage);
}

.task-toggle__dot {
  width: 8px;
  height: 8px;
}

.task-body {
  flex: 1;
  min-width: 0;
}

.task-title {
  display: block;
  background: none;
  border: none;
  padding: 0;
  text-align: left;
  font-family: var(--font-display);
  font-size: 17px;
  color: var(--paper);
}

.task-card--done .task-title {
  text-decoration: line-through;
  color: var(--paper-dim);
}

.task-title:hover {
  color: var(--gold);
}

.task-description {
  margin: 6px 0 0;
  color: var(--paper-dim);
  font-size: 13.5px;
}

.task-meta {
  margin-top: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.reminder-date {
  color: var(--gold);
  font-size: 12.5px;
}

.reminder-button {
  border: 1px solid var(--ink-600);
  background: transparent;
  color: var(--paper-dim);
  border-radius: 50px;
  padding: 4px 12px;
  font-size: 12.5px;
}

.reminder-button:hover:not(:disabled) {
  border-color: var(--gold-dim);
  color: var(--gold);
}

.reminder-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.task-footer {
  margin-top: auto;
  display: flex;
  justify-content: flex-end;
}

.task-delete {
  background: none;
  border: 1px solid var(--ink-600);
  color: var(--paper-dim);
  border-radius: 50px;
  padding: 4px 12px;
  font-size: 12.5px;
}

.task-delete:hover {
  border-color: var(--rose);
  color: var(--rose);
}
</style>