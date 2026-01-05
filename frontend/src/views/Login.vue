<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-primary to-primary-dark" dir="rtl">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden border-4 border-secondary/50">
      <div class="px-8 py-10">
        <div class="text-center mb-10">
          <img src="/logo.png" alt="Skaik Center" class="h-24 mx-auto mb-4 drop-shadow-md">
          <h2 class="text-3xl font-extrabold text-primary mb-2">مركز سكيك التعليمي</h2>
          <p class="text-sm text-gray-500">منصة الإدارة المتكاملة</p>
        </div>
        
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-1">البريد الإلكتروني</label>
            <input id="email" name="email" type="email" autocomplete="email" required v-model="email" 
              class="appearance-none block w-full px-3 py-3 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm transition duration-200" 
              placeholder="admin@example.com">
          </div>

          <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">كلمة المرور</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password" 
              class="appearance-none block w-full px-3 py-3 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm transition duration-200" 
              placeholder="••••••••">
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
              <label for="remember-me" class="mr-2 block text-sm text-gray-900"> تذكرني </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-primary hover:text-primary-light"> نسيت كلمة المرور؟ </a>
            </div>
          </div>

          <div>
            <button type="submit" 
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-primary bg-secondary hover:bg-secondary-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary transition duration-200 transform hover:scale-[1.02]">
              دخول النظام
            </button>
          </div>
        </form>
      </div>
      <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-center">
        <p class="text-xs text-gray-500">&copy; {{ new Date().getFullYear() }} مركز سكيك التعليمي. جميع الحقوق محفوظة.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { useToastStore } from '../stores/toast'

const email = ref('')
const password = ref('')
const auth = useAuthStore()
const router = useRouter()
const toast = useToastStore()

const handleLogin = async () => {
  try {
    await auth.login({ email: email.value, password: password.value })
    toast.success('تم تسجيل الدخول بنجاح')
    router.push('/admin/managers')
  } catch (e) {
    if (e.response && e.response.status === 401) {
        toast.error('خطأ في البيانات: يرجى التحقق من البريد أو كلمة المرور')
    } else {
        toast.error('حدث خطأ غير متوقع. حاول مرة أخرى لاحقاً')
    }
  }
}
</script>
