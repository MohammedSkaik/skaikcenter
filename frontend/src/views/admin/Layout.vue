<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-indigo-800 text-white min-h-screen hidden md:block">
      <div class="p-4 text-xl font-bold">Skaik Admin</div>
      <nav class="mt-4">
        <router-link to="/admin/managers" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">Managers</router-link>
        <router-link to="/admin/roles" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">Roles</router-link>
        <a href="#" @click.prevent="logout" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700 mt-8 text-red-200">Logout</a>
      </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-8">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const logout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>
