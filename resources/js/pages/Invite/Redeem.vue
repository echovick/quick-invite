<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Loader2 } from 'lucide-vue-next'
import invite from '@/routes/invite'

interface Event {
  id: number
  title: string
  address: string
  event_time: string
}

interface Invite {
  id: number
  token: string
  table_number: string
}

const props = defineProps<{
  event: Event
  invite: Invite
}>()

const form = useForm({
  name: '',
  phone: '',
  has_plus_one: false,
})

const submit = () => {
  form.post(invite.redeem.url({ token: props.invite.token }))
}

const getOrdinalSuffix = (day: number) => {
  if (day > 3 && day < 21) return 'th'
  switch (day % 10) {
    case 1: return 'st'
    case 2: return 'nd'
    case 3: return 'rd'
    default: return 'th'
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  const dayOfWeek = date.toLocaleDateString('en-US', { weekday: 'long' })
  const day = date.getDate()
  const month = date.toLocaleDateString('en-US', { month: 'long' })
  const year = date.getFullYear()
  const time = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })

  return `${dayOfWeek}, ${day}${getOrdinalSuffix(day)} ${month}, ${year} at ${time}`
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4">
    <Card class="w-full max-w-lg">
      <CardHeader>
        <CardTitle>You're Invited!</CardTitle>
        <CardDescription>
          {{ event.title }}
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-6">
        <div class="rounded-lg bg-blue-50 p-4">
          <p class="text-sm font-medium text-blue-900">Event Details</p>
          <p class="mt-1 text-sm text-blue-800">{{ event.address }}</p>
          <p class="mt-1 text-sm text-blue-800">{{ formatDate(event.event_time) }}</p>
          <p class="mt-2 text-sm font-medium text-blue-900">
            Table Number: {{ invite.table_number }}
          </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="name">Full Name</Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Enter your full name"
              required
              autofocus
            />
            <p v-if="form.errors.name" class="text-sm text-red-600">
              {{ form.errors.name }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="phone">Phone Number</Label>
            <Input
              id="phone"
              v-model="form.phone"
              type="tel"
              placeholder="Enter your phone number"
              required
            />
            <p v-if="form.errors.phone" class="text-sm text-red-600">
              {{ form.errors.phone }}
            </p>
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox
              id="plus_one"
              v-model:checked="form.has_plus_one"
            />
            <Label for="plus_one" class="cursor-pointer">
              I will be bringing a plus one
            </Label>
          </div>

          <p v-if="form.errors.error" class="text-sm text-red-600">
            {{ form.errors.error }}
          </p>

          <Button type="submit" class="w-full" :disabled="form.processing">
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            {{ form.processing ? 'Confirming...' : 'Confirm RSVP' }}
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
