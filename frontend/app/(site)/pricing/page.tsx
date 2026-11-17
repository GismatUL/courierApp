"use client";
import { FaqSign, faqSummary } from "@/components/Faq";
import clsx from "clsx";
import { Band } from "@/components/sections";
import { LinkButton, Chevron } from "@/components/ui";
import { useI18n } from "@/lib/i18n";
import { CITIES } from "@/lib/data";
import { azn, VEHICLES, type Vehicle } from "@/lib/pricing";

/* Figma frame "08 · Tariflər" (2:20261). */

const FAQ = [
  ["Qiymət nə vaxt dəyişə bilər?", "Ünvan, çəki, nəqliyyat və ya əlavə xidmətlər dəyişəndə qiymət yenidən hesablanır. Təsdiqlənmiş sifarişin qiyməti dəyişmir."],
  ["Sığorta qiymətə daxildirmi?", "Bəli. 100 ₼-dək bəyan edilmiş dəyər qiymətə daxildir."],
  ["Daimi biznes müştərilərinə endirim varmı?", "Bəli. Ayda 30 çatdırılmadan başlayaraq biznesinizə özəl tariflər təklif edirik."],
];

export default function PricingPage() {
  const { city, setCity } = useI18n();
  return (
    <>
      {/* Pricing hero 2:20263 */}
      <Band tone="brand" className="py-16 leading-[1.4]">
        <h1 className="text-[40px] font-bold text-ink md:text-[56px]">Aydın tarif.<br />Sürprizsiz çatdırılma.</h1>
        <p className="text-[19px] text-muted">Məsafənizə, bağlamanıza və seçdiyiniz nəqliyyata uyğun qiymət.</p>
        <p className="whitespace-pre-wrap text-sm font-medium text-link">
          <label className="relative">
            <span aria-hidden className="whitespace-nowrap">{`${city}  `}<Chevron className="align-middle" /></span>
            <select aria-label="Şəhər" value={city} onChange={(e) => setCity(e.target.value)} className="absolute inset-0 cursor-pointer opacity-0">
              {CITIES.map((c) => <option key={c}>{c}</option>)}
            </select>
          </label>
          {"   ·   Nümunə AZN tarifləri — yayımdan əvvəl təsdiqlənməlidir"}
        </p>
      </Band>

      {/* Tariff cards 2:20264 */}
      <Band className="py-16 leading-[1.4]">
        <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4 md:items-start">
          {(Object.keys(VEHICLES) as Vehicle[]).map((k) => {
            const v = VEHICLES[k];
            const featured = k === "car";
            return (
              <div key={k} className={clsx("flex flex-col gap-5 rounded-3xl p-7", featured ? "bg-brand-surface" : "bg-surface")}>
                <h2 className="text-[27px] font-bold text-ink">{v.label}</h2>
                <p className="text-sm font-medium text-muted">{v.capacity}</p>
                <p className="text-[46px] font-bold text-link">{azn(v.base)}</p>
                <p className="text-[13px] text-muted">başlanğıc qiymət</p>
                {[["Hər km", v.perKm], ["Əlavə dayanacaq", v.perStop], ["Gözləmə dəqiqəsi", v.perWaitMin], ["Əlavə kq", v.perKg]].map(([l, n]) => (
                  <p key={l as string} className="whitespace-pre text-base font-medium text-ink">{`${l}      ${azn(n as number)}`}</p>
                ))}
                <LinkButton href="/order" variant={featured ? "accent" : "soft"} size="lg" className="w-full">Qiyməti hesabla&nbsp;&nbsp;↗</LinkButton>
              </div>
            );
          })}
        </div>
      </Band>

      {/* Pricing explainer 2:20265 */}
      <Band tone="subtle" className="py-16 leading-[1.4]">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start">
          <div className="flex flex-col gap-4 lg:w-[760px] lg:min-w-0 lg:shrink">
            <h2 className="text-[30px] font-bold text-ink md:text-[36px]">Yekun qiymət necə yaranır?</h2>
            <p className="text-[17px] text-muted">Başlanğıc tarif + məsafə + əlavə xidmətlər. İlk 5 kq daxildir; əlavə çəki haqqı seçilmiş nəqliyyatın tutumu daxilində hesablanır.</p>
            <p className="text-[15px] text-muted">İlk 10 dəqiqə gözləmə pulsuzdur. Planlı 4 saatlıq interval daha sərfəli ola bilər. Şəhərlərarası sifarişlər ayrıca hesablanır.</p>
          </div>
          <div className="flex flex-col gap-5 rounded-3xl bg-card p-8 lg:w-[528px] lg:min-w-0 lg:shrink">
            <h3 className="text-xl font-bold text-ink">Bakı · Avtomobil · 5,5 km · 5 kq</h3>
            <p className="whitespace-pre text-[15px] font-medium text-ink md:text-[17px]">{"Başlanğıc                  3,00 ₼\n5,5 km × 0,80 ₼        4,40 ₼\nQapıdan qapıya          1,00 ₼"}</p>
            <p className="whitespace-pre text-[22px] font-bold text-link md:text-[26px]">{"Cəmi                         8,40 ₼"}</p>
          </div>
        </div>
      </Band>

      {/* Pricing FAQ 2:20266 */}
      <Band className="py-16 leading-[1.4]">
        <h2 className="text-[30px] font-bold text-ink md:text-[36px]">Tariflər haqqında</h2>
        {FAQ.map(([q, a]) => (
          <details key={q} className="group rounded-3xl bg-surface p-6">
            <summary className={`${faqSummary} text-lg`}>
              <span>{q}</span>
              <FaqSign />
            </summary>
            <p className="mt-5 text-[15px] text-muted">{a}</p>
          </details>
        ))}
        <p className="text-[15px] text-muted">Yekun qiymət məsafə, çəki, nəqliyyat və əlavə xidmətlərdən asılıdır. Sifarişi təsdiqləməzdən əvvəl bütün xərcləri görəcəksiniz.</p>
      </Band>
    </>
  );
}
