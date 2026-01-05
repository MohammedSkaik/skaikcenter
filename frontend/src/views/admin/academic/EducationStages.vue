<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">المراحل التعليمية (Education Stages)</h1>
      <button @click="openGradeModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-md">
        + مرحلة جديدة
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Grades Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
      <div v-for="grade in grades" :key="grade.id" class="bg-white shadow-sm hover:shadow-md transition rounded-xl overflow-hidden border border-gray-200 flex flex-col">
        <!-- Grade Header -->
        <div class="bg-indigo-50/50 px-6 py-4 flex justify-between items-center border-b border-indigo-100">
          <h3 class="text-lg font-bold text-indigo-900">{{ grade.name }}</h3>
          <button @click="openSubjectModal(grade)" class="text-sm bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-lg font-medium transition shadow-sm">
            + ربط مادة
          </button>
        </div>

        <!-- Subjects List -->
        <div class="p-4 flex-grow">
             <div v-if="grade.subjects.length === 0" class="text-sm text-gray-400 italic text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                لا يوجد مواد مرتبطة بهذه المرحلة.
             </div>
             <div v-else class="overflow-x-auto">
                 <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">المادة</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">حزمة</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">فردي</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">لغي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="subject in grade.subjects" :key="subject.id" class="group">
                            <td class="px-3 py-2 text-sm text-gray-900 font-bold">{{ subject.name }}</td>
                            <td class="px-3 py-2 text-sm text-gray-600">{{ subject.pivot.price_package }} ₪</td>
                            <td class="px-3 py-2 text-sm text-gray-600">{{ subject.pivot.price_single }} ₪</td>
                            <td class="px-3 py-2 text-sm text-right">
                                <button @click="detachSubject(grade, subject)" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity p-1">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                 </table>
             </div>
        </div>
      </div>
    </div>

    <!-- Grade Modal -->
    <div v-if="showGradeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="showGradeModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl scale-100">
        <h3 class="text-2xl font-bold mb-8 text-primary border-b border-gray-100 pb-4">🎓 إضافة مرحلة دراسية</h3>
        <form @submit.prevent="submitGrade" class="space-y-6">
             <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم المرحلة</label>
                <input v-model="formGrade.name" type="text" placeholder="الاسم (مثال: الصف العاشـر)" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الترتيب</label>
                <input v-model="formGrade.level_order" type="number" placeholder="مثال: 10" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white">
            </div>
            <div class="mt-10 flex justify-end gap-3">
              <button type="button" @click="showGradeModal = false" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-light shadow-lg shadow-primary/30 transition duration-200">حفظ</button>
            </div>
        </form>
      </div>
    </div>

    <!-- Attach Subject Modal -->
    <div v-if="showSubjectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="showSubjectModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl">
        <h3 class="text-2xl font-bold mb-8 text-primary border-b border-gray-100 pb-4">
            {{ isEditingPivot ? '💰 تعديل أسعار المادة' : '🔗 ربط مادة بـ ' + selectedGrade?.name }}
        </h3>
        
        <form @submit.prevent="submitPivot" class="space-y-6">
             <div v-if="!isEditingPivot">
                <label class="block text-sm font-bold text-gray-700 mb-2">اختر المادة</label>
                <select v-model="formPivot.subject_id" 
                        class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer appearance-none">
                    <option v-for="sub in allSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                </select>
                <p class="text-xs text-gray-400 mt-2">لا تجد المادة؟ أضفها أولاً من صفحة "المواد الدراسية".</p>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                 <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">سعر الحزمة</label>
                    <div class="relative">
                        <input v-model="formPivot.price_package" type="number" 
                               class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 text-center font-bold text-primary">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">₪</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">سعر الفردي</label>
                   <div class="relative">
                        <input v-model="formPivot.price_single" type="number" 
                               class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 text-center font-bold text-primary">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">₪</span>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-3">
              <button type="button" @click="showSubjectModal = false" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-secondary text-primary font-bold rounded-xl hover:bg-secondary-light shadow-lg shadow-secondary/30 transition duration-200">
                 {{ isEditingPivot ? 'تحديث الأسعار' : 'ربط المادة' }}
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

const grades = ref([])
const allSubjects = ref([])
const loading = ref(false)
const showGradeModal = ref(false)
const showSubjectModal = ref(false)
const selectedGrade = ref(null)
const isEditingPivot = ref(false)

const toast = useToastStore()
const formGrade = reactive({ name: '', level_order: '' })
const formPivot = reactive({ subject_id: null, price_package: 0, price_single: 0 })

// Fetch Data
const fetchGrades = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/grades')
    grades.value = res.data.data
  } catch (e) {
    toast.error('فشل تحميل البيانات')
  } finally {
    loading.value = false
  }
}

const fetchAllSubjects = async () => {
    try {
        const res = await api.get('/api/subjects')
        allSubjects.value = res.data.data
    } catch(e) {
        console.error('Error fetching global subjects')
    }
}

// Grades Management
const openGradeModal = () => {
    formGrade.name = ''
    formGrade.level_order = ''
    showGradeModal.value = true
}

const submitGrade = async () => {
    try {
        await api.post('/api/grades', formGrade)
        toast.success('تم إنشاء المرحلة بنجاح')
        showGradeModal.value = false
        fetchGrades()
    } catch (e) {
        toast.error('حدث خطأ. ربما الاسم مكرر؟')
    }
}

// Pivot Managment (Attach Subject)
const openSubjectModal = (grade) => {
    selectedGrade.value = grade
    isEditingPivot.value = false
    formPivot.subject_id = allSubjects.value.length > 0 ? allSubjects.value[0].id : null
    formPivot.price_package = 0
    formPivot.price_single = 0
    showSubjectModal.value = true
    
    // Refresh global list just in case
    fetchAllSubjects() 
}

const submitPivot = async () => {
    if(!selectedGrade.value) return;
    try {
        // Attach
        await api.post(`/api/grades/${selectedGrade.value.id}/subjects`, formPivot)
        toast.success('تم ربط المادة بنجاح')
        showSubjectModal.value = false
        fetchGrades()
    } catch (e) {
         toast.error(e.response?.data?.message || 'المادة مرتبطة مسبقاً بهذه المرحلة')
    }
}

const detachSubject = async (grade, subject) => {
    if(!confirm(`فك ارتباط ${subject.name} من ${grade.name}؟`)) return;
    try {
        await api.delete(`/api/grades/${grade.id}/subjects/${subject.id}`)
        toast.success('تمت الإزالة')
        fetchGrades()
    } catch (e) {
        toast.error('حدث خطأ')
    }
}

onMounted(() => {
  fetchGrades()
  fetchAllSubjects()
})
</script>
