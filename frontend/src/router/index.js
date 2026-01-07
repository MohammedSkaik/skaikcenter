import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: () => import("../views/HomeView.vue"),
    },
    {
      path: "/login",
      name: "login",
      component: () => import("../views/Login.vue"),
    },
    {
      path: "/admin",
      name: "admin",
      component: () => import("../views/admin/Layout.vue"),
      meta: { requiresAuth: true }, // Add meta field
      children: [
        {
          path: "managers",
          name: "admin-managers",
          component: () => import("../views/admin/Managers.vue"),
        },
        {
          path: "roles",
          name: "admin-roles",
          component: () => import("../views/admin/Roles.vue"),
        },
        // Academic Routes
        {
          path: "academic/years",
          name: "academic-years",
          component: () => import("../views/admin/academic/Years.vue"),
        },
        {
          path: "academic/education-stages",
          name: "academic-education-stages",
          component: () =>
            import("../views/admin/academic/EducationStages.vue"),
        },
        {
          path: "academic/subjects",
          name: "academic-subjects",
          component: () => import("../views/admin/academic/Subjects.vue"),
        },
        {
          path: "academic/rooms",
          name: "academic-rooms",
          component: () => import("../views/admin/academic/Rooms.vue"),
        },
        {
          path: "academic/classes",
          name: "academic-classes",
          component: () => import("../views/admin/academic/Classes.vue"),
        },
        // Student Affairs Routes
        {
          path: "students/guardians",
          name: "students-guardians",
          component: () => import("../views/admin/students/Guardians.vue"),
        },
      ],
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Attempt to fetch user if not present (handled by simple check or session logic)
  if (!authStore.user) {
    await authStore.fetchUser();
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: "login" });
  } else if (to.name === "login" && authStore.isAuthenticated) {
    next({ name: "admin-managers" }); // Redirect logged-in users away from login
  } else {
    next();
  }
});

export default router;
