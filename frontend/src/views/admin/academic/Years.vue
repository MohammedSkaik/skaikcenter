<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">السنوات الدراسية والفصول</h1>
      <button @click="openYearModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + سنة جديدة
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-10">
      <span class="text-gray-500">جاري التحميل...</span>
    </div>

    <!-- Years List -->
    <div v-else class="space-y-6">
      <div v-for="year in years" :key="year.id" class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
        <!-- Year Header -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-200">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ year.name }}</h3>
            <span class="text-sm text-gray-500">{{ year.start_date }} - {{ year.end_date }}</span>
            <span :class="{'bg-green-100 text-green-800': year.status === 'active', 'bg-gray-100 text-gray-800': year.status !== 'active'}" class="bs-badge mr-2 px-2 py-0.5 rounded text-xs">
              {{ year.status === 'active' ? 'نشط' : 'غير نشط' }}
            </span>
          </div>
          <div class="flex items-center space-x-2 space-x-reverse">
            <button @click="openSemesterModal(year)" class="text-sm text-indigo-600 hover:text-indigo-800 px-3">
              + إضافة فصل
            </button>
            <button class="text-gray-400 hover:text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
            </button>
          </div>
        </div>

        <!-- Semesters List -->
        <div class="px-6 py-4">
          <h4 class="text-sm font-semibold text-gray-600 mb-3">الفصول الدراسية:</h4>
          <div v-if="year.semesters.length === 0" class="text-sm text-gray-400 italic">لا يوجد فصول مضافة بعد.</div>
          <ul class="space-y-2">
            <li v-for="semester in year.semesters" :key="semester.id" class="flex justify-between items-center bg-gray-50 p-2 rounded">
              <span class="text-gray-800">{{ semester.name }}</span>
              <span class="text-xs text-gray-500">{{ semester.status }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Modals (Simplified for MVP) -->
    <div v-if="showYearModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">إضافة سنة دراسية</h3>
        <input v-model="formYear.name" type="text" placeholder="الاسم (مثال: 2025/2026)" class="w-full border p-2 rounded mb-3">
        <div class="flex space-x-2 space-x-reverse mb-3">
            <input v-model="formYear.start_date" type="date" class="w-1/2 border p-2 rounded">
            <input v-model="formYear.end_date" type="date" class="w-1/2 border p-2 rounded">
        </div>
        <div class="flex justify-end space-x-2 space-x-reverse">
          <button @click="showYearModal = false" class="text-gray-600 px-4">إلغاء</button>
          <button @click="submitYear" class="bg-indigo-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>
      </div>
    </div>
    
     <div v-if="showSemesterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">إضافة فصل لـ {{ selectedYear?.name }}</h3>
        <input v-model="formSemester.name" type="text" placeholder="الاسم (مثال: الفصل الأول)" class="w-full border p-2 rounded mb-3">
        <div class="flex justify-end space-x-2 space-x-reverse">
          <button @click="showSemesterModal = false" class="text-gray-600 px-4">إلغاء</button>
          <button @click="submitSemester" class="bg-indigo-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../../../api/axios'

const years = ref([])
const loading = ref(false)
const showYearModal = ref(false)
const showSemesterModal = ref(false)
const selectedYear = ref(null)

const formYear = reactive({
    name: '',
    start_date: '',
    end_date: ''
})

const formSemester = reactive({
    name: '',
    status: 'active'
})

const fetchYears = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/academic-years')
    years.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openYearModal = () => {
    formYear.name = ''
    formYear.start_date = ''
    formYear.end_date = ''
    showYearModal.value = true
}

const submitYear = async () => {
    try {
        await api.post('/api/academic-years', formYear)
        showYearModal.value = false
        fetchYears()
    } catch (e) {
        alert('Error creating year')
    }
}

const openSemesterModal = (year) => {
    selectedYear.value = year
    formSemester.name = ''
    showSemesterModal.value = true
}

const submitSemester = async () => {
    if(!selectedYear.value) return;
    try {
        await api.post(`/api/academic-years/${selectedYear.value.id}/semesters`, formSemester)
        showSemesterModal.value = false
        fetchYears()
    } catch (e) {
        alert('Error creating semester')
    }
}

onMounted(() => {
  fetchYears()
})
</script>
