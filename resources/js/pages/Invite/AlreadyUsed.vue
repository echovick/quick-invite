<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { AlertCircle } from 'lucide-vue-next'
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4">
    <Card class="w-full max-w-lg">
      <CardHeader>
        <div class="flex items-center gap-2">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100">
            <AlertCircle class="h-6 w-6 text-orange-600" />
          </div>
          <div>
            <CardTitle>Invite Already Used</CardTitle>
            <CardDescription>This invitation has already been redeemed</CardDescription>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div class="rounded-lg bg-blue-50 p-4">
          <p class="text-sm text-blue-800">
            <strong>Note:</strong> If you didn't redeem this invite, please contact the event organizer immediately.
          </p>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
