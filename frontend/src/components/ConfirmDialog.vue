<template>
  <transition name="dialog-fade">
    <div v-if="store.active" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" @click.self="store.handleCancel">
      <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden transform transition-all scale-100 animate-scale-in">
        
        <!-- Header / Icon -->
        <div class="pt-8 pb-4 flex justify-center">
            <div class="w-20 h-20 rounded-full flex items-center justify-center text-4xl shadow-inner border-4"
                :class="{
                    'bg-red-50 text-red-500 border-red-100': store.options.type === 'delete',
                    'bg-blue-50 text-blue-500 border-blue-100': store.options.type === 'confirm',
                    'bg-yellow-50 text-yellow-500 border-yellow-100': store.options.type === 'alert'
                }">
                <span v-if="store.options.type === 'delete'">🗑️</span>
                <span v-else-if="store.options.type === 'confirm'">🤔</span>
                <span v-else>⚠️</span>
            </div>
        </div>

        <!-- Body -->
        <div class="px-8 pb-4 text-center">
            <h3 class="text-xl font-black text-gray-800 mb-2">{{ store.options.title }}</h3>
            <p class="text-gray-600 font-medium leading-relaxed">{{ store.options.message }}</p>
            <p v-if="store.options.item" class="text-red-600 font-bold mt-2 bg-red-50 py-1 px-3 rounded-full inline-block text-sm">
                {{ store.options.item }}
            </p>
        </div>

        <!-- Actions -->
        <div class="p-6 flex gap-3 justify-center">
             <button v-if="store.options.cancelText"
                @click="store.handleCancel"
                class="flex-1 py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition duration-200"
             >
                {{ store.options.cancelText }}
             </button>

             <button 
                @click="store.handleConfirm"
                class="flex-1 py-3 px-4 text-white font-bold rounded-xl shadow-lg transition duration-200 transform hover:scale-[1.02] active:scale-95"
                :class="{
                    'bg-red-500 hover:bg-red-600 shadow-red-500/30': store.options.type === 'delete',
                    'bg-primary hover:bg-primary-light shadow-primary/30': store.options.type !== 'delete'
                }"
             >
                {{ store.options.confirmText }}
             </button>
        </div>

      </div>
    </div>
  </transition>
</template>

<script setup>
import { useDialogStore } from '../stores/dialog'

const store = useDialogStore()
</script>

<style scoped>
.dialog-fade-enter-active,
.dialog-fade-leave-active {
  transition: opacity 0.2s ease;
}
.dialog-fade-enter-from,
.dialog-fade-leave-to {
  opacity: 0;
}

.animate-scale-in {
    animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes scaleIn {
    from { transform: scale(0.9) translateY(20px); opacity: 0; }
    to { transform: scale(1) translateY(0); opacity: 1; }
}
</style>
