import Image from "next/image";
import clsx from "clsx";
import type { ReactNode } from "react";
import { BakuMap } from "@/components/BakuMap";
import { StoreBadges } from "@/components/Illustrations";
import { Container, FigmaIcon, StatusBadge, btn } from "@/components/ui";

/* Figma frame "15 · Dizayn sistemi" (2:20303): live reference of the tokens and components used in code. */

const COLORS = [
  ["ink", "#242325"], ["muted", "#686468"], ["blue", "#C72127"], ["amber", "#C72127"],
  ["bg", "#F7F6F5"], ["line", "#E6E1E1"], ["pale", "#FEF7F7"], ["green", "#087F5B"],
];
const SAMPLE = "Əə Ğğ Iı Öö Üü Çç Şş";
const LABEL = "Sifariş et  ↗";

function Block({ title, children, className, py = "py-8" }: { title: string; children: ReactNode; className?: string; py?: string }) {
  return (
    <section className="bg-card">
      <Container className={clsx("flex flex-col gap-4 leading-[1.4]", py, className)}>
        <h2 className="text-[26px] font-bold text-ink">{title}</h2>
        {children}
      </Container>
    </section>
  );
}
const field = (label: string, value: string, border: string) => (
  <div className={clsx("flex w-full flex-col gap-3 rounded-xl border bg-card p-4 font-semibold md:w-[400px]", border)}>
    <span className="text-xs text-muted">{label}</span>
    <span className="text-[15px] text-ink">{value}</span>
  </div>
);

export default function DesignSystem() {
  return (
    <main>
      {/* System header 2:20304 */}
      <section className="bg-card">
        <Container className="flex flex-col gap-4 py-14">
          <h1 className="whitespace-pre-wrap text-[32px] font-bold leading-[1.4] text-ink md:text-[44px]">{"Kuryer.net  /  Dizayn sistemi"}</h1>
          <p className="whitespace-pre-wrap text-[13px] font-semibold leading-[1.4] text-link">{"AZƏRBAYCAN ÜÇÜN ÇATDIRILMA  ·  V1.0  ·  DESKTOP 1440 / MOBİL 390"}</p>
          <p className="text-base leading-[1.4] text-ink">Nümunə tariflər, göstəricilər və tərəfdaş adları təqdimat üçündür. Hüquqi məlumatlar yayımdan əvvəl tamamlanmalıdır.</p>
          <p className="text-[15px] leading-[1.21] text-muted">Primitives → Semantic · Light / Dark. Rəng, boşluq və kənar radiusları üçün vahid inkişaf dili.</p>
        </Container>
      </section>

      <Block title="01 / Rənglər">
        <div className="flex flex-wrap gap-4">
          {COLORS.map(([n, hex]) => (
            <div key={n} className="flex w-[130px] flex-col gap-4">
              <div className="h-20 rounded-2xl" style={{ background: hex }} />
              <p className="text-[15px] font-semibold text-ink">{n}</p>
              <p className="text-[13px] text-ink">{hex}</p>
            </div>
          ))}
        </div>
      </Block>

      <Block title="02 / Tipoqrafiya · Inter" className="text-ink">
        <p className="text-[40px] font-bold md:text-[64px]">Display / 64 — {SAMPLE}</p>
        <p className="text-[32px] font-bold md:text-[40px]">Heading / 40 — {SAMPLE}</p>
        <p className="text-2xl font-bold">Title / 24 — {SAMPLE}</p>
        <p className="text-base">Body / 16 — {SAMPLE}</p>
        <p className="text-[13px]">Caption / 13 — {SAMPLE}</p>
      </Block>

      <Block title="03 / Düymələr · Ölçü və vəziyyətlər">
        {(["lg", "sm"] as const).map((size) => (
          <div key={size} className="flex flex-wrap gap-4">
            {(["accent", "soft", "ghost"] as const).map((v) => (
              <button key={v} type="button" className={btn(v, size, clsx("whitespace-pre", size === "lg" ? "w-[260px]" : "w-[200px]"))}>{LABEL}</button>
            ))}
            <button type="button" disabled className={btn("accent", size, clsx("whitespace-pre", size === "lg" ? "w-[260px]" : "w-[200px]"))}>{LABEL}</button>
          </div>
        ))}
        <div className="flex flex-wrap gap-4">
          <span className={btn("accent", "lg", "pointer-events-none w-[260px] !bg-[#B01D22]")}>Hover · Sifariş et</span>
          <span className={btn("accent", "lg", "pointer-events-none w-[260px] !bg-[#9E1A1F]")}>Pressed · Sifariş et</span>
        </div>
      </Block>

      <Block title="04 / Daxiletmə sahələri">
        <div className="flex flex-wrap gap-4 leading-[1.4]">
          {field("Telefon nömrəsi", "+994 50 123 45 67", "border-line")}
          {field("Telefon nömrəsi", "+994 50 123 45 67", "border-brand-500")}
          {field("Telefon nömrəsi", "+994 — nömrəni tamamlayın", "border-[#BD362E]")}
        </div>
        <div className="flex w-full max-w-[620px] flex-col gap-3 rounded-xl bg-card p-5 text-ink">
          <p className="text-[15px] font-semibold">Götürülmə ünvanı</p>
          <p className="text-[15px] font-semibold">Nizami küçəsi 203, Bakı</p>
          <p className="whitespace-pre-wrap text-sm">{"⌖  Nizami küçəsi 203 · Nəsimi, Bakı\n⌖  Nizami küçəsi 203A · Nəsimi, Bakı"}</p>
          <p className="whitespace-pre-wrap text-[13px] font-semibold text-link">{"Xəritədə göstər  ↗"}</p>
        </div>
      </Block>

      <Block title="05 / Nəqliyyat seçimi">
        <div className="flex flex-wrap gap-4">
          {([["foot", "Piyada", "15 kq-dək"], ["car", "Avtomobil", "200 kq-dək"], ["truck", "Yük maşını", "1 500 kq-dək"]] as const).map(([k, t, d]) => (
            <div key={k} className={clsx("flex w-[260px] flex-col gap-3 rounded-xl border p-5", k === "car" ? "border-brand-500 bg-brand-surface" : "border-line bg-card")}>
              <FigmaIcon name={k} />
              <span className="text-[15px] font-semibold text-ink">{t}</span>
              <span className="text-[13px] text-muted">{d}</span>
            </div>
          ))}
        </div>
      </Block>

      <Block title="06 / Sifariş statusları">
        <div className="flex flex-wrap gap-4">
          <StatusBadge tone="onway" className="w-[230px]">Yoldadır</StatusBadge>
          <StatusBadge tone="delivered" className="w-[230px]">Çatdırılıb</StatusBadge>
          <StatusBadge tone="searching" className="w-[230px]">Kuryer axtarılır</StatusBadge>
          <StatusBadge tone="cancelled" className="w-[230px]">Ləğv edilib</StatusBadge>
        </div>
      </Block>

      <Block title="07 / Sifariş kartı və bildirişlər">
        <div className="flex w-full max-w-[620px] flex-col gap-3 rounded-xl bg-card p-6 text-ink">
          <p className="text-[15px] font-semibold">#AZ-2048 · Yoldadır</p>
          <p className="text-[15px] font-semibold">Nizami küç. 203 → Ağ Şəhər</p>
          <p className="whitespace-pre-wrap text-sm text-muted">{"Bu gün, 14:20  ·  Avtomobil  ·  8,40 ₼"}</p>
        </div>
        <p role="status" className="w-full max-w-[620px] whitespace-pre-wrap rounded-xl bg-[#E5F6EE] p-5 text-[15px] font-semibold text-ink">{"✓  Sifarişiniz yaradıldı"}</p>
      </Block>

      <Block title="08 / Xəritə nişanları">
        <div className="flex gap-4 text-center text-[15px] font-semibold">
          <span className="w-[60px] rounded-4xl bg-brand-500 p-[14px] text-white">A</span>
          <span className="w-[60px] rounded-4xl bg-amber-500 p-[14px] text-white">B</span>
          <span className="w-[60px] rounded-4xl bg-night p-[14px] text-white">➤</span>
        </div>
      </Block>

      <Block title="09 / Dialoq" py="py-12">
        <div role="dialog" aria-label="Sifarişi ləğv edirsiniz?" className="flex w-full max-w-[560px] flex-col gap-3 rounded-xl bg-card p-8 text-ink">
          <p className="text-[15px] font-semibold">Sifarişi ləğv edirsiniz?</p>
          <p className="text-base">Kuryer artıq yoldadırsa, gözləmə haqqı tətbiq oluna bilər.</p>
          <button type="button" className={btn("accent", "lg", "w-full")}>Sifarişi saxla</button>
          <button type="button" className={btn("soft", "lg", "w-full")}>Sifarişi ləğv et</button>
        </div>
      </Block>

      <Block title="10 / Xəritə və fotoqrafiya" py="py-16">
        <div className="flex flex-col gap-8 lg:flex-row lg:items-start">
          <div className="flex flex-col gap-4">
            <p className="text-base font-semibold leading-[1.21] text-ink">Canlı marşrut · sxematik xəritə</p>
            <BakuMap width={600} className="max-w-full" />
          </div>
          <div className="flex flex-col gap-4">
            <p className="text-base font-semibold leading-[1.21] text-ink">Təhvil fotosu</p>
            <Image src="/figma/photos/proof.png" alt="Təhvil fotosu" width={360} height={240} className="h-[240px] w-[360px] rounded-[20px] object-cover" />
          </div>
          <div className="flex flex-col gap-4">
            <p className="text-base font-semibold leading-[1.21] text-ink">Kuryer portreti</p>
            <Image src="/figma/photos/elvin.png" alt="Kuryer portreti" width={150} height={150} className="h-[150px] w-[150px] rounded-[20px] object-cover" />
          </div>
        </div>
      </Block>

      <Block title="11 / Tətbiq nişanları və şəhər seçimi" py="py-16" className="!gap-6">
        <StoreBadges />
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img src="/figma/phone/qr.svg" alt="Nümunə QR" width={132} height={132} />
        <p className="text-[13px] text-muted">Nümunə QR → example.com/brand-app. Tətbiq yayımlananda keçid dəyişdirilməlidir.</p>
        <div className="flex w-full max-w-[620px] flex-col gap-4 whitespace-pre-wrap rounded-[20px] bg-surface p-6">
          <p className="text-[22px] font-bold text-ink">{"Şəhərinizi seçin  ×"}</p>
          <p className="text-[15px] font-medium text-muted">{"⌕  Şəhər axtar"}</p>
          <p className="text-base font-medium text-ink">{"● Bakı     Sumqayıt     Gəncə\nMingəçevir     Şirvan     Lənkəran\nŞəki     Quba     Naxçıvan\nXırdalan     Qəbələ     Digər şəhərlər"}</p>
        </div>
      </Block>
    </main>
  );
}
