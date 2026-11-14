import clsx from "clsx";
import { BakuMap } from "./BakuMap";

const CROSS = [-162.88, -74.88, 13.12, 101.12, 189.12, 277.12, 365.12, 453.12];

/** Geometry for the two map widths used in Figma ("Live route map" 2:20641 / 2:20738). */
const MAP = {
  244: { sea: 195.2, seaW: 73.2, streetTop: -81.52, streetH: 85.216, streetW: 339.394, bar: 344, park: 109.8, courier: 100.04, route: "route" },
  214: { sea: 171.2, seaW: 64.2, streetTop: -75.28, streetH: 78.978, streetW: 310.049, bar: 314, park: 96.3, courier: 87.74, route: "route-214" },
} as const;

/** Schematic map of Baku with courier ETA. */
export function LiveRouteMap({ width = 244, eta = "12 dəq", className }: { width?: 244 | 214; eta?: string; className?: string }) {
  const m = MAP[width];
  return (
    <div className={clsx("relative h-[270px] shrink-0 overflow-hidden rounded-3xl bg-[#EAF0EC]", className)} style={{ width }} aria-hidden>
      <div className="absolute top-0 h-[270px] rounded-[40px] bg-[#BDDFFC]" style={{ left: m.sea, width: m.seaW }} />
      {Array.from({ length: 7 }, (_, i) => (
        <div key={i} className="absolute left-[-40px] flex items-center justify-center" style={{ top: m.streetTop + i * 70, height: m.streetH, width: m.streetW }}>
          <div className="h-[14px] shrink-0 -rotate-12 bg-white" style={{ width: m.bar }} />
        </div>
      ))}
      {CROSS.map((left) => (
        <div key={left} className="absolute top-[-70px] flex h-[412.663px] w-[144.29px] items-center justify-center" style={{ left }}>
          <div className="h-[430px] w-[12px] shrink-0 rotate-[18deg] bg-white" />
        </div>
      ))}
      <div className="absolute top-[51.3px] h-[75px] w-[95px] rounded-2xl bg-[#CBE1C3]" style={{ left: m.park }} />
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img src={`/figma/phone/${m.route}.svg`} alt="" className="absolute inset-0 h-full w-full" />
      <div className="absolute left-[30px] top-[22px] w-[120px] bg-white p-2.5 text-lg font-bold leading-[1.4] text-night">Bakı</div>
      <div className="absolute top-[135px] w-[112px] whitespace-pre bg-brand-500 p-2.5 text-sm font-bold leading-[1.4] text-white" style={{ left: m.courier }}>{`➤  ${eta}`}</div>
    </div>
  );
}

/** Mobile-frame variant "Phone · tracking" (2:22667): 310 wide, radius 32, screen p16 radius 20, scaled Baku map. */
export function PhoneTrackingMobile({ className }: { className?: string }) {
  return (
    <div className={clsx("w-[310px] max-w-full shrink-0 rounded-[32px] bg-night p-[9px]", className)}>
      <div className="flex flex-col gap-4 overflow-hidden rounded-[20px] bg-white p-4 leading-[1.4] text-night">
        <p className="whitespace-pre text-[11px] font-semibold">{"9:41                         ● ▰"}</p>
        <p className="text-[19px] font-bold">Sifarişiniz yoldadır</p>
        <BakuMap width={260} />
        <p className="text-xl font-bold text-brand-500">12 dəqiqəyə qapınızda</p>
        <p className="whitespace-pre text-[13px] font-medium">{"✓  Kuryer təyin edildi\n✓  Götürüldü\n●  Yoldadır\n○  Çatdırıldı"}</p>
        <span className="rounded-xl bg-[#EBF3FF] p-[18px] text-center text-[15px] font-semibold">Kuryerlə əlaqə</span>
      </div>
    </div>
  );
}

/** Figma "Phone · live tracking" (2:20636): dark frame, 9px bezel, radius 36; screen radius 28, p 14, gap 16. */
export function PhoneTracking({ className, width = 290 }: { className?: string; width?: 290 | 260 }) {
  return (
    <div className={clsx("shrink-0 rounded-[36px] bg-night p-[9px]", className)} style={{ width }}>
      <div className="flex flex-col gap-4 overflow-hidden rounded-[28px] bg-white p-[14px] text-night">
        <p className="whitespace-pre text-[11px] font-semibold leading-[1.4]">{"9:41                     ● ▰"}</p>
        <p className="text-lg font-bold leading-[1.4]">Sifarişiniz yoldadır</p>
        <p className="text-[11px] leading-[1.4] text-[#627087]">#AZ-2048 · təxmini 14:42</p>
        <LiveRouteMap width={width === 290 ? 244 : 214} />
        <p className="text-[19px] font-bold leading-[1.4] text-brand-500">12 dəqiqəyə qapınızda</p>
        <ul className="text-xs font-medium leading-[1.4]">
          <li>● Kuryer təyin edildi</li>
          <li>● Bağlama götürüldü</li>
          <li>● Yoldadır</li>
          <li>○ Çatdırıldı</li>
        </ul>
        <span className="rounded-xl bg-[#EBF3FF] p-[18px] text-center text-[15px] font-semibold leading-[1.4]">Kuryerlə əlaqə</span>
      </div>
    </div>
  );
}
