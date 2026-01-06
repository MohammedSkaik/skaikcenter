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
              <div class="flex items-center gap-4">
                  <h2 class="text-xl font-semibold text-gray-800 hidden lg:block">لوحة التحكم</h2>
                  
                  <!-- Global Academic Cycle Selector -->
                  <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-200">
                      <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">الإطار الزمني:</span>
                      
                      <!-- Year Selector -->
                       <select :value="academicStore.currentYearId" @change="e => changeYear(e.target.value)" 
                               class="bg-transparent border-none text-sm font-bold text-primary focus:ring-0 cursor-pointer py-0 pl-8 pr-2">
                           <option v-for="year in academicStore.years" :key="year.id" :value="year.id">{{ year.name }}</option>
                       </select>
                       
                       <span class="text-gray-300">|</span>

                       <!-- Semester Selector -->
                       <select v-if="academicStore.semesters.length > 0" 
                               :value="academicStore.currentSemesterId" @change="e => changeSemester(e.target.value)"
                               class="bg-transparent border-none text-sm font-bold text-secondary focus:ring-0 cursor-pointer py-0 pl-8 pr-2">
                           <option v-for="sem in academicStore.semesters" :key="sem.id" :value="sem.id">{{ sem.name }}</option>
                       </select>
                       <span v-else class="text-xs text-red-400 px-2">لا توجد فصول</span>
                  </div>
              </div>

              <div class="flex items-center space-x-4 space-x-reverse">
                  <div class="text-left hidden sm:block">
                      <p class="text-sm font-bold text-gray-900">{{ auth.user?.name || 'المسؤول' }}</p>
                      <p class="text-xs text-gray-500">{{ auth.user?.role || 'Admin' }}</p>
                  </div>
                  <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold shadow-sm border-2 border-white ring-2 ring-gray-100">
                    {{ (auth.user?.name || 'A').charAt(0).toUpperCase() }}
                  </div>
              </div>
          </div>
      </header>

      <div class="flex-1 overflow-auto p-8 relative">
         <!-- Loading Overlay if Academic Store is loading -->
         <div v-if="academicStore.loading" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-50 flex items-center justify-center">
             <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent shadow-lg"></div>
         </div>

         <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" :key="academicStore.currentYearId + '-' + academicStore.currentSemesterId" />
            </transition>
         </router-view>
      </div>
    </main>
    
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useAcademicStore } from '../../stores/academic'
import { useRouter } from 'vue-router'
import ConfirmDialog from '../../components/ConfirmDialog.vue'

const auth = useAuthStore()
const academicStore = useAcademicStore()
const router = useRouter()

const logout = async () => {
  await auth.logout()
  router.push('/login')
}

const changeYear = (yearId) => {
    const year = academicStore.years.find(y => y.id == yearId)
    if (year) academicStore.setYear(year)
}

const changeSemester = (semId) => {
    const sem = academicStore.semesters.find(s => s.id == semId)
    if (sem) academicStore.setSemester(sem)
}

onMounted(() => {
    academicStore.fetchCycles()
})
</script>
