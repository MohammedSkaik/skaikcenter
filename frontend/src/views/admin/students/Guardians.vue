<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-primary">👨‍👩‍👧‍👦 أولياء الأمور</h1>
        <p class="text-gray-500 mt-1 text-sm">إدارة بيانات أولياء الأمور والتواصل معهم</p>
      </div>
      <button @click="openModal" class="bg-primary text-secondary font-bold px-6 py-2 rounded-xl hover:bg-primary-light hover:text-white transition shadow-lg shadow-primary/20">
        + ولي أمر جديد
      </button>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex gap-4">
        <div class="relative flex-1">
            <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="بحث بالاسم، رقم الهاتف، أو الهوية..." class="w-full pl-4 pr-10 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
            <span class="absolute right-3 top-2.5 text-gray-400">🔍</span>
        </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الاسم</th>
            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">رقم الهاتف</th>
            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">رقم الهوية</th>
            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="guardians.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">لا توجد بيانات.</td>
          </tr>
          <tr v-for="guardian in guardians" :key="guardian.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary-dark font-bold ml-3">
                  {{ guardian.name.charAt(0) }}
                </div>
                <div class="text-sm font-bold text-gray-900">{{ guardian.name }}</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
               <span class="text-sm text-gray-600 font-mono dir-ltr select-all">{{ guardian.phone }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ guardian.identification_number || '-' }}</td>
             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ guardian.email || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
              <button @click="editGuardian(guardian)" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-lg transition">تعديل</button>
              <button @click="deleteGuardian(guardian)" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-lg transition">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
          <div class="flex justify-between w-full">
              <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 py-1 rounded border bg-white disabled:opacity-50">السابق</button>
              <span class="text-sm text-gray-700 self-center">صفحة {{ pagination.current_page }} من {{ pagination.last_page }}</span>
              <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 py-1 rounded border bg-white disabled:opacity-50">التالي</button>
          </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary-dark/80 backdrop-blur-sm" @click.self="closeModal">
      <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-2xl scale-100 animate-scale-in">
        <h3 class="text-2xl font-bold mb-6 text-primary border-b border-gray-100 pb-4">
            {{ isEditing ? 'تعديل بيانات ولي أمر' : 'إضافة ولي أمر جديد' }}
        </h3>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">الاسم رباعي <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
          </div>
          
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">رقم الهاتف <span class="text-red-500">*</span></label>
            <input v-model="form.phone" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-left" dir="ltr" placeholder="059xxxxxxx">
             <p class="text-xs text-gray-400 mt-1 text-right">يجب أن يكون فريداً في النظام.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">رقم الهوية</label>
                <input v-model="form.identification_number" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">البريد الإلكتروني</label>
                <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
              </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">ملاحظات</label>
            <textarea v-model="form.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
          </div>

          <div class="pt-4 flex justify-end gap-3">
            <button type="button" @click="closeModal" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-bold">إلغاء</button>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-light font-bold shadow-lg shadow-primary/30">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '../../../api/axios'
import { useToastStore } from '../../../stores/toast'
import { useDialogStore } from '../../../stores/dialog'
import { debounce } from 'lodash'

const toast = useToastStore()
const dialog = useDialogStore()
const guardians = ref([])
const loading = ref(false)
const searchQuery = ref('')
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 15 })

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = reactive({
    name: '',
    phone: '',
    email: '',
    identification_number: '',
    notes: ''
})

const fetchGuardians = async (page = 1) => {
    loading.value = true
    try {
        const res = await api.get('/api/guardians', {
            params: {
                page,
                search: searchQuery.value
            }
        })
        guardians.value = res.data.data.data
        pagination.value = {
            current_page: res.data.data.current_page,
            last_page: res.data.data.last_page,
            total: res.data.data.total,
            per_page: res.data.data.per_page
        }
    } catch (e) {
        toast.error('فشل تحميل البيانات')
    } finally {
        loading.value = false
    }
}

const handleSearch = debounce(() => {
    fetchGuardians(1)
}, 300)

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchGuardians(page)
    }
}

const openModal = () => {
    isEditing.value = false
    editingId.value = null
    resetForm()
    showModal.value = true
}

const editGuardian = (guardian) => {
    isEditing.value = true
    editingId.value = guardian.id
    form.name = guardian.name
    form.phone = guardian.phone
    form.email = guardian.email
    form.identification_number = guardian.identification_number
    form.notes = guardian.notes
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    resetForm()
}

const resetForm = () => {
    form.name = ''
    form.phone = ''
    form.email = ''
    form.identification_number = ''
    form.notes = ''
}

const submitForm = async () => {
    try {
        if (isEditing.value) {
            await api.put(`/api/guardians/${editingId.value}`, form)
            toast.success('تم تحديث البيانات بنجاح')
        } else {
            await api.post('/api/guardians', form)
            toast.success('تم إضافة ولي الأمر بنجاح')
        }
        closeModal()
        fetchGuardians(pagination.value.current_page)
    } catch (e) {
         if (e.response && e.response.status === 422) {
             const errors = Object.values(e.response.data.errors).flat().join('\n')
             toast.error(errors)
         } else {
             toast.error('حدث خطأ أثناء الحفظ')
         }
    }
}

const deleteGuardian = async (guardian) => {
    const confirmed = await dialog.deleteConfirm(
        'قد يكون هناك طلاب مرتبطين بهذا الحساب.',
        'حذف ولي أمر',
        guardian.name
    );

    if (confirmed) {
        try {
            await api.delete(`/api/guardians/${guardian.id}`)
            toast.success('تم الحذف بنجاح')
            fetchGuardians(pagination.value.current_page)
        } catch (e) {
            toast.error('فشل الحذف')
        }
    }
}

onMounted(() => {
    fetchGuardians()
})
</script>
