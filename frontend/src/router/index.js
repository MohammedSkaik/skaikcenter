import { createRouter, createWebHistory } from "vue-router";
// import HomeView from '../views/HomeView.vue'

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
      ],
    },
  ],
});

export default router;
