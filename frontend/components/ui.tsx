import clsx from "clsx";
import Link from "next/link";
import type { ButtonHTMLAttributes, ReactNode } from "react";

type Variant = "primary" | "accent" | "secondary" | "soft" | "ghost";
type Size = "sm" | "md" | "lg";

/** Figma "Button / …" (15 · Dizayn sistemi › 03): Primary, Secondary, Ghost, Disabled; Large p18 / Small p12. */
const variants: Record<Variant, string> = {
  primary: "bg-brand-500 text-white hover:bg-brand-600 active:bg-brand-700",
  accent: "bg-amber-500 text-night hover:bg-[#F2A900] active:bg-[#DE9900]",
  secondary: "bg-card text-ink ring-1 ring-line hover:ring-slate-300",
  soft: "bg-brand-surface text-ink hover:bg-brand-100 active:bg-brand-200",
  ghost: "bg-card text-ink hover:bg-surface active:bg-line",
};
const sizes: Record<Size, string> = {
  sm: "min-h-[45px] p-3 text-[15px] leading-[1.4]",
  md: "min-h-[45px] p-3 text-[15px] leading-[1.4]",
  lg: "min-h-[57px] p-[18px] text-[15px] leading-[1.4]",
};

export function btn(variant: Variant = "primary", size: Size = "md", extra?: string) {
  return clsx(
    "inline-flex items-center justify-center gap-3 rounded-xl text-center font-semibold transition focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-brand-200 disabled:cursor-not-allowed disabled:bg-line disabled:text-muted",
    variants[variant], sizes[size], extra,
  );
}

export function Button({ variant, size, className, ...p }: ButtonHTMLAttributes<HTMLButtonElement> & { variant?: Variant; size?: Size }) {
  return <button className={btn(variant, size, className)} {...p} />;
}

export function LinkButton({ href, variant, size, className, children }: { href: string; variant?: Variant; size?: Size; className?: string; children: ReactNode }) {
  return <Link href={href} className={btn(variant, size, className)}>{children}</Link>;
}

/** Page-width wrapper matching the 1440 Figma frames (64px side padding on desktop). */
export function Container({ className, children }: { className?: string; children: ReactNode }) {
  return <div className={clsx("mx-auto w-full max-w-page px-5 lg:px-16", className)}>{children}</div>;
}

/** Downloaded Figma icon (public/figma/icons), rendered at its design size. */
export function FigmaIcon({ name, size = 32, className }: { name: string; size?: number; className?: string }) {
  // eslint-disable-next-line @next/next/no-img-element
  return <img src={`/figma/icons/${name}.svg`} alt="" width={size} height={size} style={{ width: size, height: size }} className={clsx("shrink-0 self-start", className)} />;
}

/** Figma "Badge / …" order statuses: p 10, radius 12, 13/600. */
const STATUS_TONE = {
  onway: "bg-brand-surface text-link",
  delivered: "bg-[#E5F6EE] text-[#087F5B]",
  searching: "bg-amber-surface text-[#805800]",
  cancelled: "bg-[#FCECEB] text-[#BD362E]",
} as const;
export type StatusTone = keyof typeof STATUS_TONE;
export function StatusBadge({ tone, children, className }: { tone: StatusTone; children: ReactNode; className?: string }) {
  return <span className={clsx("inline-block rounded-xl p-2.5 text-[13px] font-semibold leading-[1.4]", STATUS_TONE[tone], className)}>{children}</span>;
}

/** Down/up chevron as SVG. Replaces the design's "⌄"/"⌃" glyphs, which need a symbol font and render
 *  small and low in browser fonts. Inherits the text colour. */
export function Chevron({ up, size = 12, className }: { up?: boolean; size?: number; className?: string }) {
  return (
    <svg viewBox="0 0 12 12" width={size} height={size} fill="none" aria-hidden className={clsx("inline-block shrink-0 transition-transform", up && "rotate-180", className)}>
      <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" strokeWidth={1.6} strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** Wraps a native <select> (give it `pr-6`) and draws the chevron over its right edge. */
export function SelectChevron({ children, className, inset = "right-0" }: { children: ReactNode; className?: string; inset?: string }) {
  return (
    <span className={clsx("relative block", className)}>
      {children}
      <Chevron className={clsx("pointer-events-none absolute top-1/2 -translate-y-1/2", inset)} />
    </span>
  );
}
