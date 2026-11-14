"use client";
import Link from "next/link";
import { PriceCalculator } from "./PriceCalculator";
import { HeroIllustration } from "./Illustrations";
import { useI18n } from "@/lib/i18n";

/** Figma "Hero" — desktop 2:20215 (updated: min-h 820, px 64, py 56; 660-wide story with the illustration
 *  under the copy at 78 %, 560-wide calculator card top-right) / mobile 2:20278 (px 20, py 32, gap 24). */
export function HeroBlock() {
  const { t, city } = useI18n();
  return (
    <section className="bg-brand-surface">
      <div className="mx-auto flex max-w-page flex-col gap-6 px-5 py-8 leading-[1.4] lg:min-h-[820px] lg:flex-row lg:items-start lg:justify-between lg:gap-7 lg:px-16 lg:py-14">
        {/* Hero story */}
        <div className="flex flex-col gap-6 lg:w-[660px] lg:min-w-0 lg:shrink lg:gap-7">
          <div className="flex flex-col gap-6">
            <span className="whitespace-pre text-[11px] font-bold text-link lg:w-full lg:max-w-[360px] lg:rounded-4xl lg:bg-card lg:p-2.5 lg:text-xs lg:font-semibold lg:leading-[1.35]">
              {"●  BÜTÜN AZƏRBAYCAN ÜZRƏ"}
            </span>
            <h1 className="whitespace-pre-line text-[39px] font-bold text-ink lg:text-[48px] xl:text-[64px]">{t.heroTitle(city)}</h1>
            <p className="text-base text-muted lg:hidden">Bir sənəddən böyük bağlamaya. İndi göndərin, bu gün ünvanına çatsın.</p>
            <p className="hidden whitespace-pre-line text-[19px] leading-[1.35] text-muted lg:block">{t.heroSub}</p>
            <p className="hidden whitespace-pre text-[13px] font-medium leading-[1.35] text-ink lg:block">
              {"✓  Canlı izləmə     ✓  Sığortalı çatdırılma     ✓  24/7 dəstək"}
            </p>
          </div>
          <HeroIllustration set="hero-m" className="w-full max-w-[350px] lg:hidden" />
          {/* 624 × 449.6 artwork scaled to 78 % inside a 350px-tall slot, as in the frame */}
          <div className="hidden lg:block lg:h-[350px]">
            <HeroIllustration className="w-[486.72px] max-w-full" />
          </div>
        </div>
        {/* Quick price calculator — 560 wide, aligned to the right edge of the content */}
        <div className="lg:w-[420px] lg:shrink-0 xl:w-[560px]">
          <PriceCalculator />
        </div>
        <Link href="/business" className="whitespace-pre text-[15px] font-semibold text-link lg:hidden">{"Biznes üçün xüsusi şərtlər  ↗"}</Link>
      </div>
    </section>
  );
}
