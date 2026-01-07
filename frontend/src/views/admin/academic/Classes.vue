<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-100 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-primary">👨‍🏫 الشعب الدراسية (المجموعات)</h1>
        <p class="text-gray-500 mt-1 text-sm">
            إدارة الشعب للعام: <span class="font-bold text-secondary">{{ academicStore.currentYear?.name || '-' }}</span> 
            <span v-if="academicStore.currentSemester"> / {{ academicStore.currentSemester.name }}</span>
        </p>
      </div>
      <button @click="openModal" :disabled="!academicStore.currentSemesterId" class="bg-primary text-secondary font-bold px-6 py-3 rounded-xl hover:bg-primary-light hover:text-white transition flex items-center gap-2 shadow-lg shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="text-xl">+</span> شعبة جديدة
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-end">
        <!-- Type Filter -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">نوع الشعبة</label>
             <select v-model="filters.type" @change="fetchClasses" class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 bg-gray-50 focus:bg-white focus:border-secondary font-bold text-gray-700">
                <option value="">الكل (حزمة / فردي)</option>
                <option value="package">حزمة (عامة)</option>
                <option value="single">مادة فردية (تقوية)</option>
            </select>
        </div>
        
        <!-- Global Context Warning -->
        <div v-if="!academicStore.currentSemesterId" class="flex-1 text-red-500 text-sm font-bold flex items-center">
            ⚠️ يرجى اختيار فصل دراسي من القائمة العلوية لعرض الشعب.
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100 relative min-h-[200px]">
      <div v-if="loading" class="absolute inset-0 bg-white/80 z-10 flex items-center justify-center">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
      </div>

      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-primary text-white">
          <tr>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">اسم الشعبة</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">المرحلة</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">النوع</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">المادة (للفردي)</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">الطلاب</th>
            <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="cls in classes" :key="cls.id" class="hover:bg-gray-50 transition-colors group">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ cls.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ cls.grade?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span v-if="cls.type === 'package'" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">حزمة</span>
                <span v-else class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold">مادة فردية</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">{{ cls.subject?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="bg-gray-100 px-2 py-1 rounded text-primary font-bold">0 / {{ cls.max_students }}</span>
            </td>
             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                 <button @click="deleteClass(cls)" class="text-red-400 hover:text-red-700 opacity-0 transition-opacity">🗑️ حذف</button>
             </td>
          </tr>
           <tr v-if="classes.length === 0">
              <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                  {{ academicStore.currentSemesterId ? 'لا توجد شعب دراسية مضافة لهذا الفصل.' : 'يرجى اختيار فصل دراسي.' }}
              </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="showModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl overflow-y-auto max-h-[90vh]">
        <h3 class="text-2xl font-bold mb-6 text-primary border-b border-gray-100 pb-4">📚 إنشاء شعبة دراسية</h3>
        
        <form @submit.prevent="submit" class="space-y-5">
            <div class="bg-blue-50 p-3 rounded-lg text-blue-800 text-sm font-bold text-center mb-4">
                {{ academicStore.currentYear?.name }} - {{ academicStore.currentSemester?.name }}
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">المرحلة الدراسية</label>
                <select v-model="form.grade_id" @change="onGradeChange" class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer appearance-none">
                     <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم الشعبة</label>
                <input v-model="form.name" type="text" placeholder="مثال: المجموعة الذهبية (أ)" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white">
            </div>

             <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                 <label class="block text-sm font-bold text-gray-700 mb-3">نوع الاشتراك</label>
                 <div class="flex space-x-6 space-x-reverse">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" v-model="form.type" value="package" class="form-radio text-secondary w-5 h-5 focus:ring-secondary">
                        <span class="mr-2 font-bold text-gray-700">📦 حزمة كاملة</span>
                    </label>
                     <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" v-model="form.type" value="single" class="form-radio text-secondary w-5 h-5 focus:ring-secondary">
                        <span class="mr-2 font-bold text-gray-700">📝 مادة فردية (تقوية)</span>
                    </label>
                </div>
            </div>

            <div v-if="form.type === 'single'" class="animate-fade-in-down">
                 <label class="block text-sm font-bold text-gray-700 mb-2">المادة الدراسية</label>
                 <select v-model="form.subject_id" class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer appearance-none">
                     <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                 </select>
                 <p v-if="filteredSubjects.length === 0 && form.grade_id" class="text-red-500 text-xs mt-2">لا توجد مواد مرتبطة بهذه المرحلة. يرجى إضافتها أولاً من صفحة "المراحل التعليمية".</p>
            </div>

            <div>
                 <label class="block text-sm font-bold text-gray-700 mb-2">العدد الأقصى للطلاب</label>
                 <input v-model="form.max_students" type="number" class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white text-center">
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
              <button type="button" @click="showModal = false" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-light shadow-lg shadow-primary/30 transition duration-200 flex items-center gap-2">
                 <span>حفظ الشعبة</span>
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch } from 'vue'
import api from '../../../api/axios'
import { useToastStore } from '../../../stores/toast'
import { useAcademicStore } from '../../../stores/academic' // Global Store

const toast = useToastStore()
const academicStore = useAcademicStore()
const classes = ref([])
const grades = ref([])
const loading = ref(false)

const filters = reactive({ type: '' })
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
    if(!academicStore.currentSemesterId) {
        classes.value = []
        return
    }
    
    loading.value = true
    try {
        const params = { 
            semester_id: academicStore.currentSemesterId,
            ...filters 
        }
        const res = await api.get('/api/study-classes', { params })
        classes.value = res.data.data
    } catch(e) { 
        console.error(e)
    } finally {
        loading.value = false
    }
}

const fetchGrades = async () => {
    if(!academicStore.currentYearId) return
    try {
        // Only fetch grades for the current year
        const params = { year_id: academicStore.currentYearId }
        const res = await api.get('/api/grades', { params })
        grades.value = res.data.data
    } catch (e) {
        toast.error('فشل في تحميل المراحل الدراسية')
    }
}

// Watch for global context changes to refetch data
watch(() => academicStore.currentSemesterId, (newVal) => {
    if(newVal) {
        fetchClasses()
    } else {
        classes.value = []
    }
})

watch(() => academicStore.currentYearId, (newVal) => {
    if(newVal) fetchGrades()
})

// Form Logic
const openModal = () => {
   // Use global semester
   form.semester_id = academicStore.currentSemesterId
   
   form.grade_id = ''
   form.name = ''
   form.type = 'package'
   form.subject_id = null
   form.max_students = 20
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

const deleteClass = async (cls) => {
    const confirmed = await dialog.deleteConfirm(
        'سيتم حذف الشعبة وجميع تسجيلات الطلاب المرتبطة بها.',
        'حذف شعبة دراسية',
        cls.name
    );
    
    if (confirmed) {
        try {
            await api.delete(`/api/study-classes/${cls.id}`)
            toast.success('تم حذف الشعبة')
            fetchClasses()
        } catch (e) {
            toast.error('تعذر الحذف.')
        }
    }
}

const submit = async () => {
    if(form.type === 'package') form.subject_id = null
    
    // Safety check just in case context changed while modal open
    form.semester_id = academicStore.currentSemesterId

    if(!form.semester_id || !form.grade_id || !form.name) {
        toast.warning('يرجى تعبئة الحقول الأساسية')
        return
    }

    try {
        await api.post('/api/study-classes', form)
        toast.success('تم إنشاء الشعبة بنجاح 🎓')
        showModal.value = false
        fetchClasses()
    } catch (e) {
        toast.error('حدث خطأ أثناء الحفظ')
    }
}

onMounted(() => {
    if(academicStore.currentYearId) fetchGrades()
    if(academicStore.currentSemesterId) fetchClasses()
})
</script>
