import type { Config } from "tailwindcss";

const config: Config = {
  darkMode: "class",
  content: ["./app/**/*.{ts,tsx}", "./components/**/*.{ts,tsx}"],
  theme: {
    // Size-only font scale: line-heights come from the Figma text styles (1.35 / 1.4), set explicitly.
    fontSize: { xs: "12px", sm: "14px", base: "16px", lg: "18px", xl: "20px", "2xl": "24px", "3xl": "30px", "4xl": "36px", "5xl": "48px", "6xl": "60px", "7xl": "72px" },
    extend: {
      colors: {
        // Kuryer.net red (Figma color/blue = color/amber = #C72127); the "amber" accent is the same red.
        brand: { 50: "#FEF7F7", 100: "#FCEBEC", 200: "#F5C2C4", 400: "#E0565B", 500: "#C72127", 600: "#A91C21", 700: "#8C171B" },
        amber: { 50: "#FEF7F7", 100: "#FCEBEC", 400: "#E0565B", 500: "#C72127", 600: "#A91C21" },
        // Semantic tokens from Figma variables; light/dark values live in app/globals.css.
        ink: "rgb(var(--ink) / <alpha-value>)",
        night: "#242325", // Figma background/dark
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
        figma: "0px 12px 30px 0px rgba(89,26,31, 0.07)",
        soft: "0 8px 30px -12px rgba(36,35,37, 0.18)",
        card: "0 2px 10px -2px rgba(36,35,37, 0.08)",
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
