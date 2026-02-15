<script setup lang="ts">
import { ref, watch } from 'vue'
import { Check, X } from 'lucide-vue-next'

interface Props {
  show: boolean
  message: string
  type?: 'success' | 'error'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'success'
})

const emit = defineEmits<{
  (e: 'update:show', value: boolean): void
}>()

const visible = ref(props.show)

watch(() => props.show, (newValue) => {
  visible.value = newValue
  if (newValue) {
    setTimeout(() => {
      visible.value = false
      emit('update:show', false)
    }, 3000)
  }
})
</script>

<template>
  <Transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="translate-y-2 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-2 opacity-0"
  >
    <div
      v-if="visible"
      class="fixed bottom-4 right-4 z-50 flex items-center gap-3 rounded-lg border bg-white px-4 py-3 shadow-lg"
      :class="{
        'border-green-200 bg-green-50': type === 'success',
        'border-red-200 bg-red-50': type === 'error'
      }"
    >
      <div
        class="flex h-5 w-5 items-center justify-center rounded-full"
        :class="{
          'bg-green-500': type === 'success',
          'bg-red-500': type === 'error'
        }"
      >
        <Check v-if="type === 'success'" class="h-3 w-3 text-white" />
        <X v-else class="h-3 w-3 text-white" />
      </div>
      <p
        class="text-sm font-medium"
        :class="{
          'text-green-900': type === 'success',
          'text-red-900': type === 'error'
        }"
      >
        {{ message }}
      </p>
    </div>
  </Transition>
</template>
