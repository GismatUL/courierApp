"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState } from "react";
import clsx from "clsx";
import { Logo } from "./Logo";
import { btn } from "./ui";
import { ThemeToggle } from "./theme";
import { useI18n, type Lang } from "@/lib/i18n";
import { CityPicker } from "./CityPicker";

const LANGS: Lang[] = ["az", "ru", "en"];

/** Which header item the current route belongs to (sub-pages count: /login/verify → /login, /courier/signup → /courier). */
const SECTIONS = ["/business", "/courier", "/pricing", "/login", "/order"];
function activeSection(pathname: string): string | null {
  return SECTIONS.find((s) => pathname === s || pathname.startsWith(s + "/")) ?? null;
}
const activeLink = "text-link underline decoration-2 underline-offset-[6px]";

export function Header() {
  const { t, lang, setLang } = useI18n();
  const [open, setOpen] = useState(false);
  const active = activeSection(usePathname());
  const current = (href: string) => (active === href ? { "aria-current": "page" as const } : {});
  // The yellow CTA stays primary on the home and order pages; when another header item is the
  // current page it steps back to the secondary style so it doesn't read as the active item.
  const ctaVariant = active && active !== "/order" ? "soft" : "accent";
  const nav = [
    { href: "/business", label: t.business },
    { href: "/courier", label: t.courier },
    { href: "/pricing", label: t.pricing },
    { href: "/#faq", label: t.help },
  ];

  const Langs = ({ className }: { className: string }) => (
    <div className={className}>
      {LANGS.map((l, i) => (
        <span key={l}>
          {i > 0 && " / "}
          <button type="button" onClick={() => setLang(l)} aria-pressed={lang === l} className={clsx("uppercase hover:text-link", lang === l && "underline underline-offset-4")}>
            {l}
          </button>
        </span>
      ))}
    </div>
  );
  return (
    <header className="sticky top-0 z-40 bg-card lg:border-b lg:border-[#E8EDF3] lg:shadow-[0_3px_16px_rgba(15,23,42,0.04)] dark:lg:border-line">
      {/* Desktop — Figma "Header / Desktop" 2:20450 (updated): 1440 × 94, px 64, py 18, gap 22, bottom border + soft shadow */}
      <div className="mx-auto hidden max-w-page items-center gap-4 px-8 py-[18px] lg:flex xl:gap-[22px] xl:px-16">
        <div className="shrink-0 xl:w-[158px]"><Logo /></div>
        <div className="w-[108px] shrink-0"><CityPicker variant="desktop" /></div>
        <nav className="flex min-w-0 flex-1 items-center gap-4 xl:gap-5">
          {nav.map((n) => (
            <Link key={n.href} href={n.href} {...current(n.href)} className={clsx("whitespace-nowrap text-sm font-medium leading-[1.35] hover:text-link", active === n.href ? activeLink : "text-ink")}>{n.label}</Link>
          ))}
        </nav>
        <div className="flex shrink-0 items-center gap-4 xl:gap-[22px]">
          <Langs className="whitespace-nowrap xl:w-[108px] text-xs font-semibold leading-[1.35] text-ink" />
          <ThemeToggle />
          <Link href="/login" {...current("/login")} className={clsx("whitespace-nowrap text-sm font-semibold leading-[1.4] hover:text-link xl:w-[68px]", active === "/login" ? activeLink : "text-ink")}>{t.login}</Link>
          <Link href="/order" {...current("/order")} className={btn(ctaVariant, "lg", "whitespace-nowrap xl:w-[244px]")}>{t.order}&nbsp;&nbsp;↗</Link>
        </div>
      </div>

      {/* Mobile — Figma "Header / Mobile" 2:22452: px 20, py 18, two rows 24px apart */}
      <div className="flex flex-col gap-6 px-5 py-[18px] leading-[1.4] lg:hidden">
        <div className="flex items-center gap-4">
          <Logo className="w-[164px] shrink-0 !text-[23px] !leading-[1.4]" />
          <CityPicker variant="mobile" className="w-[94px] shrink-0" />
          <button type="button" className="w-[60px] text-left text-2xl font-medium text-ink" onClick={() => setOpen(!open)} aria-label="Menyu" aria-expanded={open}>
            {open ? "✕" : "☰"}
          </button>
        </div>
        <div className="flex gap-3">
          <Langs className="w-[180px] text-xs font-semibold text-ink" />
          <Link href="/login" {...current("/login")} className={clsx("whitespace-pre text-[13px] font-semibold text-link", active === "/login" && "underline decoration-2 underline-offset-[6px]")}>{`${t.login}  ↗`}</Link>
          <ThemeToggle className="-my-2 ml-auto" />
        </div>
      </div>

      {open && (
        <div className="border-t border-line bg-card px-5 py-4 lg:hidden">
          <nav className="flex flex-col gap-1">
            {nav.map((n) => (
              <Link key={n.href} href={n.href} {...current(n.href)} onClick={() => setOpen(false)} className={clsx("rounded-xl px-3 py-3 font-medium hover:bg-surface", active === n.href && "bg-brand-surface text-link")}>{n.label}</Link>
            ))}
          </nav>
          <Link href="/order" {...current("/order")} onClick={() => setOpen(false)} className={btn(ctaVariant, "lg", "mt-3 w-full")}>{t.order}&nbsp;&nbsp;↗</Link>
        </div>
      )}
    </header>
  );
}
