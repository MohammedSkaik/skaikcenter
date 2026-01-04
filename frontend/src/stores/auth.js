import { defineStore } from "pinia";
import api from "../api/axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    loading: false,
  }),
  getters: {
    isAdmin: (state) =>
      state.user?.type === "super_admin" || state.user?.type === "center_admin",
    isAuthenticated: (state) => !!state.user,
  },
  actions: {
    async fetchUser() {
      this.loading = true;
      try {
        const response = await api.get("/api/user");
        this.user = response.data;
      } catch (error) {
        this.user = null;
      } finally {
        this.loading = false;
      }
    },
    async login(credentials) {
      await api.get("/sanctum/csrf-cookie");
      await api.post("/login", credentials);
      await this.fetchUser();
    },
    async logout() {
      await api.post("/logout");
      this.user = null;
    },
  },
});
