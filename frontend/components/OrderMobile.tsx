"use client";
import { useState, type ReactNode } from "react";
import Link from "next/link";
import clsx from "clsx";
import { BakuMap } from "./BakuMap";
import { BoxField } from "./PriceCalculator";
import { FigmaIcon, btn, Chevron, SelectChevron } from "./ui";
import { azn, num, VEHICLES, type Vehicle } from "@/lib/pricing";

/* Figma frame "12 · Mobil / Sifariş" (2:20294): app header 2:20295, form 2:20296, price sheet 2:20297. */

export type MobilePoint = { address: string; window: string; name: string; phone: string; entry: string; notes: string; cash: boolean; cashAmount: string };
type Extras = { doorToDoor: boolean; loaders: number; thermalBag: boolean; sms: boolean };

export interface OrderMobileProps {
  vehicle: Vehicle; setVehicle: (v: Vehicle) => void;
  mode: "fast" | "scheduled"; setMode: (m: "fast" | "scheduled") => void;
  points: MobilePoint[]; update: (i: number, p: Partial<MobilePoint>) => void; addPoint: () => void; removePoint: (i: number) => void;
  weight: number; setWeight: (n: number) => void; maxKg: number;
  what: string; setWhat: (s: string) => void; declared: string; setDeclared: (s: string) => void;
  extras: Extras; toggleExtra: (k: keyof Extras) => void;
  payment: "cash" | "card" | "balance"; setPayment: (p: "cash" | "card" | "balance") => void;
  promo: string; setPromo: (s: string) => void; applyPromo: () => void;
  km: number; lines: { label: string; amount: number }[]; total: number; from: string; to: string;
  onSubmit: () => void;
}

const input = "w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:text-ink";
const SAMPLE = [
  { title: "A  Haradan götürək?", address: "Nizami küçəsi 203, Bakı", window: "Bu gün, 14:00–14:15", name: "Rəşad Əliyev", phone: "+994 50 123 45 67", cash: "Məhsulun pulunu ödə" },
  { title: "B  Hara çatdıraq?", address: "Mərkəzi Bulvar küç. 5, Ağ Şəhər", window: "Bu gün, 14:30–15:00", name: "Aysel Məmmədova", phone: "+994 55 765 43 21", cash: "Alıcıdan nağd pul topla" },
];
const EXTRAS: [keyof Extras, string, string][] = [
  ["doorToDoor", "Qapıdan qapıya         ", "+1,00 ₼"],
  ["loaders", "Yükdaşıyan köməkçi    ", "+8,00 ₼-dən"],
  ["thermalBag", "Termoçanta                ", "+1,50 ₼"],
  ["sms", "Alıcıya SMS                ", "Pulsuz"],
];

/** Price-sheet rows use space-aligned text in the design (2:23045); known labels keep its exact spacing. */
const SHEET_LABELS: Record<string, string> = {
  "Başlanğıc tarif": "Başlanğıc                  ",
  "Qapıdan qapıya": "Qapıdan qapıya          ",
};
const sheetLabel = (label: string) => SHEET_LABELS[label] ?? (label.startsWith("Məsafə") ? "Məsafə                       " : `${label}    `);

const Card = ({ children }: { children: ReactNode }) => <section className="flex flex-col gap-4 rounded-[20px] bg-card p-5 leading-[1.4]">{children}</section>;

export function OrderMobile(p: OrderMobileProps) {
  const [sheetOpen, setSheetOpen] = useState(true);
  return (
    <div className="bg-surface text-ink lg:hidden">
      {/* App header 2:20295 */}
      <header className="sticky top-0 z-40 flex flex-col gap-6 bg-card px-5 py-6 leading-[1.4]">
        <div className="flex items-start gap-4">
          <Link href="/" className="w-[280px] whitespace-pre text-xl font-bold">{"←  Yeni sifariş"}</Link>
          <a href="tel:+994124000000" className="text-xs font-semibold text-link">Kömək</a>
        </div>
        <p className="whitespace-pre text-xs font-semibold text-link">{"1 Marşrut    →    2 Ödəniş    →    3 Təsdiq"}</p>
      </header>

      <form onSubmit={(e) => { e.preventDefault(); p.onSubmit(); }}>
        <div className="flex flex-col gap-6 px-5 py-6">
          {/* Vehicle selection 2:22914 */}
          <Card>
            <h2 className="text-2xl font-bold">Nə ilə çatdıraq?</h2>
            <div className="flex items-start gap-2">
              {(Object.keys(VEHICLES) as Vehicle[]).map((v) => (
                <button
                  key={v}
                  type="button"
                  aria-pressed={p.vehicle === v}
                  onClick={() => p.setVehicle(v)}
                  className={clsx("flex w-[98px] min-w-0 flex-col items-start gap-3 rounded-xl border p-5 text-left", p.vehicle === v ? "border-brand-500 bg-brand-surface" : "border-line bg-card")}
                >
                  <FigmaIcon name={v} />
                  <span className="text-[15px] font-semibold">{VEHICLES[v].label}</span>
                  <span className="text-[13px] text-muted">{VEHICLES[v].capacity}</span>
                </button>
              ))}
            </div>
            <p className="text-[11px] text-muted">Piyada 15 kq · Avto 200 kq · Yük 1 500 kq</p>
            <div className="flex gap-2.5">
              {([["fast", "Sürətli", "30–60 dəq"], ["scheduled", "Planlı", "4 saatlıq interval"]] as const).map(([m, t, d]) => (
                <button key={m} type="button" aria-pressed={p.mode === m} onClick={() => p.setMode(m)} className={clsx("flex w-[150px] flex-col items-start gap-4 rounded-[20px] p-[14px] text-left", p.mode === m ? "bg-brand-surface" : "bg-surface")}>
                  <span className={clsx("text-[15px] font-semibold", p.mode === m ? "text-link" : "text-ink")}>{`${p.mode === m ? "●" : "○"} ${t}`}</span>
                  <span className="text-xs text-muted">{d}</span>
                </button>
              ))}
            </div>
            <p className="whitespace-pre text-sm font-semibold text-link">{p.mode === "fast" ? "Bu gün · İndi  " : "15 noyabr · 10:00–14:00  "}<Chevron className="align-middle" /></p>
          </Card>

          {/* Route points 2:22938 / 2:22960 */}
          {p.points.map((pt, i) => {
            const s = SAMPLE[i === 0 ? 0 : 1];
            const title = i === 0 ? s.title : i === 1 ? s.title : `${String.fromCharCode(65 + i)}  Hara çatdıraq?`;
            return (
              <Card key={i}>
                <div className="flex items-start justify-between gap-2">
                  <h2 className="whitespace-pre text-[23px] font-bold">{title}</h2>
                  {i > 1 && <button type="button" onClick={() => p.removePoint(i)} className="text-sm font-semibold text-muted">Sil</button>}
                </div>
                <BoxField label="Ünvan · xəritədə seç ↗" normalCase><input className={input} value={pt.address} onChange={(e) => p.update(i, { address: e.target.value })} placeholder={s.address} /></BoxField>
                <BoxField label="Tarix və vaxt aralığı" normalCase><input className={input} value={pt.window} onChange={(e) => p.update(i, { window: e.target.value })} placeholder={s.window} /></BoxField>
                <BoxField label="Əlaqədar şəxs" normalCase><input className={input} value={pt.name} onChange={(e) => p.update(i, { name: e.target.value })} placeholder={s.name} /></BoxField>
                <BoxField label="Telefon nömrəsi" normalCase><input className={input} type="tel" value={pt.phone} onChange={(e) => p.update(i, { phone: e.target.value })} placeholder={s.phone} /></BoxField>
                <BoxField label="Giriş, mərtəbə, mənzil" normalCase><input className={input} value={pt.entry} onChange={(e) => p.update(i, { entry: e.target.value })} placeholder="2-ci giriş, mərtəbə 3, mənzil 12" /></BoxField>
                <BoxField label="Kuryerə qeyd" normalCase><input className={input} value={pt.notes} onChange={(e) => p.update(i, { notes: e.target.value })} placeholder="Gəlməzdən əvvəl zəng edin" /></BoxField>
                <button type="button" role="checkbox" aria-checked={pt.cash} onClick={() => p.update(i, { cash: !pt.cash })} className="whitespace-pre text-left text-sm font-medium">
                  {`${pt.cash ? "●" : "○"}  ${s.cash}`}
                </button>
                <label className="flex text-[13px] text-muted">
                  Məbləğ:&nbsp;
                  <input aria-label="Məbləğ" inputMode="decimal" disabled={!pt.cash} value={pt.cashAmount} onChange={(e) => p.update(i, { cashAmount: e.target.value })} placeholder="0,00" className="w-10 bg-transparent outline-none placeholder:text-muted" />
                  ₼
                </label>
              </Card>
            );
          })}
          <button type="button" onClick={p.addPoint} className={btn("soft", "lg", "w-full whitespace-pre")}>{"+  Çatdırılma ünvanı əlavə et"}</button>
          <p className="text-xs leading-[1.4] text-muted">İstədiyiniz qədər nöqtə əlavə edin.</p>

          {/* Parcel 2:22985 */}
          <Card>
            <h2 className="text-2xl font-bold">Bağlama</h2>
            <BoxField label="Nə göndərirsiniz?" normalCase><input className={input} value={p.what} onChange={(e) => p.setWhat(e.target.value)} placeholder="Geyim · 1 bağlama" /></BoxField>
            <BoxField label="Çəki" normalCase><span className="whitespace-pre text-[15px] font-semibold">{`${p.weight} kq  ·  1–${num(p.maxKg, 0).replace(/\B(?=(\d{3})+(?!\d))/g, " ")} kq`}</span></BoxField>
            <input type="range" aria-label="Çəki" min={1} max={p.maxKg} value={p.weight} onChange={(e) => p.setWeight(+e.target.value)} className="figma-range-m" />
            <BoxField label="Bəyan edilmiş dəyər" normalCase><input className={input} inputMode="decimal" value={p.declared} onChange={(e) => p.setDeclared(e.target.value)} placeholder="100,00 ₼" /></BoxField>
            <p className="whitespace-pre text-[13px] font-medium text-link">{"✓  100 ₼-dək dəyər daxildir"}</p>
          </Card>

          {/* Extras 2:22997 */}
          <Card>
            <h2 className="text-[23px] font-bold">Əlavə xidmətlər</h2>
            {EXTRAS.map(([k, l, price]) => {
              const on = k === "loaders" ? p.extras.loaders > 0 : Boolean(p.extras[k]);
              return (
                <button key={k} type="button" role="checkbox" aria-checked={on} onClick={() => p.toggleExtra(k)} className="whitespace-pre text-left text-sm font-medium">
                  {`${on ? "☑" : "☐"}  ${l}${price}`}
                </button>
              );
            })}
          </Card>

          {/* Payment 2:23003 */}
          <Card>
            <h2 className="text-[23px] font-bold">Ödəniş</h2>
            <BoxField label="Ödəniş üsulu" normalCase>
              <SelectChevron>
                <select className={`${input} cursor-pointer appearance-none pr-6`} value={p.payment} onChange={(e) => p.setPayment(e.target.value as OrderMobileProps["payment"])}>
                  <option value="cash">Nağd</option><option value="card">Bank kartı</option><option value="balance">Şirkət balansı</option>
                </select>
              </SelectChevron>
            </BoxField>
            <p className="text-[13px] text-muted">Bank kartı / Şirkət balansı</p>
            <BoxField label="Promokod" normalCase><input className={input} value={p.promo} onChange={(e) => p.setPromo(e.target.value)} placeholder="Kodu daxil edin" /></BoxField>
            <button type="button" onClick={p.applyPromo} className={btn("soft", "lg", "w-full")}>Promokodu tətbiq et</button>
          </Card>

          {/* Route preview 2:23014 */}
          <Card>
            <h2 className="text-[21px] font-bold">Marşrutunuz · {num(p.km, 1)} km</h2>
            <BakuMap width={310} className="max-w-full" />
            <p className="text-[13px] font-medium">{`${p.from} → ${p.to}`}</p>
          </Card>
        </div>

        {/* Price sheet 2:20297 — placed at the end of the form as in the frame; breakdown collapsible */}
        <div className="flex flex-col gap-6 rounded-3xl bg-card px-5 py-6 leading-[1.4]">
          <span className="h-[5px] w-[50px] rounded-[3px] bg-line" aria-hidden />
          <button type="button" aria-expanded={sheetOpen} onClick={() => setSheetOpen(!sheetOpen)} className="whitespace-pre text-left text-lg font-bold">
            {"Qiymət xülasəsi  "}<Chevron up={sheetOpen} className="align-middle" />
          </button>
          {sheetOpen && (
            <p className="whitespace-pre text-sm font-medium text-muted">
              {p.lines.map((l) => `${sheetLabel(l.label)}${azn(l.amount)}`).join("\n")}
            </p>
          )}
          <div className="flex items-start gap-4">
            <span className="w-[170px] text-[21px] font-semibold">Cəmi</span>
            <span className="flex-1 text-right text-[28px] font-bold">{azn(p.total)}</span>
          </div>
          <button type="submit" className={btn("accent", "lg", "w-full whitespace-pre")}>{`Sifariş et · ${azn(p.total)}  →`}</button>
          <p className="text-[11px] text-muted">Təsdiqləməklə xidmət şərtləri ilə razılaşırsınız.</p>
        </div>
      </form>
    </div>
  );
}
