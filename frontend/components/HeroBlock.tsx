"use client";
import Link from "next/link";
import Image from "next/image";
import { PriceCalculator } from "./PriceCalculator";
import { useI18n } from "@/lib/i18n";

/** Figma "Kuryer.net / Courier in Baku" photo (2:20458 desktop 696 × 260, 2:22456 mobile 350 × 218). */
export function CourierPhoto({ className = "" }: { className?: string }) {
  return (
    <Image
      src="/figma/brand/courier.png"
      alt="Kuryer.net kuryeri Bakıda, firmanın furqonunun yanında bağlama ilə"
      width={558}
      height={347}
      className={`rounded-3xl object-cover ${className}`}
    />
  );
}

/** Figma "Hero" — desktop 2:20215 (1440 × 696: px 64, py 48; 696-wide story with the photo under the copy,
 *  40px gap, 576-wide calculator card) / mobile 2:20278 (px 20, py 32, gap 24; calculator before the photo). */
export function HeroBlock() {
  const { t } = useI18n();
  return (
    <section className="bg-brand-surface">
      <div className="mx-auto flex max-w-page flex-col gap-6 px-5 py-8 leading-[1.4] lg:flex-row lg:items-start lg:gap-10 lg:px-16 lg:py-12">
        {/* Hero story */}
        <div className="flex flex-col gap-6 lg:w-[696px] lg:min-w-0 lg:shrink lg:gap-5">
          <span className="whitespace-pre text-[11px] font-bold text-link lg:hidden">{"●  BÜTÜN AZƏRBAYCAN ÜZRƏ"}</span>
          <span className="hidden w-full max-w-[360px] whitespace-pre rounded-4xl bg-card p-2.5 text-xs font-semibold leading-[1.35] text-link lg:block">
            {"KURYER.NET  /  BÜTÜN AZƏRBAYCAN ÜZRƏ"}
          </span>
          <h1 className="whitespace-pre-line text-5xl font-bold leading-[1.05] tracking-[-1.5px] text-ink lg:text-[56px] lg:tracking-[-2px] xl:text-7xl xl:tracking-[-2.5px]">
            {t.heroTitle}
          </h1>
          <p className="text-base text-muted lg:hidden">Bir sənəddən böyük bağlamaya. İndi göndərin, bu gün ünvanına çatsın.</p>
          <p className="hidden whitespace-pre-line text-[19px] leading-[1.35] text-muted lg:block">{t.heroSub}</p>
          <p className="hidden whitespace-pre text-[13px] font-medium leading-[1.35] text-ink lg:block">
            {"Sənədlər  ·  Bağlamalar  ·  Biznes sifarişləri"}
          </p>
          <CourierPhoto className="hidden h-[260px] w-full lg:block" />
        </div>
        <div className="lg:w-[460px] lg:shrink-0 xl:w-[576px]">
          <PriceCalculator />
        </div>
        <Link href="/business" className="whitespace-pre text-[15px] font-semibold text-link lg:hidden">{"Biznes üçün xüsusi şərtlər  ↗"}</Link>
        <CourierPhoto className="h-[218px] w-full max-w-[350px] lg:hidden" />
      </div>
    </section>
  );
}
