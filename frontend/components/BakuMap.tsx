import clsx from "clsx";
import type { ReactNode } from "react";

/**
 * Figma master component "Map / Baku route" (600 × 380). Instances in the design are uniformly
 * scaled copies (e.g. 392 × 248.27 in the order summary), so the map is drawn at master size
 * and scaled with a transform to the requested width.
 */
export function BakuMap({ width, eta = "12 dəq", className, children }: { width: number; eta?: string; className?: string; children?: ReactNode }) {
  const k = width / 600;
  return (
    <div className={clsx("relative shrink-0 overflow-hidden bg-[#EAF0EC]", className)} style={{ width, height: 380 * k, borderRadius: 24 * k }} aria-hidden>
      <div className="absolute left-0 top-0 h-[380px] w-[600px] origin-top-left" style={{ transform: `scale(${k})` }}>
        <div className="absolute left-[480px] top-0 h-[380px] w-[180px] rounded-[40px] bg-[#BDDFFC]" />
        {Array.from({ length: 7 }, (_, i) => (
          <div key={i} className="absolute left-[-40px] flex h-[159.24px] w-[687.6px] items-center justify-center" style={{ top: -155.55 + i * 70 }}>
            <div className="h-[14px] w-[700px] shrink-0 -rotate-12 bg-white" />
          </div>
        ))}
        {Array.from({ length: 8 }, (_, i) => (
          <div key={i} className="absolute top-[-70px] flex h-[517.3px] w-[178.3px] items-center justify-center" style={{ left: -196.88 + i * 88 }}>
            <div className="h-[540px] w-[12px] shrink-0 rotate-[18deg] bg-white" />
          </div>
        ))}
        <div className="absolute left-[270px] top-[72.2px] h-[75px] w-[95px] rounded-2xl bg-[#CBE1C3]" />
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img src="/figma/phone/route-master.svg" alt="" className="absolute inset-0 h-full w-full" />
        <div className="absolute left-[30px] top-[22px] w-[120px] bg-white p-2.5 text-lg font-bold leading-[1.4] text-night">Bakı</div>
        <div className="absolute left-[246px] top-[190px] w-[112px] whitespace-pre bg-brand-500 p-2.5 text-sm font-bold leading-[1.4] text-white">{`➤  ${eta}`}</div>
      </div>
      {children}
    </div>
  );
}
