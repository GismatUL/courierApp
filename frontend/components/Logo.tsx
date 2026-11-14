import Link from "next/link";
import clsx from "clsx";

/** Wordmark from Figma "Header / Desktop › Brand": Inter Bold 27px, link blue. */
export function Logo({ light, className }: { light?: boolean; className?: string }) {
  return (
    <Link
      href="/"
      aria-label="Yetir — ana səhifə"
      className={clsx("whitespace-nowrap text-[27px] font-bold leading-[1.35]", light ? "text-white" : "text-link", className)}
    >
      Yetir↗
    </Link>
  );
}
