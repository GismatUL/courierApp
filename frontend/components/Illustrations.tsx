import clsx from "clsx";

/** Figma "Store / App Store" & "Store / Google Play" (2:23170, 2:23176): 224 wide, p 14, radius 12. */
export function StoreBadges({ className }: { className?: string }) {
  const badges = [
    { mark: "app-store", top: "Yükləyin", name: "App Store" },
    { mark: "google-play", top: "İndi əldə edin", name: "Google Play" },
  ];
  return (
    <div className={clsx("flex flex-wrap gap-4", className)}>
      {badges.map((b) => (
        <a key={b.mark} href="#" className="flex w-[224px] items-start gap-3 rounded-xl bg-night p-[14px] text-white">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={`/figma/phone/${b.mark}.svg`} alt="" width={28} height={32} className="h-8 w-7 shrink-0" />
          <span className="leading-[1.4]">
            <span className="block text-[10px]">{b.top}</span>
            <span className="block text-xl font-semibold">{b.name}</span>
          </span>
        </a>
      ))}
    </div>
  );
}

/** Figma "Generated illustration" (2:3), 624 × 449.6, layered SVGs in public/figma/hero. */
const HERO_LAYERS: { src: string; inset: string }[] = [
  { src: "skyline", inset: "24.69% 10.29% 28.16% 11.62%" },
  { src: "route", inset: "44.08% 12.78% 14.37% 13.55%" },
  { src: "pin-west", inset: "36.73% 76.03% 51.43% 18.09%" },
  { src: "pin-east", inset: "31.84% 16.03% 54.69% 77.21%" },
  { src: "van", inset: "55.1% 70.29% 32.65% 12.65%" },
  { src: "courier-scooter", inset: "26.66% 20.29% 13.67% 27.94%" },
];
export function HeroIllustration({ className, set = "hero" }: { className?: string; set?: "hero" | "courier" | "courier-hero" | "signup" | "auth" | "hero-m" }) {
  return (
    <div className={className}>
      <div className="relative w-full" style={{ aspectRatio: "624 / 449.647" }} aria-hidden>
        {HERO_LAYERS.map((l) => (
          <div key={l.src} className="absolute" style={{ inset: l.inset }}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={`/figma/${set}/${l.src}.svg`} alt="" className="block h-full w-full max-w-none" />
          </div>
        ))}
      </div>
    </div>
  );
}
