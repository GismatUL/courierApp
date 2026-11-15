"use client";
import { useEffect, useRef, useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import clsx from "clsx";
import { AuthLayout } from "@/components/AuthPanel";
import { btn } from "@/components/ui";

/* Figma frame "10 · SMS təsdiqi" (2:20272), workspace 2:20274. */

const LENGTH = 6;

export default function VerifyPage() {
  const router = useRouter();
  const [code, setCode] = useState<string[]>(Array(LENGTH).fill(""));
  const [left, setLeft] = useState(45);
  const [phone, setPhone] = useState("+994 50 123 45 67");
  const refs = useRef<(HTMLInputElement | null)[]>([]);
  useEffect(() => {
    try { const p = sessionStorage.getItem("phone"); if (p) setPhone(p); } catch {}
    refs.current[0]?.focus();
  }, []);
  useEffect(() => {
    if (left <= 0) return;
    const t = setTimeout(() => setLeft(left - 1), 1000);
    return () => clearTimeout(t);
  }, [left]);

  const set = (i: number, v: string) => {
    const digits = v.replace(/\D/g, "");
    if (digits.length > 1) {
      // Paste of the full code
      const next = digits.slice(0, LENGTH).split("");
      setCode([...next, ...Array(LENGTH - next.length).fill("")]);
      refs.current[Math.min(next.length, LENGTH - 1)]?.focus();
      return;
    }
    setCode((c) => c.map((x, j) => (j === i ? digits : x)));
    if (digits && i < LENGTH - 1) refs.current[i + 1]?.focus();
  };
  const filled = code.every(Boolean);

  return (
    <AuthLayout>
      <form className="contents" onSubmit={(e) => {
        e.preventDefault();
        if (filled) router.push("/dashboard");
        else refs.current[code.findIndex((x) => !x)]?.focus();
      }}>
        <Link href="/login" className="whitespace-pre text-sm font-semibold text-link">{"←  Nömrəni dəyiş"}</Link>
        <h2 className="text-[32px] font-bold text-ink">SMS kodunu yazın.</h2>
        <p className="text-base text-muted">Təsdiq kodunu {phone}<br />nömrəsinə göndərdik.</p>
        <div className="flex gap-3">
          {code.map((d, i) => (
            <input
              key={i}
              ref={(el) => { refs.current[i] = el; }}
              value={d}
              onChange={(e) => set(i, e.target.value)}
              onKeyDown={(e) => { if (e.key === "Backspace" && !d && i > 0) refs.current[i - 1]?.focus(); }}
              inputMode="numeric"
              autoComplete={i === 0 ? "one-time-code" : "off"}
              maxLength={LENGTH}
              aria-label={`Kodun ${i + 1}-ci rəqəmi`}
              placeholder="—"
              className={clsx(
                "h-[67px] w-[72px] min-w-0 rounded-3xl p-4 text-center text-[25px] font-semibold outline-none placeholder:text-muted focus:bg-brand-surface",
                d ? "bg-brand-surface text-link" : "bg-surface text-ink",
              )}
            />
          ))}
        </div>
        <button type="submit" className={btn("accent", "lg", "w-full whitespace-pre")}>{"Təsdiqlə və daxil ol  →"}</button>
        {left > 0 ? (
          <p className="text-sm font-medium text-muted">Kodu yenidən göndər · 00:{String(left).padStart(2, "0")}</p>
        ) : (
          <button type="button" onClick={() => setLeft(45)} className="text-left text-sm font-medium text-link">Kodu yenidən göndər</button>
        )}
        <a href="tel:+994124000000" className="whitespace-pre-wrap text-sm font-semibold text-link">{"Kod gəlmədi? Dəstək ilə əlaqə saxlayın  ↗"}</a>
      </form>
    </AuthLayout>
  );
}
