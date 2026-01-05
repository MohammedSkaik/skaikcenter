import { defineStore } from "pinia";

export const useToastStore = defineStore("toast", {
  state: () => ({
    toasts: [],
  }),
  actions: {
    add(toast) {
      const id = Date.now();
      const newToast = { id, ...toast };
      this.toasts.push(newToast);
      setTimeout(() => this.remove(id), 5000);
    },
    remove(id) {
      this.toasts = this.toasts.filter((t) => t.id !== id);
    },
    success(message) {
      this.add({ type: "success", message });
    },
    error(message) {
      this.add({ type: "error", message });
    },
    info(message) {
      this.add({ type: "info", message });
    },
  },
});
