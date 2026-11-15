"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";
import clsx from "clsx";
import { AuthLayout, authInput } from "@/components/AuthPanel";
import { BoxField } from "@/components/PriceCalculator";
import { btn } from "@/components/ui";

/* Figma frame "09 · Telefonla giriş" (2:20268), workspace 2:20270 — updated in the Figma Make code layer (7:988):
   phone + password sign-in, "Şifrəni unutmusunuz?", and SMS sign-in as the fallback link. */

const TYPES = ["Fərdi", "Biznes", "Kuryer"] as const;
/** Updated inputs: 8px label gap, blue focus border with a soft 3px ring. */
const field = "!gap-2 transition-colors focus-within:shadow-[0_0_0_3px_rgba(199,33,39,0.12)]";

export default function LoginPage() {
  const router = useRouter();
  const [type, setType] = useState<(typeof TYPES)[number]>("Fərdi");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");

  // The phone number is kept in this tab's session only (never in the URL) so the SMS screen can show it.
  const goToSms = () => {
    try { sessionStorage.setItem("phone", phone); } catch {}
    router.push("/login/verify");
  };

  return (
    <AuthLayout>
      <form
        className="contents"
        onSubmit={(e) => {
          e.preventDefault();
          // No auth backend yet: a filled phone + password signs in to the demo dashboard.
          router.push("/dashboard");
        }}
      >
        <h2 className="text-[34px] font-bold text-ink">Xoş gəlmisiniz.</h2>
        <p className="text-base text-muted">Telefon nömrəniz və şifrənizlə təhlükəsiz daxil olun.</p>
        <div className="flex gap-2" role="radiogroup" aria-label="Hesab növü">
          {TYPES.map((t) => (
            <button
              key={t}
              type="button"
              role="radio"
              aria-checked={type === t}
              onClick={() => setType(t)}
              className={clsx("w-[160px] min-w-0 rounded-3xl p-3 text-center text-sm font-semibold", type === t ? "bg-brand-surface text-link" : "bg-surface text-ink")}
            >
              {t}
            </button>
          ))}
        </div>
        <BoxField label="Telefon nömrəsi" normalCase className={field}>
          <input className={authInput} required type="tel" inputMode="tel" autoComplete="tel" value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="+994 50 123 45 67" />
        </BoxField>
        <BoxField label="Şifrə" normalCase className={field}>
          <input
            className="w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:font-normal placeholder:text-[#A39FA3]"
            required
            type="password"
            autoComplete="current-password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Şifrənizi daxil edin"
          />
        </BoxField>
        <div className="-mt-3 flex justify-end">
          <button type="button" onClick={goToSms} className="text-[13px] font-medium text-link hover:underline">Şifrəni unutmusunuz?</button>
        </div>
        <button type="submit" className={btn("accent", "lg", "w-full whitespace-pre")}>{"Hesaba daxil ol  →"}</button>
        <p className="text-[13px] text-muted">Davam etməklə istifadə şərtləri və məxfilik siyasəti ilə razılaşırsınız.</p>
        <button type="button" onClick={goToSms} className="text-left text-sm font-medium text-link hover:underline">
          Şifrə ilə daxil ola bilmirsiniz? SMS kodu ilə daxil olun →
        </button>
      </form>
    </AuthLayout>
  );
}
