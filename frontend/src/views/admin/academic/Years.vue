<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-primary">🗓️ السنوات الدراسية</h1>
        <p class="text-gray-500 mt-1 text-sm">إدارة السنوات والفصول الدراسية في النظام</p>
      </div>
      <button @click="openYearModal" class="bg-primary text-secondary font-bold px-6 py-2 rounded-xl hover:bg-primary-light hover:text-white transition shadow-lg shadow-primary/20 flex items-center gap-2">
        <span>+ سنة جديدة</span>
      </button>
    </div>

    <!-- Content -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
    </div>

    <div v-else class="space-y-6">
        <div v-if="years.length === 0" class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <p class="text-gray-400 font-bold mb-4">لا توجد سنوات دراسية مضافة.</p>
            <button @click="openYearModal" class="text-secondary font-bold hover:underline">أضف السنة الأولى</button>
        </div>

        <div v-else v-for="year in years" :key="year.id" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Year Header -->
            <div class="p-5 flex justify-between items-center bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors" @click="toggleYear(year.id)">
                <div class="flex items-center gap-4">
                    <span class="text-2xl transition-transform duration-300" :class="{'rotate-90': openYears.includes(year.id)}">▶</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            {{ year.name }}
                            <span v-if="year.status === 'active'" class="text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full border border-green-200">نشط</span>
                        </h3>
                        <p class="text-xs text-gray-500 font-mono">{{ year.start_date }} - {{ year.end_date }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click.stop="openEditYear(year)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="تعديل السنة">✏️</button>
                    <button @click.stop="deleteYear(year)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="حذف السنة">🗑️</button>
                </div>
            </div>

            <!-- Semesters List -->
            <div v-show="openYears.includes(year.id)" class="border-t border-gray-100 p-4 bg-white animate-fade-in">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-700 text-sm">الفصول الدراسية</h4>
                    <button @click="openSemesterModal(year)" class="text-xs bg-secondary/10 text-secondary-dark px-3 py-1.5 rounded-lg hover:bg-secondary/20 transition font-bold">+ إضافة فصل</button>
                </div>

                <div v-if="year.semesters && year.semesters.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="semester in year.semesters" :key="semester.id" class="border border-gray-200 rounded-lg p-3 flex justify-between items-center hover:shadow-sm transition bg-dots">
                        <div>
                             <h5 class="font-bold text-gray-800 text-sm">{{ semester.name }}</h5>
                             <span class="text-[10px] text-gray-400 font-mono">{{ semester.start_date }} -> {{ semester.end_date }}</span>
                        </div>
                        <div class="flex gap-1">
                             <button @click="openEditSemester(year, semester)" class="p-1.5 text-blue-400 hover:text-blue-600 rounded hover:bg-blue-50">✏️</button>
                             <button @click="deleteSemester(year, semester)" class="p-1.5 text-red-400 hover:text-red-600 rounded hover:bg-red-50">🗑️</button>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-400 text-center py-2 italic bg-gray-50 rounded border border-dashed">لا توجد فصول دراسية</p>
            </div>
        </div>
    </div>

    <!-- Year Modal -->
    <div v-if="showYearModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="closeYearModal">
        <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl scale-100 animate-scale-in">
             <h3 class="text-2xl font-bold mb-6 text-primary border-b border-gray-100 pb-4">
                {{ isEditingYear ? 'تعديل السنة الدراسية' : 'إضافة سنة دراسية جديدة' }}
             </h3>
             <form @submit.prevent="submitYear" class="space-y-5">
                 <div>
                     <label class="block text-sm font-bold text-gray-700 mb-1">اسم السنة</label>
                     <input v-model="formYear.name" type="text" placeholder="مثال: 2025/2026" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 bg-gray-50">
                 </div>
                 <div class="grid grid-cols-2 gap-4">
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ البدء</label>
                         <input v-model="formYear.start_date" type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 bg-gray-50">
                     </div>
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ الانتهاء</label>
                         <input v-model="formYear.end_date" type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 bg-gray-50">
                     </div>
                 </div>
                 <div class="flex justify-end gap-3 mt-8">
                     <button type="button" @click="closeYearModal" class="px-6 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition">إلغاء</button>
                     <button type="submit" class="px-8 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-light shadow-lg shadow-primary/30 transition">حفظ</button>
                 </div>
             </form>
        </div>
    </div>

    <!-- Semester Modal -->
    <div v-if="showSemesterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="closeSemesterModal">
        <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl scale-100 animate-scale-in">
             <h3 class="text-2xl font-bold mb-6 text-secondary-dark border-b border-gray-100 pb-4">
                {{ isEditingSemester ? 'تعديل الفصل الدراسي' : 'إضافة فصل دراسي' }}
             </h3>
             <p class="text-sm text-gray-400 mb-4">تابع للسنة: <span class="font-bold text-black">{{ selectedYear?.name }}</span></p>
             <form @submit.prevent="submitSemester" class="space-y-5">
                 <div>
                     <label class="block text-sm font-bold text-gray-700 mb-1">اسم الفصل</label>
                     <input v-model="formSemester.name" type="text" placeholder="مثال: الفصل الأول" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 bg-gray-50">
                 </div>
                 <div class="grid grid-cols-2 gap-4">
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ البدء</label>
                         <input v-model="formSemester.start_date" type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 bg-gray-50">
                     </div>
                     <div>
                         <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ الانتهاء</label>
                         <input v-model="formSemester.end_date" type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 bg-gray-50">
                     </div>
                 </div>
                 <div class="flex justify-end gap-3 mt-8">
                     <button type="button" @click="closeSemesterModal" class="px-6 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition">إلغاء</button>
                     <button type="submit" class="px-8 py-2 bg-secondary text-primary font-bold rounded-xl hover:bg-secondary-light shadow-lg shadow-secondary/30 transition">حفظ</button>
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
import { useDialogStore } from '../../../stores/dialog' // Global Dialog

const toast = useToastStore()
const dialog = useDialogStore()
const years = ref([])
const loading = ref(false)
const openYears = ref([])

// Year Modal State
const showYearModal = ref(false)
const isEditingYear = ref(false)
const editingYearId = ref(null)
const formYear = reactive({ name: '', start_date: '', end_date: '' })

// Semester Modal State
const showSemesterModal = ref(false)
const isEditingSemester = ref(false)
const selectedYear = ref(null)
const editingSemesterId = ref(null)
const formSemester = reactive({ name: '', start_date: '', end_date: '' })

const fetchYears = async () => {
    loading.value = true
    try {
        const res = await api.get('/api/academic-years')
        years.value = res.data.data
        // Auto open active year if exists could be nice, or just keep first one open
        if(years.value.length > 0 && openYears.value.length === 0) {
           // openYears.value.push(years.value[0].id)
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const toggleYear = (id) => {
    if(openYears.value.includes(id)) {
        openYears.value = openYears.value.filter(x => x !== id)
    } else {
        openYears.value.push(id)
    }
}

// Year Actions
const openYearModal = () => {
    isEditingYear.value = false
    editingYearId.value = null
    formYear.name = ''
    formYear.start_date = ''
    formYear.end_date = ''
    showYearModal.value = true
}

const openEditYear = (year) => {
    isEditingYear.value = true
    editingYearId.value = year.id
    formYear.name = year.name
    formYear.start_date = year.start_date
    formYear.end_date = year.end_date
    showYearModal.value = true
}

const closeYearModal = () => showYearModal.value = false

const submitYear = async () => {
    try {
        if(isEditingYear.value) {
            await api.put(`/api/academic-years/${editingYearId.value}`, formYear)
            toast.success('تم تحديث السنة الدراسية')
        } else {
            await api.post('/api/academic-years', formYear)
            toast.success('تم إضافة سنة دراسية جديدة')
        }
        closeYearModal()
        fetchYears()
    } catch (e) {
        toast.error('حدث خطأ. تأكد من صحة البيانات.')
    }
}

const deleteYear = async (year) => {
    const confirmed = await dialog.deleteConfirm(
        'سيتم حذف السنة الدراسية وكافة البيانات المرتبطة بها (شعب، حصص، درجات، مالية).',
        'حذف سنة دراسية',
        year.name
    );
    
    if (confirmed) {
         try {
             await api.delete(`/api/academic-years/${year.id}`)
             toast.success('تم حذف السنة الدراسية نهائياً')
             fetchYears()
         } catch (e) {
             toast.error('فشل الحذف. قد تكون هناك قيود على البيانات.')
         }
    }
}

// Semester Actions
const openSemesterModal = (year) => {
    selectedYear.value = year
    isEditingSemester.value = false
    editingSemesterId.value = null
    formSemester.name = ''
    formSemester.start_date = year.start_date // Default to year start
    formSemester.end_date = year.end_date     // Default to year end
    showSemesterModal.value = true
}

const openEditSemester = (year, semester) => {
    selectedYear.value = year
    isEditingSemester.value = true
    editingSemesterId.value = semester.id
    formSemester.name = semester.name
    formSemester.start_date = semester.start_date
    formSemester.end_date = semester.end_date
    showSemesterModal.value = true
}
const closeSemesterModal = () => showSemesterModal.value = false

const submitSemester = async () => {
    if(!selectedYear.value) return
    try {
        if(isEditingSemester.value) {
            await api.put(`/api/semesters/${editingSemesterId.value}`, formSemester)
            toast.success('تم تحديث الفصل الدراسي')
        } else {
            await api.post(`/api/academic-years/${selectedYear.value.id}/semesters`, formSemester)
            toast.success('تم إضافة فصل دراسي')
        }
        closeSemesterModal()
        fetchYears()
    } catch (e) {
        toast.error('حدث خطأ')
    }
}

const deleteSemester = async (year, semester) => {
    const confirmed = await dialog.deleteConfirm(
        'سيتم حذف الفصل الدراسي وكافة الشعب والجداول المرتبطة به.',
        'حذف فصل دراسي',
        semester.name
    );

    if (confirmed) {
        try {
            await api.delete(`/api/semesters/${semester.id}`)
            toast.success('تم حذف الفصل الدراسي نهائياً')
            fetchYears()
        } catch (e) {
            toast.error('تعذر الحذف.')
        }
    }
}

onMounted(() => {
    fetchYears()
})
</script>

<style scoped>
.bg-dots {
    background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
    background-size: 10px 10px;
}
</style>
