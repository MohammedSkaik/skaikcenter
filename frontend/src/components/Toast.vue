<template>
  <div class="fixed top-6 left-1/2 -translate-x-1/2 z-[100] flex flex-col gap-3 w-full max-w-md px-4 pointer-events-none">
    <transition-group name="toast-pop">
      <div v-for="toast in toasts" :key="toast.id" 
        class="flex items-center w-full p-4 text-gray-800 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border pointer-events-auto transform transition-all duration-300 ring-1"
        :class="{
            'border-green-100 ring-green-500/20': toast.type === 'success',
            'border-red-100 ring-red-500/20': toast.type === 'error',
            'border-blue-100 ring-blue-500/20': toast.type === 'info',
            'border-yellow-100 ring-yellow-500/20': toast.type === 'warning'
        }"
        role="alert">
        
        <div class="inline-flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full shadow-sm"
             :class="{
                 'text-green-600 bg-green-100': toast.type === 'success',
                 'text-red-600 bg-red-100': toast.type === 'error',
                 'text-blue-600 bg-blue-100': toast.type === 'info',
                 'text-yellow-600 bg-yellow-100': toast.type === 'warning'
             }">
             <span v-if="toast.type === 'success'" class="text-xl">✨</span>
             <span v-else-if="toast.type === 'error'" class="text-xl">✖</span>
             <span v-else-if="toast.type === 'warning'" class="text-xl">⚠️</span>
             <span v-else class="text-xl">ℹ️</span>
        </div>
        
        <div class="mr-4 ml-2 flex-1">
            <h4 class="font-bold text-sm" 
               :class="{
                   'text-green-700': toast.type === 'success',
                   'text-red-700': toast.type === 'error',
                   'text-yellow-700': toast.type === 'warning',
                   'text-blue-700': toast.type === 'info'
               }">
               {{ toast.type === 'success' ? 'نجاح' : (toast.type === 'error' ? 'خطأ' : (toast.type === 'warning' ? 'تنبيه' : 'معلومة')) }}
            </h4>
            <div class="text-sm font-medium text-gray-600 mt-0.5">{{ toast.message }}</div>
        </div>

        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition" 
            @click="removeToast(toast.id)">
            <span class="sr-only">Close</span>
            <span class="text-lg">×</span>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useToastStore } from '../stores/toast'
import { computed } from 'vue'

const store = useToastStore()
const toasts = computed(() => store.toasts)
const removeToast = (id) => store.remove(id)
</script>

<style scoped>
.toast-pop-enter-active,
.toast-pop-leave-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.toast-pop-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.9);
}
.toast-pop-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.9);
}
</style>
