// Replace window.confirm and local modal logic with dialogStore

const dialog = useDialogStore()

// ...

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

// ...

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
            toast.error('تعذر الحذف. ربما يحتوي الفصل على بيانات مرتبطة.')
        }
    }
}
