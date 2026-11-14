"use client";
import { useState, type FormEvent, type ReactNode } from "react";
import { Car, Footprints, Truck } from "lucide-react";
import Link from "next/link";
import { btn, SelectChevron } from "./ui";
import { calcPrice, estimateKm, azn, type Vehicle } from "@/lib/pricing";
import { useI18n } from "@/lib/i18n";

export const vehicleIcon = { foot: Footprints, car: Car, truck: Truck };
const VEHICLE_LABEL: Record<Vehicle, string> = { foot: "Piyada", car: "Avtomobil", truck: "Yük maşını" };

/** Figma "Input / Default": bordered box, label 12/600 muted above value 15/600. */
export function BoxField({ label, children, className = "", normalCase }: { label: string; children: ReactNode; className?: string; normalCase?: boolean }) {
  return (
    <label className={`flex flex-col gap-3 rounded-xl border border-line bg-card p-4 leading-[1.4] focus-within:border-brand-500 ${className}`}>
      <span className={`text-xs font-semibold text-muted ${normalCase ? "" : "uppercase"}`}>{label}</span>
      {children}
    </label>
  );
}
const fieldInput = "w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:text-ink";
/** Updated hero inputs: 8px label gap and a hover border on desktop. */
const heroField = "transition-colors lg:gap-2 lg:hover:border-[#9FB4CF]";

/** Figma "Quick price calculator" (2:20459). */
export function PriceCalculator() {
  const { t } = useI18n();
  const [from, setFrom] = useState("");
  const [to, setTo] = useState("");
  const [vehicle, setVehicle] = useState<Vehicle>("car");
  const [price, setPrice] = useState<number | null>(null);

  const calc = (e: FormEvent) => {
    e.preventDefault();
    const km = estimateKm(from, to) || 8;
    setPrice(calcPrice({ vehicle, km, stops: 1, weightKg: 1, scheduled: false, extras: { doorToDoor: false, loaders: 0, thermalBag: false, sms: false }, declaredValue: 0 }).total);
  };

  return (
    // Desktop 2:20459 (updated: 560-wide card, p 28, radius 24, gap 18, stacked fields) / mobile 2:22554 (p 20, radius 20, promo at the bottom).
    <form onSubmit={calc} className="flex flex-col gap-4 rounded-[20px] bg-card p-5 lg:gap-[18px] lg:rounded-3xl lg:p-7 lg:shadow-[0px_20px_55px_rgba(13,38,77,0.14)]">
      <div className="flex flex-col gap-4 lg:gap-[14px]">
        <h2 className="text-2xl font-bold leading-[1.4] text-ink lg:text-[23px] lg:leading-[1.35]">Hara çatdıraq?</h2>
        <p className="hidden rounded-[10px] bg-amber-surface p-2.5 text-sm font-semibold leading-[1.4] text-ink lg:block">İlk sifarişə −40% · kod: [CODE]</p>
      </div>
      <div className="flex flex-col gap-4 lg:gap-[14px]">
        <BoxField label={t.from} className={heroField}>
          <input className={fieldInput} value={from} onChange={(e) => setFrom(e.target.value)} placeholder="Götürülmə ünvanı" required />
        </BoxField>
        <BoxField label={t.to} className={heroField}>
          <input className={fieldInput} value={to} onChange={(e) => setTo(e.target.value)} placeholder="Çatdırılma ünvanı" required />
        </BoxField>
        <BoxField label={t.vehicle} className={heroField}>
          <SelectChevron>
            <select className={`${fieldInput} cursor-pointer appearance-none pr-6`} value={vehicle} onChange={(e) => setVehicle(e.target.value as Vehicle)}>
              {(Object.keys(VEHICLE_LABEL) as Vehicle[]).map((v) => <option key={v} value={v}>{VEHICLE_LABEL[v]}</option>)}
            </select>
          </SelectChevron>
        </BoxField>
        <button className={btn("accent", "lg", "w-full lg:shadow-[0_8px_18px_rgba(255,184,0,0.22)] lg:hover:bg-[#F2AD00]")}>{t.calc}&nbsp;&nbsp;→</button>
      </div>
      <div className="flex flex-col gap-4 lg:gap-2.5">
        <p className="text-sm font-semibold leading-[1.4] text-ink lg:text-[13px] lg:font-normal lg:leading-[1.35] lg:text-muted">
          {price === null ? (
            <>Təxmini 8,40 ₼-dən<span className="hidden lg:inline"> · yekun qiymət ünvana görə hesablanır</span></>
          ) : (
            <>
              Təxmini qiymət: <b className="text-ink">{azn(price)}</b> ·{" "}
              <Link href="/order" className="font-semibold text-link">Sifariş et →</Link>
            </>
          )}
        </p>
        <Link href="/business" className="hidden text-sm font-semibold leading-[1.4] text-link lg:block">{t.forBusiness}&nbsp;&nbsp;↗</Link>
      </div>
      <p className="rounded-[20px] bg-amber-surface p-[14px] text-sm font-semibold leading-[1.4] text-ink lg:hidden">İlk sifarişə −40%<br />Promokod: [CODE]</p>
    </form>
  );
}
