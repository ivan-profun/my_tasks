<script setup>
import { reactive, ref, watch } from 'vue'

const props = defineProps({
  initial: {
    type: Object,
    default: null,
  },
  submitting: {
    type: Boolean,
    default: false,
  },
  onSubmit: {
    type: Function,
    required: true,
  },
})

const emit = defineEmits(['cancel'])

const form = reactive({
  title: props.initial?.title ?? '',
  description: props.initial?.description ?? '',
})
const fieldErrors = ref({})
const generalError = ref('')

watch(
  () => props.initial,
  (val) => {
    form.title = val?.title ?? ''
    form.description = val?.description ?? ''
  },
)

function resetFields() {
  form.title = ''
  form.description = ''
}

async function handleSubmit() {
  fieldErrors.value = {}
  generalError.value = ''

  if (!form.title.trim()) {
    fieldErrors.value = { title: ['Название обязательно.'] }
    return
  }

  if (form.title.trim().length > 255) {
    fieldErrors.value = { title: ['Название не может быть длиннее 255 символов.'] }
    return
  }

  try {
    await props.onSubmit({ title: form.title.trim(), description: form.description.trim() || null })
    if (!props.initial) resetFields()
  } catch (err) {
    const data = err?.response?.data
    if (err?.response?.status === 422 && data?.errors) {
      fieldErrors.value = data.errors
    } else {
      generalError.value = data?.message || 'Не удалось сохранить задачу.'
    }
  }
}
</script>

<template>
  <form class="task-form" novalidate @submit.prevent="handleSubmit">
    <div class="field">
      <label :for="`title-${initial?.id ?? 'new'}`">Название</label>
      <input
        :id="`title-${initial?.id ?? 'new'}`"
        v-model="form.title"
        type="text"
        placeholder="Например, «Позвонить в клинику»"
        autocomplete="off"
      />
      <p v-if="fieldErrors.title" class="field-error">{{ fieldErrors.title[0] }}</p>
    </div>

    <div class="field">
      <label :for="`desc-${initial?.id ?? 'new'}`">Описание</label>
      <textarea
        :id="`desc-${initial?.id ?? 'new'}`"
        v-model="form.description"
        placeholder="Детальное описание. Необязательно"
      ></textarea>
      <p v-if="fieldErrors.description" class="field-error">{{ fieldErrors.description[0] }}</p>
    </div>

    <p v-if="generalError" class="form-error">{{ generalError }}</p>

    <div class="task-form__actions">
      <button class="btn btn-primary" type="submit" :disabled="submitting">
        {{ submitting ? 'Сохраняем…' : initial ? 'Сохранить' : 'Добавить задачу' }}
      </button>
      <button
        v-if="initial"
        class="btn btn-ghost"
        type="button"
        :disabled="submitting"
        @click="emit('cancel')"
      >
        Отмена
      </button>
    </div>
  </form>
</template>

<style scoped>
.task-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.task-form__actions {
  display: flex;
  gap: 10px;
}
</style>
