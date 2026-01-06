import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "../api/axios";

export const useAcademicStore = defineStore("academic", () => {
  const years = ref([]);
  const semesters = ref([]);
  const currentYear = ref(null);
  const currentSemester = ref(null);
  const loading = ref(false);

  // Getters
  const currentYearId = computed(() => currentYear.value?.id);
  const currentSemesterId = computed(() => currentSemester.value?.id);

  // Actions
  const fetchCycles = async () => {
    loading.value = true;
    try {
      const res = await api.get("/api/academic-years");
      years.value = res.data.data;

      // Logic to restore selection or pick latest
      const storedYearId = localStorage.getItem("skaik_current_year_id");
      if (storedYearId) {
        const found = years.value.find((y) => y.id == storedYearId);
        if (found) {
          setYear(found);
          return;
        }
      }

      // Default to latest
      if (years.value.length > 0) {
        setYear(years.value[years.value.length - 1]);
      }
    } catch (e) {
      console.error("Failed to load academic cycles", e);
    } finally {
      loading.value = false;
    }
  };

  const setYear = (year) => {
    currentYear.value = year;
    localStorage.setItem("skaik_current_year_id", year.id);

    // Update semesters based on year
    if (year && year.semesters) {
      semesters.value = year.semesters;

      // Restore semester or default to first
      const storedSemId = localStorage.getItem("skaik_current_sem_id");
      let foundSem = null;

      if (storedSemId) {
        foundSem = semesters.value.find((s) => s.id == storedSemId);
      }

      if (foundSem) {
        setSemester(foundSem);
      } else if (semesters.value.length > 0) {
        setSemester(semesters.value[0]);
      } else {
        currentSemester.value = null;
        localStorage.removeItem("skaik_current_sem_id");
      }
    } else {
      semesters.value = [];
      currentSemester.value = null;
    }
  };

  const setSemester = (semester) => {
    currentSemester.value = semester;
    localStorage.setItem("skaik_current_sem_id", semester.id);
  };

  return {
    years,
    semesters,
    currentYear,
    currentSemester,
    currentYearId,
    currentSemesterId,
    loading,
    fetchCycles,
    setYear,
    setSemester,
  };
});
