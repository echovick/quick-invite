<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Loader2 } from 'lucide-vue-next'
import admin from '@/routes/admin'

const form = useForm({
  password: '',
})

const submit = () => {
  form.post(admin.login.post.url())
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle>Admin Login</CardTitle>
        <CardDescription>
          Enter your admin password to access the dashboard
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="password">Password</Label>
            <Input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="Enter admin password"
              required
              autofocus
            />
            <p v-if="form.errors.password" class="text-sm text-red-600">
              {{ form.errors.password }}
            </p>
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            {{ form.processing ? 'Logging in...' : 'Login' }}
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
