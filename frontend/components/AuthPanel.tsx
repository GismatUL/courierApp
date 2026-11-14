import type { ReactNode } from "react";
import { HeroIllustration } from "./Illustrations";
import { Container } from "./ui";

/** Figma "Account value panel" + workspace shared by login (09), SMS (10) and courier signup (07). */
export function AuthLayout({
  eyebrow = "AZƏRBAYCAN BOYU, QAPIDAN QAPIYA.",
  title = <>Bir hesab.<br />Bütün çatdırılmalar.</>,
  sub = "Sifariş ver, bağlamanı canlı izlə və sevdiyin ünvanları yadda saxla.",
  art = "auth",
  children,
}: { eyebrow?: string; title?: ReactNode; sub?: string; art?: "auth" | "signup"; children: ReactNode }) {
  return (
    <div className="bg-surface">
      <Container className="flex flex-col-reverse gap-10 py-16 leading-[1.4] lg:flex-row lg:items-start lg:gap-[72px]">
        <div className="flex flex-col gap-6 rounded-3xl bg-brand-surface p-10 lg:min-h-[710px] lg:w-[664px] lg:min-w-0 lg:shrink">
          <p className="text-xs font-bold text-link">{eyebrow}</p>
          <h1 className="text-[36px] font-bold text-ink md:text-[46px]">{title}</h1>
          <p className="text-lg text-muted">{sub}</p>
          <div className="lg:h-[350px]">
            <HeroIllustration set={art} className="w-full max-w-[480px] lg:ml-5" />
          </div>
        </div>
        <div className="flex flex-col gap-6 rounded-3xl bg-card p-10 lg:w-[576px] lg:min-w-0 lg:shrink">{children}</div>
      </Container>
    </div>
  );
}

export const authInput =
  "w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:text-ink";
