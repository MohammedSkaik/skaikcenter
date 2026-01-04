<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">إدارة القاعات</h1>
      <button @click="openModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + قاعة جديدة
      </button>
    </div>

    <div v-if="loading" class="text-center py-10">
      <span class="text-gray-500">جاري التحميل...</span>
    </div>

    <div v-else class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم القاعة</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعة</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="room in rooms" :key="room.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ room.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ room.capacity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span v-if="room.type === 'classroom'">فصل دراسي</span>
                <span v-else-if="room.type === 'lab'">مختبر</span>
                <span v-else>قاعة عامة</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button class="text-red-600 hover:text-red-900 ml-2">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">إضافة قاعة جديدة</h3>
        <input v-model="form.name" type="text" placeholder="اسم القاعة" class="w-full border p-2 rounded mb-3">
        <input v-model="form.capacity" type="number" placeholder="السعة الاستيعابية" class="w-full border p-2 rounded mb-3">
        <select v-model="form.type" class="w-full border p-2 rounded mb-3">
            <option value="classroom">فصل دراسي</option>
            <option value="lab">مختبر</option>
            <option value="hall">قاعة عامة</option>
        </select>
        <div class="flex justify-end space-x-2 space-x-reverse">
          <button @click="showModal = false" class="text-gray-600 px-4">إلغاء</button>
          <button @click="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../../../api/axios'

const rooms = ref([])
const loading = ref(false)
const showModal = ref(false)

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
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openModal = () => {
    form.name = ''
    form.capacity = 20
    form.type = 'classroom'
    showModal.value = true
}

const submit = async () => {
    try {
        await api.post('/api/rooms', form)
        showModal.value = false
        fetchRooms()
    } catch (e) {
        alert('Error creating room')
    }
}

onMounted(() => {
  fetchRooms()
})
</script>
