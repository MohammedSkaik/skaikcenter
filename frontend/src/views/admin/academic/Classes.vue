<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">الشعب الدراسية (المجموعات)</h1>
      <button @click="openModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + شعبة جديدة
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex space-x-4 space-x-reverse">
        <select v-model="filters.semester_id" @change="fetchClasses" class="border p-2 rounded w-48 bg-white">
            <option value="">كل الفصول</option>
            <option v-for="sem in semesters" :key="sem.id" :value="sem.id">{{ sem.name }} ({{ sem.academic_year.name }})</option>
        </select>
        <select v-model="filters.type" @change="fetchClasses" class="border p-2 rounded w-48 bg-white">
            <option value="">الكل (حزمة / فردي)</option>
            <option value="package">حزمة (عامة)</option>
            <option value="single">مادة فردية (تقوية)</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم الشعبة</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المرحلة</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المادة (للفردي)</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العدد الأقصى</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="cls in classes" :key="cls.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-900">{{ cls.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ cls.grade?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span v-if="cls.type === 'package'" class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">حزمة</span>
                <span v-else class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">فردي</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ cls.subject?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ cls.max_students }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">إنشاء شعبة دراسية</h3>
        
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">الفصل الدراسي</label>
            <select v-model="form.semester_id" class="w-full border p-2 rounded">
                <option v-for="sem in semesters" :key="sem.id" :value="sem.id">{{ sem.name }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">المرحلة</label>
            <select v-model="form.grade_id" @change="onGradeChange" class="w-full border p-2 rounded">
                 <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">اسم الشعبة (مثال: المجموعة أ)</label>
            <input v-model="form.name" type="text" class="w-full border p-2 rounded">
        </div>

         <div class="mb-3 flex space-x-4 space-x-reverse">
            <label class="inline-flex items-center">
                <input type="radio" v-model="form.type" value="package" class="form-radio text-indigo-600">
                <span class="mr-2">حزمة كاملة</span>
            </label>
             <label class="inline-flex items-center">
                <input type="radio" v-model="form.type" value="single" class="form-radio text-indigo-600">
                <span class="mr-2">مادة فردية</span>
            </label>
        </div>

        <div v-if="form.type === 'single'" class="mb-3">
             <label class="block text-sm font-medium text-gray-700 mb-1">المادة</label>
             <select v-model="form.subject_id" class="w-full border p-2 rounded">
                 <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
             </select>
        </div>

        <div class="mb-3">
             <label class="block text-sm font-medium text-gray-700 mb-1">العدد الأقصى للطلاب</label>
             <input v-model="form.max_students" type="number" class="w-full border p-2 rounded">
        </div>

        <div class="flex justify-end space-x-2 space-x-reverse">
          <button @click="showModal = false" class="text-gray-600 px-4">إلغاء</button>
          <button @click="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import api from '../../../api/axios'

const classes = ref([])
const semesters = ref([])
const grades = ref([])
const filters = reactive({ semester_id: '', type: '' })
const showModal = ref(false)

const form = reactive({
    semester_id: '',
    grade_id: '',
    name: '',
    type: 'package',
    subject_id: null,
    max_students: 20
})

const fetchClasses = async () => {
    try {
        const params = { ...filters }
        const res = await api.get('/api/study-classes', { params })
        classes.value = res.data.data
    } catch(e) { console.error(e) }
}

const fetchDependencies = async () => {
    // 1. Years/Semesters (need flatten) - Simplified: endpoint should return active year's semester or all
    // For now, let's assume we can verify years endpoint returns semesters
    const yearsRes = await api.get('/api/academic-years')
    // Extract semseters
    let sems = []
    yearsRes.data.data.forEach(year => {
        year.semesters.forEach(s => {
            s.academic_year = year
            sems.push(s)
        })
    })
    semesters.value = sems

    // 2. Grades (with subjects)
    const gradesRes = await api.get('/api/grades')
    grades.value = gradesRes.data.data
}

const openModal = () => {
   showModal.value = true
}

const filteredSubjects = computed(() => {
    if(!form.grade_id) return []
    const g = grades.value.find(gr => gr.id === form.grade_id)
    return g ? g.subjects : []
})

const onGradeChange = () => {
    form.subject_id = null
}

const submit = async () => {
    if(form.type === 'package') form.subject_id = null
    try {
        await api.post('/api/study-classes', form)
        showModal.value = false
        fetchClasses()
    } catch (e) {
        alert('Error creating class')
    }
}

onMounted(() => {
    fetchDependencies()
    fetchClasses()
})
</script>
