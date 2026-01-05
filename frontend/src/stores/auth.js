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
      // Token Based Auth
      const response = await api.post("/api/login", credentials);
      const token = response.data.access_token;

      localStorage.setItem("token", token);
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`; // Update axios instance

      this.user = response.data.user;
    },
    async logout() {
      await api.post("/api/logout");
      localStorage.removeItem("token");
      delete api.defaults.headers.common["Authorization"];
      this.user = null;
    },
    async checkAuth() {
      const token = localStorage.getItem("token");
      if (token) {
        api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        await this.fetchUser();
      }
    },
  },
});
