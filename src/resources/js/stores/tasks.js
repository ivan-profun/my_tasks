import { defineStore } from 'pinia'
import tasksApi from '../api/tasks'

export const useTasksStore = defineStore('tasks', {
  state: () => ({
    tasks: [],
    loading: false,
  }),

  actions: {
    replace(task) {
      const idx = this.tasks.findIndex((t) => t.id === task.id)
      if (idx !== -1) this.tasks.splice(idx, 1, task)
    },

    async fetchTasks() {
      this.loading = true
      try {
        this.tasks = await tasksApi.fetchAll()
      } finally {
        this.loading = false
      }
    },

    async addTask(payload) {
      const task = await tasksApi.create(payload)
      this.tasks.unshift(task)
      return task
    },

    async updateTask(id, payload) {
      const task = await tasksApi.update(id, payload)
      this.replace(task)
      return task
    },

    async deleteTask(id) {
      await tasksApi.remove(id)
      this.tasks = this.tasks.filter((t) => t.id !== id)
    },

    async setReminder(id, reminderAt) {
      const task = await tasksApi.setReminder(id, reminderAt)
      this.replace(task)
      return task
    },

    async deleteReminder(id) {
      const task = await tasksApi.deleteReminder(id)
      this.replace(task)
      return task
    },
  },
})
