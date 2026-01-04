<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">الهيكل التعليمي</h1>
      <button @click="openGradeModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + مرحلة جديدة
      </button>
    </div>

    <div v-if="loading" class="text-center py-10">
      <span class="text-gray-500">جاري التحميل...</span>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="grade in grades" :key="grade.id" class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
        <!-- Grade Header -->
        <div class="bg-indigo-50 px-6 py-4 flex justify-between items-center border-b border-indigo-100">
          <h3 class="text-lg font-bold text-indigo-900">{{ grade.name }}</h3>
          <button @click="openSubjectModal(grade)" class="text-sm bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 px-3 py-1 rounded">
            + مادة
          </button>
        </div>

        <!-- Subjects List -->
        <div class="p-4">
             <div v-if="grade.subjects.length === 0" class="text-sm text-gray-400 italic text-center py-2">لا يوجد مواد مضافة.</div>
             <table v-else class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-2 py-1 text-right text-xs font-medium text-gray-500">المادة</th>
                        <th class="px-2 py-1 text-right text-xs font-medium text-gray-500">سعر الحزمة</th>
                        <th class="px-2 py-1 text-right text-xs font-medium text-gray-500">سعر الفردي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="subject in grade.subjects" :key="subject.id">
                        <td class="px-2 py-2 text-sm text-gray-900 font-medium">{{ subject.name }}</td>
                        <td class="px-2 py-2 text-sm text-gray-500">{{ subject.price_package }} ₪</td>
                        <td class="px-2 py-2 text-sm text-gray-500">{{ subject.price_single }} ₪</td>
                    </tr>
                </tbody>
             </table>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <div v-if="showGradeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">إضافة مرحلة دراسية</h3>
        <input v-model="formGrade.name" type="text" placeholder="الاسم (مثال: الصف العاشـر)" class="w-full border p-2 rounded mb-3">
        <input v-model="formGrade.level_order" type="number" placeholder="الترتيب (مثال: 10)" class="w-full border p-2 rounded mb-3">
        <div class="flex justify-end space-x-2 space-x-reverse">
          <button @click="showGradeModal = false" class="text-gray-600 px-4">إلغاء</button>
          <button @click="submitGrade" class="bg-indigo-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>
      </div>
    </div>

    <div v-if="showSubjectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">إضافة مادة لـ {{ selectedGrade?.name }}</h3>
        <input v-model="formSubject.name" type="text" placeholder="اسم المادة" class="w-full border p-2 rounded mb-3">
        <div class="grid grid-cols-2 gap-4 mb-3">
            <div>
                <label class="text-xs text-gray-500 block mb-1">حصة في الحزمة (شيكل)</label>
                <input v-model="formSubject.price_package" type="number" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">سعر الفردي (شيكل)</label>
                <input v-model="formSubject.price_single" type="number" class="w-full border p-2 rounded">
            </div>
        </div>
        <div class="flex justify-end space-x-2 space-x-reverse">
          <button @click="showSubjectModal = false" class="text-gray-600 px-4">إلغاء</button>
          <button @click="submitSubject" class="bg-indigo-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../../../api/axios'

const grades = ref([])
const loading = ref(false)
const showGradeModal = ref(false)
const showSubjectModal = ref(false)
const selectedGrade = ref(null)

const formGrade = reactive({ name: '', level_order: '' })
const formSubject = reactive({ name: '', price_package: 0, price_single: 0 })

const fetchGrades = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/grades')
    grades.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openGradeModal = () => {
    formGrade.name = ''
    formGrade.level_order = ''
    showGradeModal.value = true
}

const submitGrade = async () => {
    try {
        await api.post('/api/grades', formGrade)
        showGradeModal.value = false
        fetchGrades()
    } catch (e) {
        alert('Error creating grade')
    }
}

const openSubjectModal = (grade) => {
    selectedGrade.value = grade
    formSubject.name = ''
    formSubject.price_package = 0
    formSubject.price_single = 0
    showSubjectModal.value = true
}

const submitSubject = async () => {
    if(!selectedGrade.value) return;
    try {
        await api.post(`/api/grades/${selectedGrade.value.id}/subjects`, formSubject)
        showSubjectModal.value = false
        fetchGrades()
    } catch (e) {
        alert('Error creating subject')
    }
}

onMounted(() => {
  fetchGrades()
})
</script>
