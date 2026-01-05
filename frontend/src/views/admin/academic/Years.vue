<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">السنوات الدراسية والفصول</h1>
        <p class="text-gray-500 mt-1 text-sm">هنا يمكنك إدارة السنوات الدراسية (مثل 2024/2025) والفصول التابعة لها (الفصل الأول، الثاني، الصيفي). تأكد من صحة التواريخ لتجنب التعارض.</p>
      </div>
      <button @click="openYearModal" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition flex items-center shadow-md hover:shadow-lg transform active:scale-95 duration-200">
        <span class="text-xl ml-2 font-light">+</span> إضافة سنة دراسية
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Years List -->
    <div v-else class="space-y-6">
      <div v-for="year in years" :key="year.id" class="bg-white shadow rounded-xl overflow-hidden border border-gray-200 transition hover:shadow-md">
        <!-- Year Header -->
        <div class="bg-gray-50/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-100 gap-4">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
               {{ year.name.substring(0, 4) }}
            </div>
            <div>
               <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                 {{ year.name }}
                 <span :class="{'bg-green-100 text-green-700 ring-green-600/20': year.status === 'active', 'bg-gray-100 text-gray-600 ring-gray-500/10': year.status !== 'active'}" class="px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset">
                   {{ year.status === 'active' ? 'نشط' : 'مؤرشف' }}
                 </span>
               </h3>
               <div class="text-sm text-gray-500 mt-1 flex items-center gap-4">
                  <span title="تاريخ البداية">📅 {{ formatDate(year.start_date) }}</span>
                  <span class="text-gray-300">|</span>
                  <span title="تاريخ النهاية">🏁 {{ formatDate(year.end_date) }}</span>
               </div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button @click="openSemesterModal(year)" class="text-sm bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 px-4 py-2 rounded-lg transition shadow-sm font-medium">
              + إضافة فصل
            </button>
          </div>
        </div>

        <!-- Semesters List -->
        <div class="px-6 py-5 bg-white">
          <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center">
            <span class="ml-2 w-1 h-4 bg-indigo-500 rounded-full"></span>
            الفصول الدراسية
          </h4>
          
          <div v-if="year.semesters.length === 0" class="text-sm text-gray-400 italic bg-gray-50 p-4 rounded-lg text-center border border-dashed border-gray-200">
             ⚠️ لا يوجد فصول مضافة لهذه السنة بعد.
          </div>
          
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="semester in year.semesters" :key="semester.id" class="relative group bg-gray-50 hover:bg-indigo-50/50 p-4 rounded-xl border border-gray-200 hover:border-indigo-200 transition-all duration-200">
               <div class="flex justify-between items-start mb-2">
                 <span class="font-bold text-gray-800">{{ semester.name }}</span>
                 <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="editSemester(year, semester)" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg" title="تعديل">✏️</button>
                    <button @click="deleteSemester(year, semester)" class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg" title="حذف">🗑️</button>
                 </div>
               </div>
               <div class="text-xs text-gray-500 space-y-1">
                  <div class="flex justify-between"><span>البداية:</span> <span class="font-medium text-gray-700">{{ formatDate(semester.start_date) || 'غير محدد' }}</span></div>
                  <div class="flex justify-between"><span>النهاية:</span> <span class="font-medium text-gray-700">{{ formatDate(semester.end_date) || 'غير محدد' }}</span></div>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Year Modal -->
    <div v-if="showYearModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm transition-all" @click.self="showYearModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl scale-100 transition-transform">
        <h3 class="text-2xl font-bold mb-8 text-primary border-b border-gray-100 pb-4">🎉 إضافة سنة دراسية جديدة</h3>
        
        <form @submit.prevent="submitYear" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم السنة الدراسية</label>
                <input v-model="formYear.name" type="text" placeholder="مثال: 2024/2025" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white" autofocus>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">تاريخ البداية 📅</label>
                    <input v-model="formYear.start_date" type="date" 
                           class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">تاريخ النهاية 🏁</label>
                    <input v-model="formYear.end_date" type="date" 
                           class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer">
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-3">
              <button type="button" @click="showYearModal = false" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-light shadow-lg shadow-primary/30 transition duration-200">حفظ البيانات</button>
            </div>
        </form>
      </div>
    </div>
    
    <!-- Semester Modal -->
    <div v-if="showSemesterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="closeSemesterModal">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl">
        <h3 class="text-2xl font-bold mb-8 text-primary border-b border-gray-100 pb-4">
          {{ isEditingSemester ? '✏️ تعديل الفصل الدراسي' : '➕ إضافة فصل دراسي جديد' }}
          <span class="text-sm font-normal text-gray-500 mr-2">({{ selectedYear?.name }})</span>
        </h3>
        
        <form @submit.prevent="submitSemester" class="space-y-6">
            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-sm border border-blue-100 flex items-start">
               <span class="ml-2 text-xl">ℹ️</span>
               <div>
                  يجب أن يكون تاريخ الفصل ضمن نطاق السنة الدراسية:
                  <div class="font-bold mt-1 text-base" dir="ltr">
                    {{ formatDate(selectedYear?.start_date) }} ➡️ {{ formatDate(selectedYear?.end_date) }}
                  </div>
               </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم الفصل</label>
                <input v-model="formSemester.name" type="text" placeholder="مثال: الفصل الأول" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white">
            </div>
             <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">بداية الفصل</label>
                    <input v-model="formSemester.start_date" type="date" 
                           class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نهاية الفصل</label>
                    <input v-model="formSemester.end_date" type="date" 
                           class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer">
                </div>
            </div>
             <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الحالة</label>
                <select v-model="formSemester.status" 
                        class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white cursor-pointer appearance-none">
                    <option value="active">نشط (الطلاب يسجلون فيه)</option>
                    <option value="inactive">مؤرشف / قادم</option>
                </select>
            </div>

            <div class="mt-10 flex justify-end gap-3">
              <button type="button" @click="closeSemesterModal" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-secondary text-primary font-bold rounded-xl hover:bg-secondary-light shadow-lg shadow-secondary/30 transition duration-200">
                 {{ isEditingSemester ? 'تحديث' : 'حفظ' }}
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

const years = ref([])
const loading = ref(false)
const showYearModal = ref(false)
const showSemesterModal = ref(false)
const isEditingSemester = ref(false)
const selectedYear = ref(null)
const selectedSemesterId = ref(null)

const toast = useToastStore()

const formYear = reactive({ name: '', start_date: '', end_date: '' })
const formSemester = reactive({ name: '', start_date: '', end_date: '', status: 'active' })

// Formatter
const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}-${month}-${year}`;
}

// Fetch Data
const fetchYears = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/academic-years')
    years.value = res.data.data
  } catch (e) {
    toast.error('تعذر تحميل البيانات. تأكد من الاتصال بالسيرفر.')
  } finally {
    loading.value = false
  }
}

// Year Logic
const openYearModal = () => {
    Object.assign(formYear, { name: '', start_date: '', end_date: '' })
    showYearModal.value = true
}

const submitYear = async () => {
    if(!formYear.name || !formYear.start_date || !formYear.end_date) {
        toast.warning('يرجى تعبئة جميع الحقول المطلوبة للسنة الدراسية')
        return;
    }
    try {
        await api.post('/api/academic-years', formYear)
        toast.success('تم إضافة السنة بنجاح 🎉')
        showYearModal.value = false
        fetchYears()
    } catch (e) {
        toast.error('حدث خطأ أثناء الحفظ. ربما الاسم مكرر؟')
    }
}

// Semester Logic
const openSemesterModal = (year) => {
    selectedYear.value = year
    isEditingSemester.value = false
    selectedSemesterId.value = null
    Object.assign(formSemester, { name: '', start_date: '', end_date: '', status: 'active' })
    showSemesterModal.value = true
}

const editSemester = (year, semester) => {
    selectedYear.value = year
    isEditingSemester.value = true
    selectedSemesterId.value = semester.id
    // Simple clone of existing dates/names
    // Note: Assuming API returns YYYY-MM-DD or ISO. If ISO includes time, split it.
    Object.assign(formSemester, { 
        name: semester.name, 
        start_date: semester.start_date ? semester.start_date.split('T')[0] : '', 
        end_date: semester.end_date ? semester.end_date.split('T')[0] : '', 
        status: semester.status 
    })
    showSemesterModal.value = true
}

const validateSemesterDates = () => {
    const yearStart = new Date(selectedYear.value.start_date)
    const yearEnd = new Date(selectedYear.value.end_date)
    const semStart = new Date(formSemester.start_date)
    const semEnd = new Date(formSemester.end_date)

    if (semStart < yearStart || semEnd > yearEnd) {
        toast.error(`تواريخ الفصل يجب أن تكون داخل نطاق السنة (${selectedYear.value.start_date} - ${selectedYear.value.end_date})`)
        return false
    }
    if (semStart >= semEnd) {
        toast.error('تاريخ نهاية الفصل يجب أن يكون بعد تاريخ البداية')
        return false
    }
    
    // Checks for overlapping could be added here if we had full list of other semesters locally
    return true
}

const submitSemester = async () => {
    if(!formSemester.name || !formSemester.start_date || !formSemester.end_date) {
        toast.warning('يرجى تعبئة الاسم وتواريخ البداية والنهاية')
        return
    }

    if (!validateSemesterDates()) return;

    try {
        if (isEditingSemester.value) {
            await api.put(`/api/semesters/${selectedSemesterId.value}`, { ...formSemester, academic_year_id: selectedYear.value.id }) // Ensure ID is passed if backend needs it, usually PUT api/semesters/{id} is enough
            toast.success('تم تحديث الفصل بنجاح ✨')
        } else {
            await api.post(`/api/academic-years/${selectedYear.value.id}/semesters`, formSemester)
            toast.success('تم إضافة الفصل بنجاح ✨')
        }
        showSemesterModal.value = false
        fetchYears()
    } catch (e) {
        toast.error(e.response?.data?.message || 'حدث خطأ أثناء الحفظ')
    }
}

const deleteSemester = async (year, semester) => {
    if (!confirm(`هل أنت متأكد من حذف ${semester.name}؟ لا يمكن التراجع عن هذا الإجراء.`)) return;
    
    try {
        await api.delete(`/api/semesters/${semester.id}`)
        toast.success('تم حذف الفصل')
        fetchYears()
    } catch (e) {
        toast.error('تعذر الحذف. ربما يحتوي الفصل على بيانات مرتبطة.')
    }
}


const closeSemesterModal = () => showSemesterModal.value = false

onMounted(() => {
  fetchYears()
})
</script>
