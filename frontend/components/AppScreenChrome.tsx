"use client";
import { usePathname } from "next/navigation";
import type { ReactNode } from "react";

/** Routes whose mobile frames (12 · Sifariş, 13 · İzləmə) are app-style screens without the site header/footer. */
export const APP_SCREENS = ["/order", "/tracking"];

/** Hides site chrome below lg on app-style screens; desktop frames keep it. */
export function SiteChrome({ children }: { children: ReactNode }) {
  const pathname = usePathname();
  return <div className={APP_SCREENS.includes(pathname) ? "hidden lg:contents" : "contents"}>{children}</div>;
}
