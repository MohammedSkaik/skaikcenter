<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-primary">🏢 إدارة القاعات</h1>
        <p class="text-gray-500 mt-1 text-sm">إدارة القاعات والفصول والمختبرات المتاحة في المركز.</p>
      </div>
      <button @click="openModal" class="bg-primary text-secondary font-bold px-6 py-3 rounded-xl hover:bg-primary-light hover:text-white transition flex items-center gap-2 shadow-lg shadow-primary/20">
        <span class="text-xl">+</span> إضافة قاعة جديدة
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-primary text-white">
          <tr>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">اسم القاعة</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">السعة</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">النوع</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="room in rooms" :key="room.id" class="hover:bg-gray-50 transition-colors group">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ room.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold bg-gray-50 w-24 text-center rounded-lg border border-gray-100 mx-2">{{ room.capacity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span v-if="room.type === 'classroom'" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">فصل دراسي</span>
                <span v-else-if="room.type === 'lab'" class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold">مختبر</span>
                <span v-else class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-bold">قاعة عامة</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="editRoom(room)" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition" title="تعديل">
                        ✏️
                    </button>
                    <button @click="deleteRoom(room)" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition" title="حذف">
                        🗑️
                    </button>
                </div>
            </td>
          </tr>
          <tr v-if="rooms.length === 0">
              <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                  لا يوجد قاعات مضافة حتى الآن.
              </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="showModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl scale-100 border border-gray-100">
        <h3 class="text-2xl font-bold mb-8 text-primary border-b border-gray-100 pb-4">
            {{ isEditing ? '✏️ تعديل بيانات القاعة' : '🏛️ إضافة قاعة جديدة' }}
        </h3>
        
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم القاعة</label>
                <input v-model="form.name" type="text" placeholder="مثال: القاعة الذهبية، مختبر الحاسوب" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white" autofocus>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">السعة الاستيعابية</label>
                    <input v-model="form.capacity" type="number" placeholder="20" 
                           class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white text-center">
                </div>
                <div>
                     <label class="block text-sm font-bold text-gray-700 mb-2">نوع القاعة</label>
                    <select v-model="form.type" 
                            class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer appearance-none">
                        <option value="classroom">فصل دراسي</option>
                        <option value="lab">مختبر</option>
                        <option value="hall">قاعة عامة</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-10 flex justify-end gap-3">
              <button type="button" @click="showModal = false" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-light shadow-lg shadow-primary/30 transition duration-200 flex items-center gap-2">
                <span>{{ isEditing ? 'تحديث' : 'حفظ' }}</span>
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../../../api/axios'
import { useToastStore } from '../../../stores/toast'

const rooms = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const toast = useToastStore()

const form = reactive({
    name: '',
    capacity: 20,
    type: 'classroom'
})

const fetchRooms = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/rooms')
    rooms.value = res.data.data
  } catch (e) {
    toast.error('تعذر تحميل البيانات')
  } finally {
    loading.value = false
  }
}

const openModal = () => {
    isEditing.value = false
    editingId.value = null
    Object.assign(form, { name: '', capacity: 20, type: 'classroom' })
    showModal.value = true
}

const editRoom = (room) => {
    isEditing.value = true
    editingId.value = room.id
    Object.assign(form, { name: room.name, capacity: room.capacity, type: room.type })
    showModal.value = true
}

const deleteRoom = async (room) => {
    if (!confirm(`⚠️ تحذير هام!\n\nهل أنت متأكد تماماً من حذف "${room.name}"؟\n\n📌 تنبيه: سيتم حذف جميع الجداول الدراسية والحجوزات المرتبطة بهذه القاعة.\nلا يمكن التراجع عن هذا الإجراء.`)) {
        return;
    }

    try {
        await api.delete(`/api/rooms/${room.id}`)
        toast.success('تم حذف القاعة بنجاح')
        fetchRooms()
    } catch (e) {
        toast.error('تعذر الحذف. ربما القاعة مرتبطة ببيانات أخرى لا يمكن حذفها حالياً.')
    }
}

const submit = async () => {
    if(!form.name) {
        toast.warning('يرجى كتابة اسم القاعة')
        return
    }
    
    try {
        if (isEditing.value) {
            await api.put(`/api/rooms/${editingId.value}`, form)
            toast.success('تم تحديث البيانات بنجاح ✨')
        } else {
            await api.post('/api/rooms', form)
            toast.success('تم إضافة القاعة بنجاح ✨')
        }
        showModal.value = false
        fetchRooms()
    } catch (e) {
        toast.error('حدث خطأ أثناء الحفظ')
    }
}

onMounted(() => {
  fetchRooms()
})
</script>
