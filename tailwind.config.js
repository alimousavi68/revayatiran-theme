/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./**/*.php",
        "./assets/js/**/*.js",
    ],
    darkMode: ["class", '[data-theme="dark"]'],
    theme: {
        container: {
            center: true,
            padding: "16px",
            screens: {
                sm: "640px",
                md: "768px",
                lg: "1024px",
                xl: "1280px",
                "2xl": "1300px",
            },
        },
        extend: {
            fontFamily: {
                sans: ["IRANYekanX", "sans-serif"],
            },
            colors: {
                primary: {
                    DEFAULT: "var(--primary)",
                    hover: "var(--accent-strong)",
                    dark: "var(--primary-dark, var(--accent-strong))",
                },
                accent: {
                    DEFAULT: "var(--accent)",
                    strong: "var(--accent-strong)",
                    deep: "var(--accent-deep)",
                },
                bg: {
                    DEFAULT: "var(--bg)",
                    elevated: "var(--bg-elevated)",
                    soft: "var(--bg-soft)",
                },
                surface: {
                    DEFAULT: "var(--surface)",
                    strong: "var(--surface-strong)",
                    dark: "var(--surface-dark)",
                    darker: "var(--surface-darker)",
                },
                text: {
                    DEFAULT: "var(--text)",
                    muted: "var(--text-muted)",
                    soft: "var(--text-soft)",
                },
                border: {
                    DEFAULT: "var(--border)",
                    strong: "var(--border-strong)",
                },
            },
        },
    },
    plugins: [],
};
