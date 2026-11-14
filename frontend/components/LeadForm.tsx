"use client";
import { useState } from "react";
import clsx from "clsx";
import { CheckCircle2 } from "lucide-react";
import { BoxField } from "./PriceCalculator";
import { Button } from "./ui";

const input = "w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:text-ink";
const VOLUMES = ["<30", "30–300", "300+"];

/** Figma "Business lead form" (2:20589): surface/subtle card, radius 24, p 24, gap 16. */
export function LeadForm({ className }: { className?: string }) {
  const [sent, setSent] = useState(false);
  const [volume, setVolume] = useState("30–300");
  if (sent)
    return (
      <div className={clsx("rounded-3xl bg-surface p-6 text-center text-ink", className)}>
        <CheckCircle2 className="mx-auto text-emerald-500" size={48} />
        <h3 className="mt-4 text-[26px] font-bold leading-[1.35]">Müraciətiniz qəbul edildi</h3>
        <p className="mt-2 text-muted">Menecerimiz 1 iş saatı ərzində sizinlə əlaqə saxlayacaq.</p>
      </div>
    );
  return (
    <form
      onSubmit={(e) => { e.preventDefault(); setSent(true); }}
      className={clsx("flex flex-col gap-4 rounded-[20px] bg-surface p-5 text-ink lg:rounded-3xl lg:p-6", className)}
    >
      <h3 className="text-[23px] font-bold leading-[1.4] lg:text-[26px] lg:leading-[1.35]">Sizinlə əlaqə saxlayaq</h3>
      <div className="grid gap-4 lg:grid-cols-2">
        <BoxField label="Adınız" normalCase><input className={input} required placeholder="Ad və soyad" /></BoxField>
        <BoxField label="Şirkət" normalCase><input className={input} required placeholder="Şirkətin adı" /></BoxField>
      </div>
      <BoxField label="Telefon" normalCase>
        <input className={input} required type="tel" placeholder="+994 50 123 45 67" pattern="^\+?994[\s\d]{9,12}$" />
      </BoxField>
      <fieldset className="flex flex-col gap-3 rounded-xl border border-line bg-card p-4 leading-[1.4]">
        <legend className="float-left text-xs font-semibold text-muted">Aylıq çatdırılma<span className="hidden lg:inline"> sayı</span></legend>
        <div className="clear-left flex flex-wrap items-center text-[15px] font-semibold">
          {VOLUMES.map((v, i) => (
            <span key={v} className="whitespace-pre">
              {i > 0 && <><span className="lg:hidden">{"  /  "}</span><span className="hidden lg:inline">{"     /     "}</span></>}
              <button type="button" aria-pressed={volume === v} onClick={() => setVolume(v)} className={volume === v ? "text-ink" : "text-ink/70 hover:text-ink"}>
                {v}{volume === v && " ✓"}
              </button>
            </span>
          ))}
        </div>
      </fieldset>
      <label className="flex items-center gap-2 text-xs leading-[1.4] text-muted lg:text-[13px] lg:leading-[1.35]">
        <input type="checkbox" required defaultChecked className="h-3.5 w-3.5 accent-muted" />
        Şəxsi məlumatların emalına razıyam.
      </label>
      <Button type="submit" variant="accent" size="lg" className="w-full">Təklif al&nbsp;&nbsp;→</Button>
    </form>
  );
}
