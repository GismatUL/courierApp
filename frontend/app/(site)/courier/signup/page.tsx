"use client";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { AuthLayout } from "@/components/AuthPanel";
import { BoxField } from "@/components/PriceCalculator";
import { btn, SelectChevron } from "@/components/ui";
import { CITIES } from "@/lib/data";

/* Figma frame "07 · Kuryer qeydiyyatı" (2:20257), workspace 2:20259. Step 2 is the SMS screen (frame 10). */

const input = "w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:text-ink";

export default function CourierSignup() {
  const router = useRouter();
  return (
    <AuthLayout
      art="signup"
      eyebrow="SƏRBƏST QRAFİK. GÜNDƏLİK QAZANC."
      title={<>İlk sifarişinə<br />10 dəqiqə qalıb.</>}
      sub="Məlumatlarını yaz, nömrəni təsdiqlə və uyğun sifarişləri qəbul etməyə başla."
    >
        {/* Account form 2:22175 */}
        <form
          onSubmit={(e) => {
            e.preventDefault();
            try { sessionStorage.setItem("phone", String(new FormData(e.currentTarget).get("phone") ?? "")); } catch {}
            router.push("/login/verify");
          }}
          className="contents"
        >
          <h2 className="text-[32px] font-bold text-ink">Kuryer qeydiyyatı</h2>
          <p className="whitespace-pre-wrap text-[13px] font-semibold text-link">{"1 / 2  ·  Şəxsi məlumatlar"}</p>
          <BoxField label="Ad və soyad" normalCase><input className={input} required autoComplete="name" placeholder="Adınızı daxil edin" /></BoxField>
          <BoxField label="Telefon nömrəsi" normalCase><input className={input} name="phone" required type="tel" autoComplete="tel" placeholder="+994 50 123 45 67" /></BoxField>
          <BoxField label="Şəhər" normalCase>
            <SelectChevron>
              <select className={`${input} cursor-pointer appearance-none pr-6`} defaultValue="Bakı">
                {CITIES.map((c) => <option key={c} value={c}>{`${c}`}</option>)}
              </select>
            </SelectChevron>
          </BoxField>
          <BoxField label="Nəqliyyat növü" normalCase>
            <SelectChevron>
              <select className={`${input} cursor-pointer appearance-none pr-6`} defaultValue="Avtomobil">
                {["Piyada", "Avtomobil", "Yük maşını"].map((v) => <option key={v} value={v}>{`${v}`}</option>)}
              </select>
            </SelectChevron>
          </BoxField>
          <p className="text-xs text-muted">Piyada · Avtomobil · Yük maşını</p>
          <div className="text-[13px] text-muted">
            <label className="flex items-center gap-2"><input type="checkbox" required defaultChecked className="h-3.5 w-3.5 accent-muted" />18 yaşım var və şərtlərlə razıyam.</label>
            <label className="flex items-center gap-2"><input type="checkbox" required defaultChecked className="h-3.5 w-3.5 accent-muted" />Şəxsi məlumatların emalına razıyam.</label>
          </div>
          <button type="submit" className={btn("accent", "lg", "w-full whitespace-pre")}>{"Nömrəni təsdiqlə  →"}</button>
          <Link href="/login" className="whitespace-pre-wrap text-sm font-semibold text-link">{"Artıq qeydiyyatdan keçmisiniz? Daxil ol  ↗"}</Link>
          <p className="whitespace-pre-wrap text-sm font-medium text-ink">
            Kuryer tətbiqini yüklə<br />
            <a href="#">{"  App Store"}</a>{"     "}<a href="#">▷ Google Play</a>
          </p>
        </form>
    </AuthLayout>
  );
}
