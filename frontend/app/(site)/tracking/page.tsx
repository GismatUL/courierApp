"use client";
import { useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import clsx from "clsx";
import { BakuMap } from "@/components/BakuMap";
import { Container, StatusBadge, btn } from "@/components/ui";

/* Figma frame "03 · Sifarişi izlə" (2:20235). */

const TIMELINE = [
  { label: "Kuryer təyin edildi", time: "14:05" },
  { label: "Bağlama götürüldü", time: "14:18" },
  { label: "Yoldadır", time: "14:30" },
  { label: "Çatdırıldı", time: "Təxmini 14:42" },
];
const CURRENT = 2; // index of the active step

export default function TrackingPage() {
  // Demo: ETA counts down once a minute. Replace with WebSocket / polling to the backend.
  const [eta, setEta] = useState(12);
  const [copied, setCopied] = useState(false);
  useEffect(() => {
    const id = setInterval(() => setEta((m) => Math.max(1, m - 1)), 60_000);
    return () => clearInterval(id);
  }, []);
  const share = async () => {
    try {
      await navigator.clipboard.writeText(window.location.href);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch {}
  };

  return (
    <>
    <TrackingMobile eta={eta} share={share} copied={copied} />
    <div className="hidden bg-surface lg:block">
      <Container className="flex flex-col gap-7 pb-16 pt-10 leading-[1.4]">
        <p className="whitespace-pre text-[13px] font-medium text-muted">
          <Link href="/dashboard" className="hover:text-ink">Sifarişlərim</Link>{"  /  "}#AZ-2048
        </p>
        <div className="flex flex-col gap-4 md:flex-row md:items-start">
          <h1 className="text-[32px] font-bold text-ink md:w-[900px] md:shrink-0 md:text-[40px]">Bağlamanız yoldadır.</h1>
          <button type="button" onClick={share} className={btn("soft", "lg", "w-full whitespace-pre md:w-[396px]")}>
            {copied ? "Link kopyalandı ✓" : "İzləmə linkini paylaş  ↗"}
          </button>
        </div>

        <div className="flex flex-col gap-8 lg:flex-row lg:items-start">
          {/* Map and delivery details 2:21480 */}
          <div className="flex min-w-0 flex-col gap-4 lg:w-[840px] lg:min-w-0 lg:shrink">
            <div className="overflow-hidden rounded-[33.6px]">
              <BakuMap width={840} eta={`${eta} dəq`} className="max-w-full" />
            </div>
            <div className="flex flex-col gap-5 rounded-3xl bg-brand-500 p-6 text-white" aria-live="polite">
              <p className="text-[48px] font-bold">{eta} dəqiqə</p>
              <p className="text-base font-medium">Təxmini çatma vaxtı 14:42 · məlumatlar yenilənir</p>
            </div>
            <div className="flex flex-col gap-5 rounded-3xl bg-card p-6">
              <h2 className="text-[23px] font-bold text-ink">Marşrutunuz</h2>
              <p className="whitespace-pre text-base font-medium text-ink">{"A   Nizami küçəsi 203, Bakı\n      Götürüldü · 14:18"}</p>
              <p className="whitespace-pre text-base font-medium text-ink">{"B   Mərkəzi Bulvar küçəsi 5, Ağ Şəhər\n      Aysel Məmmədova · +994 55 765 43 21"}</p>
              <button type="button" className={btn("soft", "lg", "w-full whitespace-pre")}>{"Vaxtı və ya təlimatı dəyiş  ↗"}</button>
            </div>
          </div>

          {/* Courier and status 2:21481 */}
          <div className="flex flex-col gap-4 lg:w-[440px] lg:min-w-0 lg:shrink">
            <div className="flex flex-col gap-5 rounded-3xl bg-card p-6">
              <div className="flex gap-4">
                <Image src="/figma/photos/elvin.png" alt="Kuryer Elvin Həsənov" width={80} height={80} className="h-20 w-20 rounded-full object-cover" />
                <div className="flex flex-col gap-4">
                  <p className="text-[22px] font-bold text-ink">Elvin Həsənov</p>
                  <p className="whitespace-pre text-sm font-medium text-muted">{"★ 4,9  ·  1 248 çatdırılma"}</p>
                </div>
              </div>
              <p className="text-[15px] font-medium text-ink">Toyota Prius · 10-AB-123</p>
              <div className="grid grid-cols-2 gap-3">
                <a href="tel:+994000000000" className={btn("soft", "lg", "whitespace-pre")}>{"☎  Zəng et"}</a>
                <a href="#" className={btn("soft", "lg", "whitespace-pre")}>{"↗  Yaz"}</a>
              </div>
            </div>
            <div className="flex flex-col gap-5 rounded-3xl bg-card p-6">
              <h2 className="text-[23px] font-bold text-ink">Sifariş #AZ-2048</h2>
              <span className="w-[180px] rounded-xl bg-brand-surface p-2.5 text-[13px] font-semibold text-link">Yoldadır</span>
              <ol className="flex flex-col gap-5">
                {TIMELINE.map((s, i) => (
                  <li key={s.label} className="flex flex-col gap-5">
                    <span className={clsx("whitespace-pre text-[17px] font-semibold", i < CURRENT ? "text-[#087F5B]" : i === CURRENT ? "text-link" : "text-muted")}>
                      {`${i < CURRENT ? "✓" : i === CURRENT ? "●" : "○"}  ${s.label}`}
                    </span>
                    <span className="text-[13px] text-muted">{s.time}</span>
                  </li>
                ))}
              </ol>
              <p className="text-base font-semibold text-ink">Ödəniş: 8,40 ₼ · Nağd</p>
            </div>
            <div className="flex flex-col gap-5 rounded-3xl bg-brand-surface p-6">
              <h2 className="text-xl font-bold text-ink">Alıcı üçün də rahatdır</h2>
              <p className="text-[15px] text-muted">Hesaba daxil olmadan canlı izləmə, kuryerlə əlaqə və vaxt dəyişmə.</p>
              <a href="tel:+994124000000" className={btn("soft", "lg", "w-full")}>09:00–19:00 dəstək</a>
            </div>
          </div>
        </div>

        {/* Delivered state preview 2:21547 */}
        <section className="flex flex-col gap-5 rounded-3xl bg-card p-6">
          <h2 className="text-2xl font-bold text-ink">Çatdırıldıqdan sonra: təhvil təsdiqi</h2>
          <p className="text-[13px] text-muted">Tamamlanmış sifariş nümunəsi · #AZ-2047</p>
          <div className="flex flex-col gap-6 md:flex-row md:items-start">
            <Image src="/figma/photos/proof.png" alt="Təhvil fotosu: qapının ağzında bağlama" width={300} height={180} className="h-[180px] w-full rounded-2xl object-cover md:w-[300px] md:shrink-0" />
            <div className="flex flex-col items-start gap-4">
              <span className="w-[200px] rounded-xl bg-[#E5F6EE] p-2.5 text-[13px] font-semibold text-[#087F5B]">Çatdırılıb</span>
              <p className="text-[21px] font-semibold text-ink">Təhvil verildi · 13 noyabr, 17:24</p>
              <p className="text-base text-muted">Alıcı: Nərmin Əliyeva<br />Təhvil fotosu və vaxtı sifariş tarixçəsində saxlanılır.</p>
              <a href="/figma/photos/proof.png" download className="whitespace-pre text-sm font-semibold text-link">{"Təsdiqi yüklə  ↓"}</a>
            </div>
          </div>
        </section>
      </Container>
    </div>
    </>
  );
}

const mCard = "flex flex-col gap-4 rounded-[20px] bg-card p-5 leading-[1.4]";

/** Figma frame "13 · Mobil / İzləmə" (2:20298): app header 2:20299, map 2:20300, details 2:20301. */
function TrackingMobile({ eta, share, copied }: { eta: number; share: () => void; copied: boolean }) {
  return (
    <div className="bg-surface text-ink lg:hidden">
      <header className="sticky top-0 z-40 flex flex-col items-start gap-6 bg-card px-5 py-6 leading-[1.4]">
        <div className="flex w-full items-start gap-4">
          <Link href="/dashboard" className="w-[260px] whitespace-pre text-lg font-bold">{"←  Sifariş #AZ-2048"}</Link>
          <button type="button" onClick={share} className="text-xs font-semibold text-link">{copied ? "Kopyalandı ✓" : "Paylaş ↗"}</button>
        </div>
        <StatusBadge tone="onway" className="w-[150px]">Yoldadır</StatusBadge>
      </header>
      <section className="flex flex-col gap-6 bg-card px-5 py-6">
        <BakuMap width={350} eta={`${eta} dəq`} className="max-w-full" />
        <div className="flex flex-col gap-4 rounded-[20px] bg-brand-500 p-5 leading-[1.4] text-white" aria-live="polite">
          <p className="text-[36px] font-bold">{eta} dəqiqə</p>
          <p className="text-sm font-medium">Təxmini çatma vaxtı · 14:42</p>
        </div>
      </section>
      <div className="flex flex-col gap-6 px-5 py-6">
        <div className={mCard}>
          <div className="flex items-start gap-[14px]">
            <Image src="/figma/photos/elvin.png" alt="Kuryer Elvin Həsənov" width={64} height={64} className="h-16 w-16 rounded-full object-cover" />
            <div className="flex flex-col gap-4">
              <p className="text-[19px] font-bold">Elvin Həsənov</p>
              <p className="text-xs font-medium text-muted">★ 4,9 · 1 248 çatdırılma</p>
            </div>
          </div>
          <p className="text-sm font-medium">Toyota Prius · 10-AB-123</p>
          <div className="grid grid-cols-2 gap-3">
            <a href="tel:+994000000000" className={btn("soft", "lg", "whitespace-pre")}>{"☎  Zəng et"}</a>
            <a href="#" className={btn("soft", "lg", "whitespace-pre")}>{"↗  Yaz"}</a>
          </div>
        </div>
        <div className={mCard}>
          <h2 className="text-[21px] font-bold">Çatdırılma mərhələləri</h2>
          {TIMELINE.map((s, i) => (
            <div key={s.label} className="contents">
              <p className={clsx("text-base font-semibold", i < CURRENT ? "text-[#087F5B]" : i === CURRENT ? "text-link" : "text-muted")}>
                {`${i < CURRENT ? "✓" : i === CURRENT ? "●" : "○"} ${s.label}`}
              </p>
              <p className="text-xs text-muted">{s.time}</p>
            </div>
          ))}
        </div>
        <div className={mCard}>
          <h2 className="text-[23px] font-bold">Marşrut</h2>
          <p className="whitespace-pre text-[15px] font-medium">{"A  Nizami küçəsi 203\n    Götürüldü · 14:18"}</p>
          <p className="whitespace-pre text-[15px] font-medium">{"B  Mərkəzi Bulvar küçəsi 5\n    Ağ Şəhər · Aysel Məmmədova"}</p>
          <button type="button" className={btn("soft", "lg", "w-full")}>Vaxtı və ya qeydi dəyiş</button>
          <p className="text-sm font-semibold">Ödəniş: 8,40 ₼ · Nağd</p>
        </div>
        <div className={mCard}>
          <h2 className="text-[22px] font-bold">Təhvil təsdiqi</h2>
          <p className="text-xs text-muted">Tamamlanmış sifariş nümunəsi<br />#AZ-2047 · 13 noyabr, 17:24</p>
          <Image src="/figma/photos/proof.png" alt="Təhvil fotosu: qapının ağzında bağlama" width={310} height={190} className="h-[190px] w-full rounded-2xl object-cover" />
          <StatusBadge tone="delivered" className="w-[170px]">Çatdırılıb</StatusBadge>
          <p className="text-sm text-muted">Təhvil fotosu və vaxtı saxlanılır.</p>
        </div>
        <a href="tel:+994124000000" className={btn("soft", "lg", "w-full")}>09:00–19:00 dəstək</a>
      </div>
    </div>
  );
}
