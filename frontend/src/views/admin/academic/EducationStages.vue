<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-primary">🎓 المراحل التعليمية</h1>
        <p class="text-gray-500 mt-1 text-sm">إدارة المراحل الدراسية للعام: <span class="text-secondary font-bold">{{ academicStore.currentYear?.name || 'غير محدد' }}</span></p>
      </div>
      <div>
          <button @click="openGradeModal" :disabled="!academicStore.currentYearId" class="bg-primary text-secondary font-bold px-6 py-2 rounded-xl hover:bg-primary-light hover:text-white transition shadow-lg shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed">
            + مرحلة جديدة
          </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
    </div>

    <!-- Grades Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
       <!-- Empty State -->
       <div v-if="grades.length === 0" class="col-span-2 text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
           <p class="text-gray-400 font-bold mb-4">لا توجد مراحل دراسية مضافة لهذا العام ({{ academicStore.currentYear?.name }}).</p>
           <div class="flex justify-center gap-3">
               <button @click="openGradeModal" :disabled="!academicStore.currentYearId" class="text-secondary font-bold hover:underline">إضافة يدوية</button>
               <span class="text-gray-300">|</span>
               <button @click="importFromPrevious" class="text-primary font-bold hover:underline flex items-center gap-1">
                   <span>♻️ نسخ من العام السابق</span>
               </button>
           </div>
       </div>

      <div v-else v-for="grade in grades" :key="grade.id" class="bg-white shadow-sm hover:shadow-md transition rounded-xl overflow-hidden border border-gray-200 flex flex-col">
            <!-- Grade Header with Package Price -->
            <div class="p-4 bg-gray-50 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors" @click="toggleGrade(grade.id)">
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ openGrades.includes(grade.id) ? '📂' : '📁' }}</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ grade.name }}</h3>
                        <p class="text-xs text-secondary font-bold">سعر حزمة الوزارة: {{ grade.package_price }} ₪</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">{{ grade.subjects?.length || 0 }} مادة</span>
                    <button @click.stop="openSubjectModal(grade)" class="text-sm bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition shadow-sm">+ ربط مادة</button>
                    <!-- Small options menu could be here for edit grade -->
                </div>
            </div>

            <!-- Subjects Table -->
            <div v-show="openGrades.includes(grade.id)" class="border-t border-gray-100 animate-fade-in">
                 <div v-if="!grade.subjects || grade.subjects.length === 0" class="p-8 text-center text-gray-400">
                    <p>لا توجد مواد مرتبطة بهذه المرحلة بعد.</p>
                    <button @click="openSubjectModal(grade)" class="mt-2 text-secondary font-bold hover:underline">إضافة أول مادة</button>
                 </div>
                 <div v-else class="overflow-x-auto">
                 <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">المادة</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">تقوية شهرية (12 حصة)</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">حصة واحدة</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="subject in grade.subjects" :key="subject.id" class="group hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2 text-sm text-gray-900 font-bold">{{ subject.name }}</td>
                            <td class="px-3 py-2 text-sm text-gray-600 font-bold text-secondary">{{ subject.pivot.price_single }} ₪</td>
                            <td class="px-3 py-2 text-sm text-gray-600">{{ subject.pivot.price_one_session }} ₪</td>
                            <td class="px-3 py-2 text-sm text-right flex items-center gap-2">
                                <button @click="openEditPivot(grade, subject)" class="text-blue-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity p-1" title="تعديل الأسعار">✏️</button>
                                <button @click="detachSubject(grade, subject)" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity p-1" title="إلغاء الربط">🗑️</button>
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
        <p class="mb-4 text-sm text-gray-500">تنبيه: سيتم إضافة المرحلة للسنة الدراسية: <span class="font-bold text-black">{{ academicStore.currentYear?.name || 'غير محدد' }}</span></p>
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
             <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">سعر حزمة الوزارة (لكل المواد)</label>
                 <div class="relative">
                    <input v-model="formGrade.package_price" type="number" placeholder="0" 
                           class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white text-center font-bold text-primary">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">₪</span>
                 </div>
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
            
            <div class="space-y-4">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">سعر حزمة دروس التقوية الشهرية (12 حصة)</label>
                   <div class="relative">
                        <input v-model="formPivot.price_single" type="number" 
                               class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 text-center font-bold text-primary">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">₪</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">سعر الحصة الواحدة (مرة واحدة)</label>
                   <div class="relative">
                        <input v-model="formPivot.price_one_session" type="number" 
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
import { ref, onMounted, reactive, watch } from 'vue'
import api from '../../../api/axios'
import { useToastStore } from '../../../stores/toast'
import { useAcademicStore } from '../../../stores/academic' // Global Store

const academicStore = useAcademicStore()
const grades = ref([])
const allSubjects = ref([])
const loading = ref(false)
const openGrades = ref([])
const showGradeModal = ref(false)
const showSubjectModal = ref(false)
const selectedGrade = ref(null)
const isEditingPivot = ref(false)

const toast = useToastStore()
const formGrade = reactive({ name: '', level_order: '', package_price: 0 })
const formPivot = reactive({ subject_id: null, price_single: 0, price_one_session: 0 })

// Fetch Grades based on Global Store
const fetchGrades = async () => {
  if(!academicStore.currentYearId) return
  loading.value = true
  try {
    const res = await api.get('/api/grades', { params: { year_id: academicStore.currentYearId } })
    grades.value = res.data.data
  } catch(e) { console.error(e) } finally {
    loading.value = false
  }
}

const fetchAllSubjects = async () => {
  try {
    const res = await api.get('/api/subjects')
    allSubjects.value = res.data.data
  } catch(e) { console.error(e) }
}

const toggleGrade = (id) => {
    if(openGrades.value.includes(id)) {
        openGrades.value = openGrades.value.filter(x => x !== id)
    } else {
        openGrades.value.push(id)
    }
}

// Grades Management
const openGradeModal = () => {
    formGrade.name = ''
    formGrade.level_order = ''
    formGrade.package_price = 0
    showGradeModal.value = true
}

const submitGrade = async () => {
    if (!academicStore.currentYearId) {
        toast.error('لم يتم تحديد سنة دراسية')
        return
    }
    
    try {
        const payload = { ...formGrade, academic_year_id: academicStore.currentYearId };
        await api.post('/api/grades', payload)
        toast.success('تم إضافة المرحلة بنجاح 🎉')
        showGradeModal.value = false
        fetchGrades()
    } catch (e) {
        toast.error('حدث خطأ. ربما الاسم مكرر لهذه السنة؟')
    }
}

// Pivot Managment (Attach Subject)
const openSubjectModal = (grade) => {
    selectedGrade.value = grade
    isEditingPivot.value = false
    formPivot.subject_id = allSubjects.value.length > 0 ? allSubjects.value[0].id : null
    formPivot.price_single = 0
    formPivot.price_one_session = 0
    showSubjectModal.value = true
    
    // Refresh global list just in case
    fetchAllSubjects() 
}

const openEditPivot = (grade, subject) => {
    selectedGrade.value = grade
    isEditingPivot.value = true
    formPivot.subject_id = subject.id
    formPivot.price_single = subject.pivot.price_single
    formPivot.price_one_session = subject.pivot.price_one_session
    showSubjectModal.value = true
}

const submitPivot = async () => {
    if(!selectedGrade.value) return;
    try {
        // Attach
        if (isEditingPivot.value) {
            await api.put(`/api/grades/${selectedGrade.value.id}/subjects/${formPivot.subject_id}`, formPivot)
            toast.success('تم تحديث أسعار المادة بنجاح')
        } else {
            await api.post(`/api/grades/${selectedGrade.value.id}/subjects`, formPivot)
            toast.success('تم ربط المادة بنجاح')
        }
        showSubjectModal.value = false
        fetchGrades()
    } catch (e) {
         toast.error(e.response?.data?.message || 'حدث خطأ أثناء الحفظ')
    }
}

const detachSubject = async (grade, subject) => {
    const confirmed = await dialog.deleteConfirm(
        'هل أنت متأكد من إلغاء ربط هذه المادة؟ قد يؤثر ذلك على الشعب المرتبطة.',
        'إلغاء ربط مادة',
        subject.name
    );
    
    if (confirmed) {
        try {
            await api.delete(`/api/grades/${grade.id}/subjects/${subject.id}`)
            toast.success('تم إلغاء الربط بنجاح')
            fetchGrades()
        } catch (e) {
            toast.error('حدث خطأ')
        }
    }
}

const importFromPrevious = async () => {
    const confirmed = await dialog.confirm(
        'سيتم نسخ جميع المراحل والمواد والأسعار من السنة الدراسية السابقة المتاحة إلى السنة الحالية. هل تود المتابعة؟',
        'استيراد البيانات'
    );
    
    if (confirmed) {
        try {
            await api.post('/api/grades/import-from-previous', { 
                target_year_id: academicStore.currentYearId 
            })
            toast.success('تم استيراد البيانات بنجاح 📥')
            fetchGrades()
        } catch (e) {
            toast.error(e.response?.data?.message || 'فشل الاستيراد')
        }
    }
}

// Watch for global year changes
watch(() => academicStore.currentYearId, (newId) => {
    if(newId) fetchGrades()
})

onMounted(async () => {
  if (academicStore.currentYearId) {
      fetchGrades()
  }
  fetchAllSubjects()
})
</script>
