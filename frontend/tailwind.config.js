/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#00205B", // Deep Navy Blue
          light: "#1e3a8a",
          dark: "#001235",
        },
        secondary: {
          DEFAULT: "#FFD700", // Gold/Yellow
          light: "#FFE033",
          dark: "#B29600",
        },
      },
      fontFamily: {
        sans: [
          "Dubai",
          "Almarai",
          "ui-sans-serif",
          "system-ui",
          "-apple-system",
          "BlinkMacSystemFont",
          "Segoe UI",
          "Roboto",
          "Helvetica Neue",
          "Arial",
          "sans-serif",
        ],
      },
    },
  },
  plugins: [],
};
