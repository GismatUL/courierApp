"use client";
import { useState, type FormEvent, type ReactNode } from "react";
import { Bike, Car, Footprints, Truck } from "lucide-react";
import Link from "next/link";
import clsx from "clsx";
import { btn } from "./ui";
import { calcPrice, estimateKm, azn, VEHICLES, type Vehicle } from "@/lib/pricing";
import { useI18n } from "@/lib/i18n";

export const vehicleIcon = { foot: Footprints, moto: Bike, car: Car, truck: Truck };

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
const heroField = "transition-colors hover:border-[#C9C1C1]";
const intro = "Ünvanları daxil edin, qiyməti öyrənin";

/** Figma "Quick price calculator" — desktop 2:20459 (576 wide, p 32, radius 24, gap 24) / mobile 2:22554 (p 20, radius 20, gap 16). */
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
    <form onSubmit={calc} className="flex flex-col gap-4 rounded-[20px] bg-card p-5 lg:gap-6 lg:rounded-3xl lg:p-8 lg:shadow-[0px_12px_40px_rgba(89,26,31,0.06)]">
      <div className="flex flex-col gap-4 lg:gap-3">
        <h2 className="text-2xl font-bold leading-[1.4] text-ink lg:text-3xl lg:leading-[1.35]">Hara çatdıraq?</h2>
        <p className="hidden rounded-[10px] bg-amber-surface p-2.5 text-sm font-semibold leading-[1.4] text-muted lg:block">{intro}</p>
      </div>
      <div className="flex flex-col gap-4 lg:gap-3">
        <BoxField label={t.from} className={heroField}>
          <input className={fieldInput} value={from} onChange={(e) => setFrom(e.target.value)} placeholder="Götürülmə ünvanı" required />
        </BoxField>
        <BoxField label={t.to} className={heroField}>
          <input className={fieldInput} value={to} onChange={(e) => setTo(e.target.value)} placeholder="Çatdırılma ünvanı" required />
        </BoxField>
        {/* Vehicle tabs (icon + name + capacity) instead of a dropdown; native radios keep arrow-key navigation. */}
        <fieldset className="rounded-xl border border-line bg-card p-4 leading-[1.4]">
          <legend className="float-left mb-3 w-full text-xs font-semibold uppercase text-muted">{t.vehicle}</legend>
          <div className="clear-both grid grid-cols-2 gap-2 sm:grid-cols-4">
            {(Object.keys(VEHICLES) as Vehicle[]).map((v) => {
              const Icon = vehicleIcon[v];
              const on = vehicle === v;
              return (
                <label
                  key={v}
                  className={clsx(
                    "flex cursor-pointer flex-col items-center gap-1 rounded-[10px] border px-1.5 py-2.5 text-center transition-colors has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-500/40",
                    on ? "border-brand-500 bg-brand-surface" : "border-line hover:border-[#C9C1C1]",
                  )}
                >
                  <input type="radio" name="vehicle" value={v} checked={on} onChange={() => setVehicle(v)} className="sr-only" />
                  <Icon aria-hidden className={clsx("size-6", on ? "text-link" : "text-muted")} strokeWidth={1.75} />
                  <span className={clsx("whitespace-nowrap text-xs font-semibold lg:text-[13px]", on ? "text-link" : "text-ink")}>{VEHICLES[v].label}</span>
                  <span className="whitespace-nowrap text-[11px] text-muted">{VEHICLES[v].capacity}</span>
                </label>
              );
            })}
          </div>
        </fieldset>
        <button className={btn("accent", "lg", "w-full")}>
          <span className="lg:hidden">{t.calc}</span><span className="hidden lg:inline">{t.calcDelivery}</span>&nbsp;&nbsp;→
        </button>
      </div>
      <div className="flex flex-col gap-4 lg:gap-3">
        <p className="text-sm font-semibold leading-[1.4] text-ink lg:text-[13px] lg:font-normal lg:leading-[1.35] lg:text-muted">
          {price === null ? (
            <><span className="lg:hidden">Qiymət</span><span className="hidden lg:inline">Yekun qiymət</span> marşruta görə hesablanır</>
          ) : (
            <>
              Təxmini qiymət: <b className="text-ink">{azn(price)}</b> ·{" "}
              <Link href="/order" className="font-semibold text-link">Sifariş et →</Link>
            </>
          )}
        </p>
        <Link href="/business" className="hidden text-sm font-semibold leading-[1.4] text-link lg:block">{t.forBusiness}&nbsp;&nbsp;↗</Link>
      </div>
      <p className="rounded-[20px] bg-amber-surface p-[14px] text-sm font-semibold leading-[1.4] text-muted lg:hidden">{intro}</p>
    </form>
  );
}
