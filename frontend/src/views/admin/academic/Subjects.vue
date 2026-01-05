<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">المواد الدراسية (Master List)</h1>
        <p class="text-gray-500 mt-1 text-sm bg-yellow-50 p-2 rounded border border-yellow-100 inline-block">
            🔔 هذه القائمة الرئيسية للمواد (مثل: عربي، إنجليزي). الأسعار تحدد داخل "المراحل التعليمية".
        </p>
      </div>
      <button @click="openModal" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition flex items-center shadow-md">
        <span class="text-xl ml-2 font-light">+</span> إضافة مادة جديدة
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
    </div>

    <!-- List -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">اسم المادة</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="subject in subjects" :key="subject.id" class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ subject.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <button @click="editSubject(subject)" class="text-indigo-600 hover:text-indigo-900 ml-4 font-medium">تعديل</button>
                        <button @click="deleteSubject(subject)" class="text-red-600 hover:text-red-900 font-medium">حذف</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="subjects.length === 0" class="p-8 text-center text-gray-500 italic">
            لا توجد مواد مضافة بعد. ابدأ بإضافة وحدة.
        </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="showModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl transform transition-all scale-100 border border-gray-100">
        <h3 class="text-2xl font-bold mb-8 text-primary border-b border-gray-100 pb-4">
            {{ isEditing ? '✏️ تعديل مادة' : '✨ إضافة مادة جديدة' }}
        </h3>
        
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم المادة</label>
                <input v-model="form.name" type="text" placeholder="مثال: اللغة العربية" 
                       class="w-full text-lg px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-secondary focus:ring-0 transition-colors bg-gray-50 focus:bg-white">
            </div>

            <div class="mt-10 flex justify-end gap-3">
              <button type="button" @click="showModal = false" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-xl transition duration-200">إلغاء</button>
              <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-light shadow-lg shadow-primary/30 transition duration-200 flex items-center gap-2">
                <span>💾</span> حفظ التغييرات
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

const subjects = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const selectedId = ref(null)

const toast = useToastStore()
const form = reactive({ name: '' })

const fetchSubjects = async () => {
    loading.value = true
    try {
        const res = await api.get('/api/subjects')
        subjects.value = res.data.data
    } catch (e) {
        toast.error('فشل تحميل المواد')
    } finally {
        loading.value = false
    }
}

const openModal = () => {
    isEditing.value = false
    selectedId.value = null
    form.name = ''
    showModal.value = true
}

const editSubject = (subject) => {
    isEditing.value = true
    selectedId.value = subject.id
    form.name = subject.name
    showModal.value = true
}

const submit = async () => {
    if(!form.name) return toast.warning('اسم المادة مطلوب')
    
    try {
        if(isEditing.value) {
            await api.put(`/api/subjects/${selectedId.value}`, form)
            toast.success('تم التحديث بنجاح')
        } else {
            await api.post('/api/subjects', form)
            toast.success('تم الإضافة بنجاح')
        }
        showModal.value = false
        fetchSubjects()
    } catch (e) {
        toast.error(e.response?.data?.message || 'حدث خطأ')
    }
}

const deleteSubject = async (subject) => {
    if(!confirm(`حذف مادة "${subject.name}"؟ سيتم إزالتها من جميع المراحل المرتبطة بها!`)) return;
    try {
        await api.delete(`/api/subjects/${subject.id}`)
        toast.success('تم الحذف')
        fetchSubjects()
    } catch (e) {
        toast.error('تعذر الحذف')
    }
}

onMounted(() => fetchSubjects())
</script>
