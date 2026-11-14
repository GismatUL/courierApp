import type { ReactNode } from "react";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { SiteChrome } from "@/components/AppScreenChrome";

/** Public site pages: Figma frames with "Header / Desktop" and "Footer / Desktop". */
export default function SiteLayout({ children }: { children: ReactNode }) {
  return (
    <>
      <SiteChrome><Header /></SiteChrome>
      <main>{children}</main>
      <SiteChrome><Footer /></SiteChrome>
    </>
  );
}
