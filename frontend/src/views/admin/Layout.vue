<template>
  <div class="min-h-screen bg-gray-100 flex font-sans" dir="rtl">
    <!-- Sidebar -->
    <aside class="w-64 bg-primary text-white min-h-screen hidden md:flex flex-col shadow-xl z-10 transition-all duration-300">
      <!-- Clean Header with Logo & Text -->
      <div class="h-20 flex items-center px-5 gap-3 border-b border-white/10 bg-primary-dark select-none">
        <div class="flex-shrink-0 bg-white rounded-lg p-1 h-10 w-10 flex items-center justify-center shadow-lg shadow-white/10">
            <img src="/logo.png" alt="Logo" class="h-full w-full object-contain">
        </div>
        <div class="flex flex-col">
            <h1 class="text-sm font-bold text-white leading-tight">مركز سكيك التعليمي</h1>
            <p class="text-[10px] text-secondary font-medium tracking-wide">SKAIK CENTER</p>
        </div>
      </div>
      
      <nav class="flex-1 mt-6 overflow-y-auto px-4 space-y-2">
        <p class="px-4 text-xs font-bold text-secondary-light uppercase tracking-wider mb-2">الإدارة العامة</p>
        <router-link to="/admin/managers" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">👥</span> المدراء والصلاحيات
        </router-link>
        <router-link to="/admin/roles" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">🛡️</span> الأدوار الوظيفية
        </router-link>
        
        <div class="border-t border-white/10 my-4"></div>
        <p class="px-4 text-xs font-bold text-secondary-light uppercase tracking-wider mb-2">الشؤون التعليمية</p>
        
        <router-link to="/admin/academic/years" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">📅</span> السنوات والفصول الدراسية
        </router-link>
        <router-link to="/admin/academic/subjects" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">📖</span> المواد الدراسية
        </router-link>
        <router-link to="/admin/academic/education-stages" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">🎓</span> المراحل التعليمية
        </router-link>
        <router-link to="/admin/academic/rooms" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">🏫</span> القاعات
        </router-link>
        <router-link to="/admin/academic/classes" active-class="bg-secondary text-primary font-bold shadow-lg" class="flex items-center py-2.5 px-4 rounded-lg text-gray-200 hover:bg-white/10 hover:text-white transition duration-200">
           <span class="ml-3">🎓</span> الشعب الدراسية
        </router-link>
      </nav>

      <div class="p-4 border-t border-white/10">
        <a href="#" @click.prevent="logout" class="flex items-center py-2.5 px-4 rounded-lg text-red-300 hover:bg-red-900/40 hover:text-white transition duration-200">
          <span class="ml-3">🚪</span> تسجيل الخروج
        </a>
      </div>
    </aside>

    <!-- Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Bar -->
      <header class="bg-white shadow-sm z-0">
          <div class="px-8 py-4 flex justify-between items-center">
              <h2 class="text-xl font-semibold text-gray-800">لوحة التحكم</h2>
              <div class="flex items-center space-x-4 space-x-reverse">
                  <span class="text-sm text-gray-500">مرحباً، {{ auth.user?.name || 'المسؤول' }}</span>
                  <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                    {{ (auth.user?.name || 'A').charAt(0).toUpperCase() }}
                  </div>
              </div>
          </div>
      </header>

      <div class="flex-1 overflow-auto p-8">
         <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
         </router-view>
      </div>
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
