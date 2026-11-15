import type { Metadata, Viewport } from "next";
import type { ReactNode } from "react";
import "./globals.css";
import { Inter } from "next/font/google";
import { I18nProvider } from "@/lib/i18n";
import { themeScript } from "@/components/theme";

export const metadata: Metadata = {
  title: "Kuryer.net — Azərbaycanda kuryer çatdırılması",
  description: "Tələb əsasında kuryer çatdırılması: piyada, avtomobil və yük maşını ilə. 30 dəqiqədən.",
};
const inter = Inter({ subsets: ["latin", "latin-ext", "cyrillic"], variable: "--font-inter", display: "swap" });

export const viewport: Viewport = { themeColor: "#C72127" };

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="az" className={inter.variable} suppressHydrationWarning>
      <head>
        <script dangerouslySetInnerHTML={{ __html: themeScript }} />
      </head>
      <body>
        <I18nProvider>{children}</I18nProvider>
      </body>
    </html>
  );
}
