<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Loader2 } from 'lucide-vue-next'

interface Props {
  show: boolean
  processing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  processing: false
})

const emit = defineEmits<{
  submit: [data: { name: string; phone: string; has_plus_one: boolean }]
  cancel: []
  'update:show': [value: boolean]
}>()

const name = ref('')
const phone = ref('')
const hasPlusOne = ref(false)
const errors = ref<Record<string, string>>({})

watch(() => props.show, (newVal) => {
  if (!newVal) {
    // Reset form when modal closes
    name.value = ''
    phone.value = ''
    hasPlusOne.value = false
    errors.value = {}
  }
})

const handleSubmit = () => {
  errors.value = {}

  if (!name.value) {
    errors.value.name = 'Name is required'
    return
  }

  if (!phone.value) {
    errors.value.phone = 'Phone is required'
    return
  }

  emit('submit', {
    name: name.value,
    phone: phone.value,
    has_plus_one: hasPlusOne.value
  })
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
      <h2 class="text-xl font-semibold mb-4">Assign Reserved Table</h2>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="space-y-2">
          <Label for="assign-name">Name</Label>
          <Input
            id="assign-name"
            v-model="name"
            type="text"
            placeholder="Enter guest name"
            :disabled="processing"
          />
          <p v-if="errors.name" class="text-sm text-red-600">{{ errors.name }}</p>
        </div>

        <div class="space-y-2">
          <Label for="assign-phone">Phone Number</Label>
          <Input
            id="assign-phone"
            v-model="phone"
            type="tel"
            placeholder="Enter phone number"
            :disabled="processing"
          />
          <p v-if="errors.phone" class="text-sm text-red-600">{{ errors.phone }}</p>
        </div>

        <div class="flex items-center space-x-2">
          <input
            id="assign-plus-one"
            v-model="hasPlusOne"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300"
            :disabled="processing"
          />
          <Label for="assign-plus-one" class="cursor-pointer">Guest has a plus one</Label>
        </div>

        <div class="flex gap-3 justify-end pt-2">
          <Button type="button" variant="outline" @click="handleCancel" :disabled="processing">
            Cancel
          </Button>
          <Button type="submit" :disabled="processing">
            <Loader2 v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
            {{ processing ? 'Generating...' : 'Generate Pass' }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>
