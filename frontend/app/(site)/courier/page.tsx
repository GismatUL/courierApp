"use client";
import { FaqSign, faqSummary } from "@/components/Faq";
import { useState } from "react";
import { HeroIllustration } from "@/components/Illustrations";
import { BoxField } from "@/components/PriceCalculator";
import { Band, H2 } from "@/components/sections";
import { Container, LinkButton, SelectChevron } from "@/components/ui";
import { num } from "@/lib/pricing";

/* Figma frame "06 · Kuryer ol" (2:20249). */

const RATES = { "Bakı · Piyada": 4, "Bakı · Avtomobil": 5.5, "Bakı · Yük maşını": 8, "Sumqayıt · Avtomobil": 5, "Gəncə · Avtomobil": 5 } as const;
type RateKey = keyof typeof RATES;
const REQUIREMENTS = [
  ["01", "18 yaş və şəxsiyyət vəsiqəsi", "Qeydiyyatda məlumatlarını təsdiqlə."],
  ["02", "Smartfon və internet", "Sifarişləri tətbiqdən qəbul et və idarə et."],
  ["03", "İstədiyin nəqliyyat", "Piyada başla və ya şəxsi avtomobilindən istifadə et."],
];
const FAQ = [
  ["Təcrübə tələb olunur?", "Xeyr. Tətbiq sizə hər addımda kömək edəcək."],
  ["Öz iş saatlarımı seçə bilərəm?", "Bəli. Uyğun sifarişləri istədiyiniz vaxt qəbul edin."],
  ["Hansı şəhərlərdə kuryer ola bilərəm?", "Bütün Azərbaycan üzrə qeydiyyat açıqdır. Aktiv sifarişlər şəhərə görə dəyişir."],
  ["Qazancımı necə alıram?", "Təsdiqlənmiş qazancı tətbiqdəki qaydalara uyğun bank hesabınıza çıxara bilərsiniz."],
];
const select = "w-full cursor-pointer appearance-none bg-transparent text-[15px] font-semibold text-ink outline-none";

export default function CourierPage() {
  const [hours, setHours] = useState(6);
  const [days, setDays] = useState(5);
  const [rate, setRate] = useState<RateKey>("Bakı · Avtomobil");
  const weekly = hours * days * RATES[rate];

  return (
    <>
      {/* Courier hero 2:20251 */}
      <section className="bg-brand-surface">
        <Container className="flex flex-col gap-6 py-[72px] leading-[1.4] lg:flex-row lg:items-start">
          <div className="flex flex-col items-start gap-4 lg:w-[680px] lg:min-w-0 lg:shrink">
            <p className="text-xs font-bold text-link">AZƏRBAYCANIN HƏR YERİNDƏ KURYER OL</p>
            <h1 className="text-[40px] font-bold text-ink md:text-[60px]">Şəhər hərəkətdə.<br />Qazanc səndə.</h1>
            <p className="text-[19px] text-muted">Piyada, avtomobillə və ya yük maşını ilə çatdır. Öz qrafikini qur, hər gün qazan.</p>
            <LinkButton href="/courier/signup" variant="accent" size="lg" className="w-full max-w-[320px]">Qazanmağa başla&nbsp;&nbsp;↗</LinkButton>
            <p className="whitespace-pre-wrap text-sm font-medium text-ink">{"10 dəqiqəyə onlayn qeydiyyat  ·  Sərbəst qrafik"}</p>
          </div>
          <div className="lg:h-[410px] lg:w-[608px] lg:min-w-0 lg:shrink">
            <HeroIllustration set="courier-hero" className="mx-auto w-full max-w-[560px] lg:ml-5" />
          </div>
        </Container>
      </section>

      {/* Earnings calculator 2:20252 */}
      <Band className="py-16 leading-[1.4]">
        <H2>Vaxtını seç. Qazancını gör.</H2>
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start">
          <div className="flex flex-col gap-5 rounded-3xl bg-surface p-8 lg:w-[760px] lg:min-w-0 lg:shrink">
            <div className="grid gap-4 sm:grid-cols-2">
              <BoxField label="Gündə neçə saat?" normalCase>
                <SelectChevron>
                  <select className={`${select} pr-6`} value={hours} onChange={(e) => setHours(+e.target.value)}>
                    {[2, 4, 6, 8, 10, 12].map((h) => <option key={h} value={h}>{`${h} saat`}</option>)}
                  </select>
                </SelectChevron>
              </BoxField>
              <BoxField label="Həftədə neçə gün?" normalCase>
                <SelectChevron>
                  <select className={`${select} pr-6`} value={days} onChange={(e) => setDays(+e.target.value)}>
                    {[1, 2, 3, 4, 5, 6, 7].map((d) => <option key={d} value={d}>{`${d} gün`}</option>)}
                  </select>
                </SelectChevron>
              </BoxField>
            </div>
            <BoxField label="Şəhər və nəqliyyat" normalCase>
              <SelectChevron>
                <select className={`${select} pr-6`} value={rate} onChange={(e) => setRate(e.target.value as RateKey)}>
                  {(Object.keys(RATES) as RateKey[]).map((k) => <option key={k} value={k}>{`${k}`}</option>)}
                </select>
              </SelectChevron>
            </BoxField>
            <p className="text-sm text-muted">{`Nümunə: ${hours} saat × ${days} gün × ${num(RATES[rate])} ₼ / saat`}</p>
          </div>
          <div className="flex flex-col gap-5 rounded-3xl bg-brand-500 p-8 text-white lg:w-[528px] lg:min-w-0 lg:shrink" aria-live="polite">
            <p className="text-xs font-bold">TƏXMİNİ HƏFTƏLİK QAZANC</p>
            <p className="text-[48px] font-bold md:text-[64px]">{Number.isInteger(weekly) ? weekly : num(weekly)} ₼</p>
            <p className="text-sm">Gəlir sifariş sayı, məsafə və işlədiyiniz vaxtdan asılıdır. Nümunə hesablama, zəmanətli qazanc deyil.</p>
          </div>
        </div>
      </Band>

      {/* Requirements 2:20253 */}
      <Band tone="subtle" className="py-16 leading-[1.4]">
        <H2>Başlamaq üçün nə lazımdır?</H2>
        <div className="grid gap-6 md:grid-cols-3 md:items-start">
          {REQUIREMENTS.map(([n, t, d]) => (
            <div key={n} className="flex flex-col gap-5 rounded-3xl bg-card p-6">
              <p className="text-[40px] font-bold text-link">{n}</p>
              <h3 className="text-[23px] font-bold text-ink">{t}</h3>
              <p className="text-base text-muted">{d}</p>
            </div>
          ))}
        </div>
      </Band>

      {/* Payouts 2:20254 */}
      <Band className="py-16 leading-[1.4]">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start">
          <div className="flex flex-col gap-4 lg:w-[780px] lg:min-w-0 lg:shrink">
            <H2>Gündəlik qazanc.<br />Aydın hesablaşma.</H2>
            <p className="text-lg text-muted">Tamamlanmış çatdırılmaları və balansını tətbiqdən izlə. Qazancını qeyd etdiyin bank hesabına çıxar.</p>
            {["Hər sifarişin qiyməti əvvəlcədən görünür", "Çıxarış şərtləri tətbiqdə şəffaf göstərilir", "Dəstək komandası 24/7 yanındadır"].map((x) => (
              <p key={x} className="whitespace-pre-wrap text-base font-medium text-ink">{`✓  ${x}`}</p>
            ))}
          </div>
          <div className="flex flex-col gap-5 rounded-3xl bg-amber-surface p-8 text-ink lg:w-[508px] lg:min-w-0 lg:shrink">
            <p className="text-xs font-bold">BUGÜNKÜ QAZANC</p>
            <p className="text-[50px] font-bold">38,50 ₼</p>
            <p className="text-sm">7 tamamlanmış çatdırılma · nümunə</p>
            <LinkButton href="/courier/signup" variant="accent" size="lg" className="w-full">İndi qeydiyyatdan keç</LinkButton>
          </div>
        </div>
      </Band>

      {/* Courier FAQ 2:20255 — all answers shown, as in the design */}
      <Band tone="subtle" className="py-16 leading-[1.4]">
        <h2 className="text-[30px] font-bold text-ink md:text-[36px]">İlk sifarişdən əvvəl</h2>
        {FAQ.map(([q, a]) => (
          <details key={q} open className="group rounded-3xl bg-card p-6">
            <summary className={`${faqSummary} text-lg`}>
              <span>{q}</span>
              <FaqSign />
            </summary>
            <p className="mt-5 text-[15px] text-muted">{a}</p>
          </details>
        ))}
        <LinkButton href="/courier/signup" variant="accent" size="lg" className="w-full max-w-[320px]">Qazanmağa başla&nbsp;&nbsp;↗</LinkButton>
      </Band>
    </>
  );
}
