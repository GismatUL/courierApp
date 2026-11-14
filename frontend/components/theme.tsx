"use client";
import { useEffect, useState } from "react";
import clsx from "clsx";

/** Inline script that sets the theme class before paint (avoids a flash). Follows the OS setting
 *  until the user picks a theme with ThemeToggle; the choice is stored as `theme` ("light" | "dark"). */
export const themeScript = `try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark')}catch(e){}`;

/** Light/dark switch (not in the Figma frames; added on request). Text glyphs match the header's style. */
export function ThemeToggle({ className }: { className?: string }) {
  const [dark, setDark] = useState<boolean | null>(null);
  useEffect(() => setDark(document.documentElement.classList.contains("dark")), []);
  const toggle = () => {
    const next = !document.documentElement.classList.contains("dark");
    document.documentElement.classList.toggle("dark", next);
    setDark(next);
    try { localStorage.setItem("theme", next ? "dark" : "light"); } catch {}
  };
  return (
    <button
      type="button"
      onClick={toggle}
      aria-label={dark ? "İşıqlı rejimə keç" : "Qaranlıq rejimə keç"}
      title={dark ? "İşıqlı rejim" : "Qaranlıq rejim"}
      className={clsx("grid h-8 w-8 shrink-0 place-items-center rounded-xl text-lg leading-none text-ink hover:bg-surface", className)}
    >
      {/* Render nothing until mounted so server and client markup match. */}
      <span aria-hidden>{dark === null ? "" : dark ? "☀" : "☾"}</span>
    </button>
  );
}
