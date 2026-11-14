"use client";
import { useMemo, useState, type ReactNode } from "react";
import Link from "next/link";
import clsx from "clsx";
import { StatusBadge, btn, type StatusTone, Chevron, SelectChevron } from "@/components/ui";
import { useI18n, type Lang } from "@/lib/i18n";

/* Figma frame "04 · Müştəri kabineti" (2:20239): own app layout, no site header/footer. */

const NAV = [
  ["◈  Sifarişlər", "#orders"],
  ["＋  Yeni sifariş", "/order"],
  ["⌖  Ünvanlar", "#addresses"],
  ["₼  Balans və hesablar", "#balance"],
  ["{ }  API açarları", "#api"],
  ["♧  Komanda", "#team"],
  ["⚙  Bildirişlər", "#notifications"],
] as const;

const STATUS: Record<StatusTone, string> = { onway: "Yoldadır", delivered: "Çatdırılıb", searching: "Kuryer axtarılır", cancelled: "Ləğv edilib" };
const ORDERS: { id: string; route: string; date: string; status: StatusTone; amount: string }[] = [
  { id: "#AZ-2048", route: "Nizami 203 → Ağ Şəhər", date: "Bu gün, 14:00", status: "onway", amount: "8,40 ₼" },
  { id: "#AZ-2047", route: "28 May → Yasamal", date: "Dünən, 17:24", status: "delivered", amount: "6,20 ₼" },
  { id: "#AZ-2046", route: "Gəncə mərkəz → Kəpəz", date: "Bu gün, 12:45", status: "searching", amount: "7,80 ₼" },
  { id: "#AZ-2045", route: "Sumqayıt → Xırdalan", date: "Bu gün, 12:00", status: "delivered", amount: "14,00 ₼" },
  { id: "#AZ-2044", route: "Sahil → Nərimanov", date: "Dünən, 18:20", status: "delivered", amount: "8,40 ₼" },
  { id: "#AZ-2043", route: "Şəki mərkəz → Kiş", date: "Dünən, 16:10", status: "cancelled", amount: "0,00 ₼" },
];
const COLS = ["Sifariş", "Marşrut", "Tarix", "Status", "Məbləğ", "Əməliyyat"];
const GRID = "grid grid-cols-[130fr_316fr_150fr_140fr_130fr_142fr] gap-4";

function Panel({ id, title, children, className }: { id?: string; title: string; children: ReactNode; className?: string }) {
  return (
    <section id={id} className={clsx("flex scroll-mt-6 flex-col gap-5 rounded-3xl bg-card p-6", className)}>
      <h2 className="text-[22px] font-bold text-ink">{title}</h2>
      {children}
    </section>
  );
}
const pill = "rounded-3xl bg-surface p-3 text-[13px] font-medium text-ink outline-none";

export default function DashboardPage() {
  const { lang, setLang } = useI18n();
  const [query, setQuery] = useState("");
  const [status, setStatus] = useState<"all" | StatusTone>("all");
  const [notify, setNotify] = useState({ sms: true, email: true, promo: false });
  const rows = useMemo(
    () => ORDERS.filter((o) => (status === "all" || o.status === status) && `${o.id} ${o.route}`.toLowerCase().includes(query.toLowerCase())),
    [query, status],
  );

  return (
    <div className="mx-auto flex min-h-screen max-w-page flex-col gap-6 p-4 leading-[1.4] lg:flex-row lg:items-start lg:p-6">
      {/* Dashboard navigation 2:21558 */}
      <nav className="flex shrink-0 flex-col gap-5 rounded-3xl bg-night p-6 lg:sticky lg:top-6 lg:h-[calc(100vh-48px)] lg:w-[232px]">
        <Link href="/" className="text-[26px] font-bold text-white">Yetir↗</Link>
        <p className="text-[11px] font-semibold text-[#98B2D4]">BİZNES KABİNETİ</p>
        {NAV.map(([label, href], i) => (
          <Link key={href} href={href} className={clsx("whitespace-pre rounded-xl p-3 text-[15px] font-medium text-white", i === 0 ? "bg-brand-500" : "hover:bg-card/10")}>
            {label}
          </Link>
        ))}
        <p className="text-[13px] text-[#B9C5D7]">24/7 dəstək<br /><a href="tel:+994120000000">+994 12 000 00 00</a></p>
      </nav>

      {/* Dashboard body 2:21576 */}
      <main className="flex min-w-0 flex-1 flex-col gap-6">
        <div className="flex flex-col gap-4 md:flex-row md:items-start">
          <p className="whitespace-pre text-lg font-semibold text-ink md:w-[660px] md:shrink-0">{"ALMA market  "}<Chevron className="align-middle" /></p>
          <p className="whitespace-pre text-sm font-medium text-ink">
            {(["az", "ru", "en"] as Lang[]).map((l, i) => (
              <span key={l}>{i > 0 && " / "}<button type="button" onClick={() => setLang(l)} className={clsx("uppercase", lang === l && "underline underline-offset-4")}>{l}</button></span>
            ))}
            {"       ♧       Rəşad Əliyev"}
          </p>
        </div>
        <div className="flex flex-col gap-4 md:flex-row md:items-start">
          <div className="flex flex-col gap-4 md:w-[820px] md:shrink-0">
            <h1 className="text-[30px] font-bold text-ink md:text-[36px]">Sifarişləriniz, bir yerdə.</h1>
            <p className="text-[15px] text-muted">Salam, Rəşad. Bu gün 12 çatdırılmanız var.</p>
          </div>
          <Link href="/order" className={btn("accent", "lg", "w-full whitespace-pre md:w-[300px] md:self-start")}>{"+  Yeni sifariş"}</Link>
        </div>

        {/* Metrics 2:21588 */}
        <div className="grid gap-4 md:grid-cols-3 md:items-start">
          {[
            ["Bu gün", "12", "8 çatdırılıb · 4 aktiv"],
            ["Bu ay", "248", "Keçən aya nisbətən +18%"],
            ["Şirkət balansı", "320,00 ₼", "Balansı artır  ↗"],
          ].map(([t, v, d]) => (
            <div key={t} className="flex flex-col gap-5 rounded-3xl bg-card p-6">
              <p className="text-sm font-medium text-muted">{t}</p>
              <p className="text-[34px] font-bold text-ink">{v}</p>
              <p className="whitespace-pre text-[13px] font-medium text-link">{d}</p>
            </div>
          ))}
        </div>

        {/* Orders table 2:21601 */}
        <section id="orders" className="flex scroll-mt-6 flex-col gap-5 overflow-x-auto rounded-3xl bg-card p-6">
          <h2 className="text-2xl font-bold text-ink">Son sifarişlər</h2>
          <div className="grid gap-3 md:grid-cols-[380fr_220fr_250fr_202fr]">
            <label className={clsx(pill, "flex whitespace-pre")}>
              {"⌕  "}<input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Nömrə və ya ünvan axtar" aria-label="Axtar" className="flex-1 bg-transparent outline-none placeholder:text-ink" />
            </label>
            <SelectChevron inset="right-3">
              <select className={clsx(pill, "w-full appearance-none pr-8")} aria-label="Dövr" defaultValue="month">
                <option value="month">Bu ay</option><option value="week">Bu həftə</option><option value="today">Bu gün</option>
              </select>
            </SelectChevron>
            <SelectChevron inset="right-3">
              <select className={clsx(pill, "w-full appearance-none pr-8")} aria-label="Status" value={status} onChange={(e) => setStatus(e.target.value as typeof status)}>
                <option value="all">Bütün statuslar</option>
                {(Object.keys(STATUS) as StatusTone[]).map((s) => <option key={s} value={s}>{STATUS[s]}</option>)}
              </select>
            </SelectChevron>
            <button type="button" className={clsx(pill, "whitespace-pre text-left")}>{"İxrac et  ↓"}</button>
          </div>
          <div role="table" className="min-w-[760px]">
            <div role="row" className={clsx(GRID, "pb-5 text-xs font-semibold text-muted")}>
              {COLS.map((c) => <span key={c} role="columnheader">{c}</span>)}
            </div>
            <div className="flex flex-col gap-5">
              {rows.map((o) => (
                <div role="row" key={o.id} className={clsx(GRID, "items-start py-3 text-[13px] text-ink")}>
                  <span role="cell" className="font-semibold">{o.id}</span>
                  <span role="cell">{o.route}</span>
                  <span role="cell">{o.date}</span>
                  <span role="cell"><StatusBadge tone={o.status} className="w-full">{STATUS[o.status]}</StatusBadge></span>
                  <span role="cell" className="font-semibold">{o.amount}</span>
                  <Link role="cell" href="/order" className="whitespace-pre text-link">{"Təkrarla  ↻"}</Link>
                </div>
              ))}
              {rows.length === 0 && <p className="py-3 text-[13px] text-muted">Heç nə tapılmadı.</p>}
            </div>
          </div>
          <p className="whitespace-pre text-[13px] font-medium text-muted">{"1–6 / 248 sifariş                                                   ←     1     2     3     →"}</p>
        </section>

        <div className="grid items-start gap-6 md:grid-cols-2">
          <Panel id="addresses" title="Saxlanmış ünvanlar">
            {["Baş ofis · Nizami küçəsi 203, Bakı", "Anbar · Babək prospekti 21, Bakı", "Filial · Gəncə, Atatürk prospekti 12"].map((a) => (
              <p key={a} className="whitespace-pre-wrap text-sm font-medium text-ink">{`⌖  ${a}`}</p>
            ))}
            <button type="button" className="text-left text-sm font-semibold text-link">+ Yeni ünvan əlavə et</button>
          </Panel>
          <Panel id="balance" title="Balans və hesab-fakturalar">
            <p className="whitespace-pre text-[22px] font-semibold text-link">{"Mövcud balans    320,00 ₼"}</p>
            <p className="text-sm text-muted">INV-1126 · Noyabr 2026 · 842,60 ₼<br />Müqavilə № B-2026-014</p>
            <button type="button" className={btn("soft", "lg", "w-full whitespace-pre")}>{"Hesab-fakturanı yüklə  ↓"}</button>
          </Panel>
        </div>
        <div className="grid items-start gap-6 md:grid-cols-2">
          <Panel id="api" title="API açarları">
            <p className="text-[15px] font-medium text-ink">İstehsal açarı<br /><code className="font-sans">yt_live_••••••••••••••8f2a</code></p>
            <p className="text-[13px] text-muted">Son istifadə: bu gün, 14:02<br />Açarı yenilə · API sənədləri ↗</p>
            <button type="button" className={btn("soft", "lg", "w-full whitespace-pre")}>{"+  Yeni açar yarat"}</button>
          </Panel>
          <Panel id="team" title="Komanda üzvləri">
            {["Rəşad Əliyev · Administrator", "Aysel Quliyeva · Operator", "Murad Həsənli · Maliyyə"].map((m) => (
              <p key={m} className="text-sm font-medium text-ink">{m}</p>
            ))}
            <button type="button" className={btn("soft", "lg", "w-full whitespace-pre")}>{"+  Üzv dəvət et"}</button>
          </Panel>
        </div>
        <Panel id="notifications" title="Bildiriş parametrləri">
          <div className="grid gap-6 md:grid-cols-3 md:items-start">
            {([["sms", "Sifariş statusları · SMS"], ["email", "Hesab-fakturalar · E-poçt"], ["promo", "Kampaniya xəbərləri"]] as const).map(([k, l]) => (
              <label key={k} className="flex cursor-pointer items-center gap-2 text-sm font-medium text-ink">
                <input type="checkbox" checked={notify[k]} onChange={() => setNotify((n) => ({ ...n, [k]: !n[k] }))} className="h-3.5 w-3.5 accent-muted" />
                {l}
              </label>
            ))}
          </div>
        </Panel>
      </main>
    </div>
  );
}
