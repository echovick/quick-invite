<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'

interface Props {
  show: boolean
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  variant?: 'default' | 'destructive'
}

const props = withDefaults(defineProps<Props>(), {
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  variant: 'default'
})

const emit = defineEmits<{
  confirm: []
  cancel: []
  'update:show': [value: boolean]
}>()

const handleConfirm = () => {
  emit('confirm')
  emit('update:show', false)
}

const handleCancel = () => {
  emit('cancel')
  emit('update:show', false)
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" @click="handleCancel"></div>
    <div class="relative bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
      <h2 class="text-xl font-semibold mb-2">{{ title }}</h2>
      <p class="text-gray-600 mb-6">{{ message }}</p>
      <div class="flex gap-3 justify-end">
        <Button variant="outline" @click="handleCancel">
          {{ cancelText }}
        </Button>
        <Button :variant="variant" @click="handleConfirm">
          {{ confirmText }}
        </Button>
      </div>
    </div>
  </div>
</template>
