import type { Config } from "tailwindcss";

const config: Config = {
  darkMode: "class",
  content: ["./app/**/*.{ts,tsx}", "./components/**/*.{ts,tsx}"],
  theme: {
    // Size-only font scale: line-heights come from the Figma text styles (1.35 / 1.4), set explicitly.
    fontSize: { xs: "12px", sm: "14px", base: "16px", lg: "18px", xl: "20px", "2xl": "24px", "3xl": "30px", "4xl": "36px", "5xl": "48px", "6xl": "60px", "7xl": "72px" },
    extend: {
      colors: {
        brand: {
          50: "#EEF5FF",
          100: "#D9E8FF",
          200: "#B3D1FF",
          400: "#4D94FF",
          500: "#0A6CFF",
          600: "#0057D9",
          700: "#0045AD",
        },
        amber: {
          50: "#FFF8E5",
          100: "#FFEFC2",
          400: "#FFC633",
          500: "#FFB800",
          600: "#E0A100",
        },
        // Semantic tokens from Figma variables; light/dark values live in app/globals.css.
        ink: "rgb(var(--ink) / <alpha-value>)",
        night: "#0F172A",
        card: "rgb(var(--card) / <alpha-value>)",
        surface: "rgb(var(--surface) / <alpha-value>)",
        page: "rgb(var(--page) / <alpha-value>)", // page background ("base" would clash with the text-base font size)
        muted: "rgb(var(--muted) / <alpha-value>)",
        line: "rgb(var(--line) / <alpha-value>)",
        link: "rgb(var(--link) / <alpha-value>)",
        "brand-surface": "rgb(var(--brand-surface) / <alpha-value>)",
        "amber-surface": "rgb(var(--amber-surface) / <alpha-value>)",
      },
      fontFamily: {
        sans: ["var(--font-inter)", "system-ui", "sans-serif"],
      },
      borderRadius: { xl2: "20px", "4xl": "30px" },
      maxWidth: { page: "1440px" },
      boxShadow: {
        figma: "0px 12px 30px 0px rgba(13, 38, 77, 0.07)",
        soft: "0 8px 30px -12px rgba(15, 23, 42, 0.18)",
        card: "0 2px 10px -2px rgba(15, 23, 42, 0.08)",
      },
      keyframes: {
        marquee: { "0%": { transform: "translateX(0)" }, "100%": { transform: "translateX(-50%)" } },
        "marquee-rev": { "0%": { transform: "translateX(-50%)" }, "100%": { transform: "translateX(0)" } },
        drive: { "0%": { offsetDistance: "0%" }, "100%": { offsetDistance: "100%" } },
      },
      animation: {
        marquee: "marquee 40s linear infinite",
        "marquee-rev": "marquee-rev 40s linear infinite",
      },
    },
  },
  plugins: [],
};
export default config;
