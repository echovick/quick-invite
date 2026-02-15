<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Loader2 } from 'lucide-vue-next'
import setup from '@/routes/setup'

const form = useForm({
  admin_password: '',
  title: '',
  address: '',
  event_time: '',
  rsvp_enabled: true,
})

const submit = () => {
  form.post(setup.store.url())
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4">
    <Card class="w-full max-w-lg">
      <CardHeader>
        <CardTitle>Event Setup</CardTitle>
        <CardDescription>
          Set up your event details and create an admin password
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="admin_password">Admin Password</Label>
            <Input
              id="admin_password"
              v-model="form.admin_password"
              type="password"
              placeholder="Enter admin password"
              required
            />
            <p v-if="form.errors.admin_password" class="text-sm text-red-600">
              {{ form.errors.admin_password }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="title">Event Title</Label>
            <Input
              id="title"
              v-model="form.title"
              type="text"
              placeholder="Enter event title"
              required
            />
            <p v-if="form.errors.title" class="text-sm text-red-600">
              {{ form.errors.title }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="address">Event Address</Label>
            <Input
              id="address"
              v-model="form.address"
              type="text"
              placeholder="Enter event address"
              required
            />
            <p v-if="form.errors.address" class="text-sm text-red-600">
              {{ form.errors.address }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="event_time">Event Time</Label>
            <Input
              id="event_time"
              v-model="form.event_time"
              type="datetime-local"
              required
            />
            <p v-if="form.errors.event_time" class="text-sm text-red-600">
              {{ form.errors.event_time }}
            </p>
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            {{ form.processing ? 'Setting up...' : 'Complete Setup' }}
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
