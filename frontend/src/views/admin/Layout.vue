<template>
  <div class="min-h-screen bg-gray-100 flex font-sans" dir="rtl">
    <!-- Sidebar -->
    <aside class="w-64 bg-indigo-800 text-white min-h-screen hidden md:block">
      <div class="p-4 text-xl font-bold border-b border-indigo-700">إدارة المركز</div>
      <nav class="mt-4">
        <router-link to="/admin/managers" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700 mx-2">المدراء والصلاحيات</router-link>
        <router-link to="/admin/roles" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700 mx-2">الأدوار الوظيفية</router-link>
        <a href="#" @click.prevent="logout" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700 mt-8 text-red-200 mx-2">تسجيل الخروج</a>
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
