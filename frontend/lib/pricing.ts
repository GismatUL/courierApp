export type Vehicle = "foot" | "car" | "truck";

export const VEHICLES: Record<
  Vehicle,
  { label: string; capacity: string; maxKg: number; base: number; perKm: number; perStop: number; perWaitMin: number; perKg: number }
> = {
  foot: { label: "Piyada", capacity: "15 kq-dək", maxKg: 15, base: 2, perKm: 0.5, perStop: 1, perWaitMin: 0.1, perKg: 0.2 },
  car: { label: "Avtomobil", capacity: "200 kq-dək", maxKg: 200, base: 3, perKm: 0.8, perStop: 1.5, perWaitMin: 0.15, perKg: 0.15 },
  truck: { label: "Yük maşını", capacity: "1 500 kq-dək", maxKg: 1500, base: 15, perKm: 1.2, perStop: 3, perWaitMin: 0.3, perKg: 0.1 },
};

/** Fake distance estimate from two address strings (placeholder until a maps API is wired in). */
export function estimateKm(from: string, to: string): number {
  if (!from || !to) return 0;
  const seed = [...(from + to)].reduce((a, c) => a + c.charCodeAt(0), 0);
  return 3 + (seed % 22);
}

export interface PriceInput {
  vehicle: Vehicle;
  km: number;
  stops: number; // number of drop-off points
  weightKg: number;
  scheduled: boolean;
  extras: { doorToDoor: boolean; loaders: number; thermalBag: boolean; sms: boolean };
  declaredValue: number;
  promo?: string;
}

export function calcPrice(i: PriceInput) {
  const v = VEHICLES[i.vehicle];
  const freeKg = 5; // "İlk 5 kq daxildir" (Figma 08 · Tariflər)
  const lines: { label: string; amount: number }[] = [];
  lines.push({ label: "Başlanğıc tarif", amount: v.base });
  lines.push({ label: `Məsafə · ${num(i.km, 1)} km × ${num(v.perKm)} ₼`, amount: +(i.km * v.perKm).toFixed(2) });
  if (i.stops > 1) lines.push({ label: `Əlavə ünvanlar (${i.stops - 1})`, amount: (i.stops - 1) * v.perStop });
  if (i.weightKg > freeKg) lines.push({ label: "Əlavə çəki", amount: +((i.weightKg - freeKg) * v.perKg).toFixed(2) });
  if (i.extras.doorToDoor) lines.push({ label: "Qapıdan qapıya", amount: 1 });
  if (i.extras.loaders) lines.push({ label: `Yükdaşıyanlar (${i.extras.loaders})`, amount: i.extras.loaders * 8 });
  if (i.extras.thermalBag) lines.push({ label: "Termoçanta", amount: 1.5 });
  // Declared value up to 100 ₼ and recipient SMS are included in the price.
  if (i.declaredValue > 100) lines.push({ label: "Əlavə sığorta (0,5%)", amount: +((i.declaredValue - 100) * 0.005).toFixed(2) });
  let subtotal = lines.reduce((a, l) => a + l.amount, 0);
  if (i.scheduled) {
    const d = +(subtotal * 0.15).toFixed(2);
    lines.push({ label: "Planlı çatdırılma endirimi", amount: -d });
    subtotal -= d;
  }
  if (i.promo && i.promo.trim().toUpperCase() === "[CODE]") {
    const d = +(subtotal * 0.4).toFixed(2);
    lines.push({ label: "Promo kod −40%", amount: -d });
    subtotal -= d;
  }
  return { lines, total: Math.max(0, +subtotal.toFixed(2)) };
}

export const num = (n: number, digits = 2) => n.toFixed(digits).replace(".", ",");
export const azn = (n: number) => `${num(n)} ₼`;
