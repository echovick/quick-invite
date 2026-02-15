<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Loader2 } from 'lucide-vue-next'
import Toast from '@/components/Toast.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'
import AssignModal from '@/components/AssignModal.vue'
import admin from '@/routes/admin'
import invite from '@/routes/invite'

interface Invite {
  id: number
  token: string
  table_number: string
  is_used: boolean
  is_reserved: boolean
  invitee_name: string | null
  invitee_phone: string | null
  has_plus_one: boolean
  used_at: string | null
  created_at: string
}

interface Event {
  id: number
  title: string
  address: string
  event_time: string
  rsvp_enabled: boolean
}

interface Stats {
  total: number
  used: number
  remaining: number
  reserved: number
}

const props = defineProps<{
  event: Event
  invites: Invite[]
  stats: Stats
}>()

const showCreateForm = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const showRevokeModal = ref(false)
const showReserveModal = ref(false)
const showAssignModal = ref(false)
const selectedInviteId = ref<number | null>(null)
const assignProcessing = ref(false)

const form = useForm({
  count: 1,
  table_start: 1,
  reserve_tables: false,
  reserved_count: 0,
})

const submit = () => {
  form.post(admin.invites.create.url(), {
    onSuccess: () => {
      form.reset()
      showCreateForm.value = false
    },
  })
}

const logout = () => {
  router.post(admin.logout.url())
}

const copyInviteLink = (token: string) => {
  const url = invite.show.url({ token })
  navigator.clipboard.writeText(window.location.origin + url)
  toastMessage.value = 'Invite link copied to clipboard!'
  showToast.value = true
}

const revokeInvite = (inviteId: number) => {
  selectedInviteId.value = inviteId
  showRevokeModal.value = true
}

const confirmRevoke = () => {
  if (selectedInviteId.value) {
    router.post(admin.invites.revoke.url({ invite: selectedInviteId.value }), {
      onSuccess: () => {
        toastMessage.value = 'Invite revoked successfully!'
        showToast.value = true
        selectedInviteId.value = null
      }
    })
  }
}

const reserveTable = (inviteId: number) => {
  selectedInviteId.value = inviteId
  showReserveModal.value = true
}

const confirmReserve = () => {
  if (selectedInviteId.value) {
    router.post(admin.invites.reserve.url({ invite: selectedInviteId.value }), {
      onSuccess: () => {
        toastMessage.value = 'Table reserved successfully!'
        showToast.value = true
        selectedInviteId.value = null
      }
    })
  }
}

const assignReservedTable = (inviteId: number) => {
  selectedInviteId.value = inviteId
  showAssignModal.value = true
}

const handleAssignSubmit = (data: { name: string; phone: string; has_plus_one: boolean }) => {
  if (!selectedInviteId.value) return

  assignProcessing.value = true

  // Create a form to submit for file download
  const form = document.createElement('form')
  form.method = 'POST'
  form.action = admin.invites.assign.url({ invite: selectedInviteId.value })

  // Add CSRF token
  const csrfInput = document.createElement('input')
  csrfInput.type = 'hidden'
  csrfInput.name = '_token'
  csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
  form.appendChild(csrfInput)

  // Add form data
  Object.entries(data).forEach(([key, value]) => {
    const input = document.createElement('input')
    input.type = 'hidden'
    input.name = key
    input.value = String(value)
    form.appendChild(input)
  })

  document.body.appendChild(form)
  form.submit()
  document.body.removeChild(form)

  // Close modal and show success message
  setTimeout(() => {
    showAssignModal.value = false
    selectedInviteId.value = null
    assignProcessing.value = false
    toastMessage.value = 'Table assigned and pass generated!'
    showToast.value = true

    // Reload the page to show updated data
    router.reload()
  }, 500)
}

const downloadPass = (inviteId: number) => {
  window.location.href = admin.invites.download.url({ invite: inviteId })
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString()
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-8">
    <div class="mx-auto max-w-7xl space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">{{ event.title }}</h1>
          <p class="text-gray-600">{{ event.address }}</p>
          <p class="text-sm text-gray-500">{{ formatDate(event.event_time) }}</p>
        </div>
        <Button variant="outline" @click="logout">Logout</Button>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader>
            <CardTitle>Total Invites</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold">{{ stats.total }}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Used</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold text-green-600">{{ stats.used }}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Reserved</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold text-purple-600">{{ stats.reserved }}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Remaining</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold text-blue-600">{{ stats.remaining }}</p>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <div class="flex items-center justify-between">
            <div>
              <CardTitle>Manage Invites</CardTitle>
              <CardDescription>Create and view your event invites</CardDescription>
            </div>
            <Button @click="showCreateForm = !showCreateForm">
              {{ showCreateForm ? 'Cancel' : 'Create Invites' }}
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <div v-if="showCreateForm" class="mb-6 rounded-lg border bg-gray-50 p-4">
            <form @submit.prevent="submit" class="space-y-4">
              <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                  <Label for="count">Number of Invites</Label>
                  <Input
                    id="count"
                    v-model.number="form.count"
                    type="number"
                    min="1"
                    max="1000"
                    required
                  />
                  <p v-if="form.errors.count" class="text-sm text-red-600">
                    {{ form.errors.count }}
                  </p>
                </div>

                <div class="space-y-2">
                  <Label for="table_start">Starting Table Number</Label>
                  <Input
                    id="table_start"
                    v-model.number="form.table_start"
                    type="number"
                    min="1"
                    required
                  />
                  <p v-if="form.errors.table_start" class="text-sm text-red-600">
                    {{ form.errors.table_start }}
                  </p>
                </div>
              </div>

              <div class="space-y-4">
                <div class="flex items-center space-x-2">
                  <input
                    id="reserve_tables"
                    v-model="form.reserve_tables"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                  />
                  <Label for="reserve_tables" class="cursor-pointer">Reserve Tables</Label>
                </div>

                <div v-if="form.reserve_tables" class="space-y-2">
                  <Label for="reserved_count">Number of Tables to Reserve</Label>
                  <Input
                    id="reserved_count"
                    v-model.number="form.reserved_count"
                    type="number"
                    min="0"
                    :max="form.count"
                    required
                  />
                  <p class="text-xs text-gray-500">
                    Reserved tables will be marked as used and show "Reserved" instead of guest details
                  </p>
                  <p v-if="form.errors.reserved_count" class="text-sm text-red-600">
                    {{ form.errors.reserved_count }}
                  </p>
                </div>
              </div>

              <Button type="submit" :disabled="form.processing">
                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                {{ form.processing ? 'Creating...' : 'Create Invites' }}
              </Button>
            </form>
          </div>

          <div v-if="invites.length === 0" class="text-center py-12">
            <p class="text-gray-500">No invites created yet</p>
          </div>

          <div v-else>
            <!-- Mobile card view -->
            <div class="space-y-3 md:hidden">
              <div
                v-for="invite in invites"
                :key="invite.id"
                class="rounded-lg border bg-white p-4 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-500">Table #</p>
                    <p class="text-lg font-bold">{{ invite.table_number }}</p>
                  </div>
                  <Badge :variant="invite.is_reserved ? 'outline' : invite.is_used ? 'default' : 'secondary'">
                    {{ invite.is_reserved ? 'Reserved' : invite.is_used ? 'Used' : 'Available' }}
                  </Badge>
                </div>

                <div v-if="invite.invitee_name" class="space-y-1">
                  <p class="text-sm font-medium text-gray-500">Invitee</p>
                  <p class="text-sm">{{ invite.invitee_name }}</p>
                  <p class="text-sm text-gray-600">{{ invite.invitee_phone }}</p>
                  <p class="text-xs text-gray-500">
                    {{ invite.has_plus_one ? 'Plus One: Yes' : 'Plus One: No' }}
                  </p>
                </div>

                <div v-if="invite.used_at" class="border-t pt-2">
                  <p class="text-xs text-gray-500">
                    Used: {{ formatDate(invite.used_at) }}
                  </p>
                </div>

                <div class="flex gap-2">
                  <Button
                    v-if="!invite.is_used && !invite.is_reserved"
                    size="sm"
                    variant="outline"
                    class="flex-1"
                    @click="copyInviteLink(invite.token)"
                  >
                    Copy Link
                  </Button>
                  <Button
                    v-if="!invite.is_used && !invite.is_reserved"
                    size="sm"
                    variant="secondary"
                    class="flex-1"
                    @click="reserveTable(invite.id)"
                  >
                    Reserve
                  </Button>
                  <Button
                    v-if="invite.is_used && !invite.is_reserved"
                    size="sm"
                    variant="outline"
                    class="flex-1"
                    @click="downloadPass(invite.id)"
                  >
                    Download Pass
                  </Button>
                  <Button
                    v-if="invite.is_used && !invite.is_reserved"
                    size="sm"
                    variant="destructive"
                    class="flex-1"
                    @click="revokeInvite(invite.id)"
                  >
                    Revoke
                  </Button>
                  <Button
                    v-if="invite.is_reserved"
                    size="sm"
                    variant="outline"
                    class="flex-1"
                    @click="assignReservedTable(invite.id)"
                  >
                    Assign Table
                  </Button>
                </div>
              </div>
            </div>

            <!-- Desktop table view -->
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full">
                <thead class="border-b">
                  <tr class="text-left">
                    <th class="pb-2 px-2">Table #</th>
                    <th class="pb-2 px-2">Status</th>
                    <th class="pb-2 px-2">Invitee</th>
                    <th class="pb-2 px-2">Phone</th>
                    <th class="pb-2 px-2">Plus One</th>
                    <th class="pb-2 px-2">Used At</th>
                    <th class="pb-2 px-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="invite in invites" :key="invite.id" class="border-b">
                    <td class="py-3 px-2">{{ invite.table_number }}</td>
                    <td class="py-3 px-2">
                      <Badge :variant="invite.is_reserved ? 'outline' : invite.is_used ? 'default' : 'secondary'">
                        {{ invite.is_reserved ? 'Reserved' : invite.is_used ? 'Used' : 'Available' }}
                      </Badge>
                    </td>
                    <td class="py-3 px-2">{{ invite.invitee_name || '-' }}</td>
                    <td class="py-3 px-2">{{ invite.invitee_phone || '-' }}</td>
                    <td class="py-3 px-2">{{ invite.has_plus_one ? 'Yes' : 'No' }}</td>
                    <td class="py-3 px-2 text-sm text-gray-500">
                      {{ invite.used_at ? formatDate(invite.used_at) : '-' }}
                    </td>
                    <td class="py-3 px-2">
                      <div class="flex gap-2">
                        <Button
                          v-if="!invite.is_used && !invite.is_reserved"
                          size="sm"
                          variant="outline"
                          @click="copyInviteLink(invite.token)"
                        >
                          Copy Link
                        </Button>
                        <Button
                          v-if="!invite.is_used && !invite.is_reserved"
                          size="sm"
                          variant="secondary"
                          @click="reserveTable(invite.id)"
                        >
                          Reserve
                        </Button>
                        <Button
                          v-if="invite.is_used && !invite.is_reserved"
                          size="sm"
                          variant="outline"
                          @click="downloadPass(invite.id)"
                        >
                          Download Pass
                        </Button>
                        <Button
                          v-if="invite.is_used && !invite.is_reserved"
                          size="sm"
                          variant="destructive"
                          @click="revokeInvite(invite.id)"
                        >
                          Revoke
                        </Button>
                        <Button
                          v-if="invite.is_reserved"
                          size="sm"
                          variant="outline"
                          @click="assignReservedTable(invite.id)"
                        >
                          Assign Table
                        </Button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <Toast v-model:show="showToast" :message="toastMessage" type="success" />

    <ConfirmModal
      v-model:show="showRevokeModal"
      title="Revoke Invite"
      message="Are you sure you want to revoke this invite? This will reset it so it can be used again."
      confirm-text="Revoke"
      variant="destructive"
      @confirm="confirmRevoke"
    />

    <ConfirmModal
      v-model:show="showReserveModal"
      title="Reserve Table"
      message="Are you sure you want to reserve this table?"
      confirm-text="Reserve"
      @confirm="confirmReserve"
    />

    <AssignModal
      v-model:show="showAssignModal"
      :processing="assignProcessing"
      @submit="handleAssignSubmit"
    />
  </div>
</template>
