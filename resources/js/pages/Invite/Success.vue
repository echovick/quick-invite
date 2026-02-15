<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Check } from 'lucide-vue-next'
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
  invitee_name: string
  has_plus_one: boolean
}

const props = defineProps<{
  event: Event
  invite: Invite
}>()

console.log('Success page props:', {
  invite: props.invite,
  event: props.event
})

const downloadPass = () => {
  window.location.href = invite.download.url({ token: props.invite.token })
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
        <div class="flex items-center gap-2">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
            <Check class="h-6 w-6 text-green-600" />
          </div>
          <div>
            <CardTitle>RSVP Confirmed!</CardTitle>
            <CardDescription>Your spot has been reserved</CardDescription>
          </div>
        </div>
      </CardHeader>
      <CardContent class="space-y-6">
        <div class="rounded-lg bg-green-50 p-4">
          <p class="text-sm font-medium text-green-900">Welcome, {{ props.invite.invitee_name }}!</p>
          <p class="mt-2 text-sm text-green-800">
            You're confirmed for {{ props.event.title }}
          </p>
          <p class="mt-1 text-sm text-green-800">{{ props.event.address }}</p>
          <p class="mt-1 text-sm text-green-800">{{ formatDate(props.event.event_time) }}</p>
        </div>

        <div class="rounded-lg border-2 border-blue-500 bg-blue-50 p-6 text-center">
          <p class="text-sm font-medium text-blue-900">Your Table Number</p>
          <p class="mt-2 text-5xl font-bold text-blue-600">{{ props.invite.table_number }}</p>
          <p v-if="props.invite.has_plus_one" class="mt-2 text-sm text-blue-800">
            + 1 Guest
          </p>
        </div>

        <div class="space-y-2">
          <Button @click="downloadPass" class="w-full" size="lg">
            Download Event Pass (PDF)
          </Button>
          <p class="text-center text-xs text-gray-500">
            Save this pass to show at the event entrance
          </p>
        </div>

        <div class="rounded-lg bg-yellow-50 p-4">
          <p class="text-xs text-yellow-800">
            <strong>Important:</strong> Please arrive 15 minutes before the event starts.
            Bring your event pass (digital or printed) for quick check-in.
          </p>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
