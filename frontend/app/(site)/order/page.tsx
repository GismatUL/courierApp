"use client";
import { useMemo, useState, type ReactNode } from "react";
import Link from "next/link";
import clsx from "clsx";
import { CheckCircle2 } from "lucide-react";
import { BoxField } from "@/components/PriceCalculator";
import { BakuMap } from "@/components/BakuMap";
import { OrderMobile } from "@/components/OrderMobile";
import { Container, FigmaIcon, btn } from "@/components/ui";
import { calcPrice, estimateKm, azn, num, VEHICLES, type Vehicle } from "@/lib/pricing";

/* Figma frame "02 · Sifariş yarat" (2:20231). Sample values from the design are used as placeholders. */

type Point = { address: string; window: string; name: string; phone: string; notes: string; entry: string; cash: boolean; cashAmount: string };
const emptyPoint = (): Point => ({ address: "", window: "", name: "", phone: "", notes: "", entry: "", cash: false, cashAmount: "" });
const SAMPLE = [
  { address: "Nizami küçəsi 203, Bakı", short: "Nizami küç. 203", window: "Bu gün, 14:00–14:15", name: "Rəşad Əliyev", phone: "+994 50 123 45 67", cash: "Kuryer məhsulun pulunu ödəsin" },
  { address: "Mərkəzi Bulvar küçəsi 5, Ağ Şəhər", short: "Ağ Şəhər", window: "Bu gün, 14:30–15:00", name: "Aysel Məmmədova", phone: "+994 55 765 43 21", cash: "Alıcıdan nağd ödəniş toplansın" },
];
const SAMPLE_KM = 5.5;
const EXTRAS = [
  { key: "doorToDoor", label: "Qapıdan qapıya", price: "+1,00 ₼" },
  { key: "loaders", label: "Yükdaşıyan köməkçi", price: "+8,00 ₼-dən" },
  { key: "thermalBag", label: "Termoçanta", price: "+1,50 ₼" },
  { key: "sms", label: "Alıcıya SMS bildiriş", price: "Pulsuz" },
] as const;
const PAYMENTS = [["cash", "Nağd"], ["card", "Bank kartı"], ["balance", "Şirkət balansı"]] as const;

const input = "w-full bg-transparent text-[15px] font-semibold text-ink outline-none placeholder:text-ink";

/** White step card: radius 24, p 24, gap 20, title 24/700. */
function StepCard({ title, children }: { title: string; children: ReactNode }) {
  return (
    <section className="flex flex-col gap-5 rounded-3xl bg-card p-6 leading-[1.4]">
      <h2 className="whitespace-pre text-2xl font-bold text-ink">{title}</h2>
      {children}
    </section>
  );
}

function PointCard({ index, point, onChange, onRemove }: { index: number; point: Point; onChange: (p: Partial<Point>) => void; onRemove?: () => void }) {
  const pickup = index === 0;
  const s = SAMPLE[pickup ? 0 : 1];
  return (
    <div className="flex flex-col gap-5 rounded-3xl bg-surface p-5">
      <div className="flex items-start gap-3">
        <span className={clsx("w-11 shrink-0 rounded-4xl p-[14px] text-center text-[15px] font-semibold", pickup ? "bg-brand-500 text-white" : "bg-amber-500 text-night")}>
          {pickup ? "A" : String.fromCharCode(65 + index)}
        </span>
        <h3 className="flex-1 text-xl font-bold text-ink">{pickup ? "Götürülmə nöqtəsi" : "Çatdırılma nöqtəsi"}</h3>
        {onRemove && <button type="button" onClick={onRemove} className="text-sm font-semibold text-muted hover:text-rose-600">Sil</button>}
      </div>
      <BoxField label="Ünvan · xəritədə seç  ↗" normalCase className="[&>span]:whitespace-pre">
        <input className={input} value={point.address} onChange={(e) => onChange({ address: e.target.value })} placeholder={s.address} />
      </BoxField>
      <div className="grid gap-x-4 gap-y-5 sm:grid-cols-2">
        <BoxField label="Gün və vaxt aralığı" normalCase><input className={input} value={point.window} onChange={(e) => onChange({ window: e.target.value })} placeholder={s.window} /></BoxField>
        <BoxField label="Əlaqədar şəxs" normalCase><input className={input} value={point.name} onChange={(e) => onChange({ name: e.target.value })} placeholder={s.name} /></BoxField>
        <BoxField label="Telefon nömrəsi" normalCase><input className={input} type="tel" value={point.phone} onChange={(e) => onChange({ phone: e.target.value })} placeholder={s.phone} /></BoxField>
        <BoxField label="Giriş, mərtəbə, mənzil" normalCase><input className={input} value={point.entry} onChange={(e) => onChange({ entry: e.target.value })} placeholder="2-ci giriş, 3-cü mərtəbə, mənzil 12" /></BoxField>
      </div>
      <BoxField label="Kuryer üçün qeyd" normalCase><input className={input} value={point.notes} onChange={(e) => onChange({ notes: e.target.value })} placeholder="Gəlməzdən 5 dəqiqə əvvəl zəng edin" /></BoxField>
      <div className="flex flex-wrap items-center whitespace-pre text-sm font-medium text-ink">
        <button type="button" role="checkbox" aria-checked={point.cash} onClick={() => onChange({ cash: !point.cash })}>
          {`${point.cash ? "●" : "○"}  ${s.cash}`}
        </button>
        {"     Məbləğ: "}
        <input
          aria-label="Məbləğ"
          inputMode="decimal"
          disabled={!point.cash}
          value={point.cashAmount}
          onChange={(e) => onChange({ cashAmount: e.target.value })}
          placeholder="0,00"
          className="w-12 bg-transparent font-medium text-ink outline-none placeholder:text-ink disabled:cursor-not-allowed"
        />
        ₼
      </div>
    </div>
  );
}

export default function OrderPage() {
  const [vehicle, setVehicle] = useState<Vehicle>("car");
  const [mode, setMode] = useState<"fast" | "scheduled">("fast");
  const [points, setPoints] = useState<Point[]>([emptyPoint(), emptyPoint()]);
  const [weight, setWeight] = useState(5);
  const [what, setWhat] = useState("");
  const [declared, setDeclared] = useState("");
  const [extras, setExtras] = useState({ doorToDoor: true, loaders: 0, thermalBag: false, sms: true });
  const [payment, setPayment] = useState<"cash" | "card" | "balance">("cash");
  const [promo, setPromo] = useState("");
  const [appliedPromo, setAppliedPromo] = useState("");
  const [placed, setPlaced] = useState(false);

  const km = useMemo(() => {
    let k = 0;
    for (let i = 1; i < points.length; i++) k += estimateKm(points[i - 1].address, points[i].address);
    return k || SAMPLE_KM;
  }, [points]);
  const declaredValue = parseFloat(declared.replace(",", ".")) || 100;
  const price = calcPrice({ vehicle, km, stops: points.length - 1, weightKg: weight, scheduled: mode === "scheduled", extras, declaredValue, promo: appliedPromo });
  const update = (i: number, p: Partial<Point>) => setPoints((ps) => ps.map((x, j) => (j === i ? { ...x, ...p } : x)));
  const maxKg = VEHICLES[vehicle].maxKg;
  const from = points[0].address.split(",")[0] || SAMPLE[0].short;
  const to = points[points.length - 1].address.split(",")[0] || SAMPLE[1].short;

  if (placed)
    return (
      <Container className="py-20">
        <div className="mx-auto max-w-lg rounded-3xl bg-card p-8 text-center">
          <CheckCircle2 className="mx-auto text-emerald-500" size={56} />
          <h1 className="mt-4 text-[30px] font-bold leading-[1.4]">Sifariş qəbul edildi</h1>
          <p className="mt-2 text-muted">Kuryer axtarılır. Statusu canlı izləyə bilərsiniz.</p>
          <Link href="/tracking" className={btn("accent", "lg", "mt-6 w-full")}>Sifarişi izlə&nbsp;&nbsp;→</Link>
        </div>
      </Container>
    );

  return (
    <>
    <OrderMobile
      vehicle={vehicle} setVehicle={(v) => { setVehicle(v); setWeight((w) => Math.min(w, VEHICLES[v].maxKg)); }}
      mode={mode} setMode={setMode}
      points={points} update={update} addPoint={() => setPoints((ps) => [...ps, emptyPoint()])} removePoint={(i) => setPoints((ps) => ps.filter((_, j) => j !== i))}
      weight={weight} setWeight={setWeight} maxKg={maxKg}
      what={what} setWhat={setWhat} declared={declared} setDeclared={setDeclared}
      extras={extras} toggleExtra={(k) => setExtras((e) => (k === "loaders" ? { ...e, loaders: e.loaders ? 0 : 1 } : { ...e, [k]: !e[k] }))}
      payment={payment} setPayment={setPayment}
      promo={promo} setPromo={setPromo} applyPromo={() => setAppliedPromo(promo)}
      km={km} lines={price.lines} total={price.total} from={from} to={to}
      onSubmit={() => setPlaced(true)}
    />
    <div className="hidden bg-surface lg:block">
      <Container className="flex flex-col gap-7 pb-16 pt-10 leading-[1.4]">
        <p className="whitespace-pre text-[13px] font-medium text-muted">
          <Link href="/" className="hover:text-ink">Ana səhifə</Link>{"  /  "}Yeni sifariş
        </p>
        <div className="flex flex-col gap-4 md:flex-row md:items-start">
          <h1 className="text-[32px] font-bold text-ink md:w-[850px] md:shrink-0 md:text-[40px]">Yeni çatdırılma sifarişi</h1>
          <a href="tel:+994120000000" className="whitespace-pre text-sm font-semibold text-link">{"Sualınız var?  24/7 dəstək  ↗"}</a>
        </div>
        <ol className="grid gap-4 md:grid-cols-3 md:items-start">
          {["Marşrut və bağlama", "Ödəniş", "Təsdiq"].map((s, i) => (
            <li key={s} className={clsx("whitespace-pre rounded-3xl p-4 text-[15px] font-semibold", i === 0 ? "bg-brand-surface text-link" : "bg-card text-muted")} aria-current={i === 0 ? "step" : undefined}>
              {`${i + 1}  ${s}`}
            </li>
          ))}
        </ol>

        <form
          onSubmit={(e) => { e.preventDefault(); setPlaced(true); }}
          className="flex flex-col gap-8 lg:flex-row lg:items-start"
        >
          <div className="flex min-w-0 flex-col gap-6 lg:w-[840px] lg:min-w-0 lg:shrink">
            {/* 01 · Transport and timing 2:21271 */}
            <StepCard title="01  Nə ilə və nə vaxt?">
              <div className="grid gap-3 sm:grid-cols-3">
                {(Object.keys(VEHICLES) as Vehicle[]).map((v) => (
                  <button
                    type="button"
                    key={v}
                    aria-pressed={vehicle === v}
                    onClick={() => { setVehicle(v); setWeight((w) => Math.min(w, VEHICLES[v].maxKg)); }}
                    className={clsx("flex flex-col items-start gap-3 rounded-xl border p-5 text-left", vehicle === v ? "border-brand-500 bg-brand-surface" : "border-line bg-card")}
                  >
                    <FigmaIcon name={v} />
                    <span className="text-[15px] font-semibold text-ink">{VEHICLES[v].label}</span>
                    <span className="text-[13px] text-muted">{VEHICLES[v].capacity}</span>
                  </button>
                ))}
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                {([["fast", "Sürətli", "İndi · təxmini 30–60 dəqiqə"], ["scheduled", "Planlı", "4 saatlıq interval · daha sərfəli"]] as const).map(([m, t, d]) => (
                  <button
                    type="button"
                    key={m}
                    aria-pressed={mode === m}
                    onClick={() => setMode(m)}
                    className={clsx("flex flex-col items-start gap-5 rounded-3xl p-5 text-left", mode === m ? "bg-brand-surface" : "bg-surface")}
                  >
                    <span className={clsx("whitespace-pre text-lg font-semibold", mode === m ? "text-link" : "text-ink")}>{`${mode === m ? "●" : "○"}  ${t}`}</span>
                    <span className="text-sm text-muted">{d}</span>
                  </button>
                ))}
              </div>
              <p className="text-[13px] text-muted">Planlı vaxt nümunəsi: 15 noyabr, 10:00–14:00</p>
            </StepCard>

            {/* 02 · Route details 2:21294 */}
            <StepCard title="02  Marşrut">
              <p className="text-sm text-muted">Bir götürülmə nöqtəsi, istədiyiniz qədər çatdırılma ünvanı.</p>
              {points.map((p, i) => (
                <PointCard key={i} index={i} point={p} onChange={(x) => update(i, x)} onRemove={i > 1 ? () => setPoints((ps) => ps.filter((_, j) => j !== i)) : undefined} />
              ))}
              <button type="button" onClick={() => setPoints((ps) => [...ps, emptyPoint()])} className={btn("soft", "lg", "w-full whitespace-pre")}>
                {"+  Çatdırılma ünvanı əlavə et"}
              </button>
              <p className="text-[13px] text-muted">Əlavə dayanacaqların sayı məhdud deyil. Qiymət marşruta görə yenilənir.</p>
            </StepCard>

            {/* 03 · Parcel and insurance 2:21354 */}
            <StepCard title="03  Nə göndərirsiniz?">
              <div className="grid gap-4 sm:grid-cols-[480fr_296fr]">
                <BoxField label="Bağlamanın tərkibi" normalCase><input className={input} value={what} onChange={(e) => setWhat(e.target.value)} placeholder="Geyim · 1 bağlama" /></BoxField>
                <BoxField label="Bəyan edilmiş dəyər" normalCase><input className={input} inputMode="decimal" value={declared} onChange={(e) => setDeclared(e.target.value)} placeholder="100,00 ₼" /></BoxField>
              </div>
              <label htmlFor="weight" className="whitespace-pre text-base font-semibold text-ink">
                {"Bağlamanın çəkisi                                                  "}{weight} kq
              </label>
              <input
                id="weight"
                type="range"
                min={1}
                max={maxKg}
                value={weight}
                onChange={(e) => setWeight(+e.target.value)}
                className="figma-range"
                style={{ ["--pct" as string]: `${((weight - 1) / (maxKg - 1)) * 100}%` }}
              />
              <p className="whitespace-pre text-xs text-muted">{`1 kq                                                                                           ${maxKg} kq`}</p>
              <a href="#" className="whitespace-pre text-[13px] font-medium text-link">{"✓  100 ₼-dək bəyan edilmiş dəyər daxildir. Sığorta şərtləri  ↗"}</a>
            </StepCard>

            {/* 04 · Extras 2:21368 */}
            <StepCard title="04  Əlavə xidmətlər">
              {EXTRAS.map((x) => {
                const on = x.key === "loaders" ? extras.loaders > 0 : extras[x.key];
                const toggle = () => setExtras((e) => (x.key === "loaders" ? { ...e, loaders: e.loaders ? 0 : 1 } : { ...e, [x.key]: !e[x.key] }));
                return (
                  <label key={x.key} className="flex cursor-pointer items-center gap-3 text-sm">
                    <span className="flex flex-1 items-center gap-2 text-muted">
                      <input type="checkbox" checked={on} onChange={toggle} className="h-3.5 w-3.5 accent-muted" />
                      {x.label}
                    </span>
                    <span className="w-[110px] text-right font-semibold text-ink">{x.price}</span>
                  </label>
                );
              })}
            </StepCard>

            {/* 05 · Payment 2:21390 */}
            <StepCard title="05  Ödəniş">
              <div className="grid gap-3 sm:grid-cols-3" role="radiogroup" aria-label="Ödəniş üsulu">
                {PAYMENTS.map(([k, l]) => (
                  <button
                    type="button"
                    role="radio"
                    aria-checked={payment === k}
                    key={k}
                    onClick={() => setPayment(k)}
                    className={clsx("whitespace-pre rounded-3xl p-[18px] text-left text-[15px] font-semibold", payment === k ? "bg-brand-surface text-link" : "bg-surface text-ink")}
                  >
                    {`${payment === k ? "●" : "○"}  ${l}`}
                  </button>
                ))}
              </div>
              <p className="whitespace-pre text-sm text-muted">Ödənişi göndərən edir. <button type="button" className="hover:text-ink">{"Ödəyicini dəyiş  ↗"}</button></p>
              <div className="grid gap-4 sm:grid-cols-[556fr_220fr]">
                <BoxField label="Promokod" normalCase><input className={input} value={promo} onChange={(e) => setPromo(e.target.value)} placeholder="Promokodu daxil edin" /></BoxField>
                <button type="button" onClick={() => setAppliedPromo(promo)} className={btn("soft", "lg", "self-start")}>Tətbiq et</button>
              </div>
              <p className="text-[13px] text-muted">Sifariş verərək istifadə şərtləri və məxfilik siyasəti ilə razılaşırsınız.</p>
            </StepCard>
          </div>

          {/* Sticky price summary 2:21270 */}
          <aside className="flex flex-col gap-5 lg:sticky lg:top-[118px] lg:w-[440px] lg:min-w-0 lg:shrink lg:self-start">
            <div className="flex flex-col gap-5 rounded-3xl bg-card p-6">
              <h2 className="text-2xl font-bold text-ink">Sifarişiniz</h2>
              <BakuMap width={392} className="max-w-full" />
              <p className="whitespace-pre text-[15px] font-semibold text-ink">{`${from}  →  ${to}`}</p>
              <p className="whitespace-pre text-[13px] text-muted">{`${num(km, 1)} km  ·  ${VEHICLES[vehicle].label}  ·  1 bağlama`}</p>
              <span className="w-[190px] rounded-xl bg-brand-surface p-2.5 text-[13px] font-semibold text-link">
                {mode === "fast" ? "Sürətli · 30–60 dəq" : "Planlı · 4 saat"}
              </span>
              {price.lines.map((l) => (
                <div key={l.label} className="flex gap-3 text-sm">
                  <span className="flex-1 text-muted">{l.label}</span>
                  <span className="w-[110px] text-right font-semibold text-ink">{azn(l.amount)}</span>
                </div>
              ))}
              <div className="flex gap-3 text-sm">
                <span className="flex-1 text-muted">Sığorta və SMS</span>
                <span className="w-[110px] text-right font-semibold text-ink">Daxildir</span>
              </div>
              <div className="flex gap-3 text-sm font-bold text-ink">
                <span className="flex-1">Cəmi</span>
                <span className="w-[110px] text-right">{azn(price.total)}</span>
              </div>
              <p className="text-[42px] font-bold text-ink">{azn(price.total)}</p>
              <button type="submit" className={btn("accent", "lg", "w-full whitespace-pre")}>{`Sifariş et · ${azn(price.total)}  →`}</button>
              <p className="text-xs text-muted">Qiymət ünvan və xidmətlərə görə yenilənir.<br />Nümunə hesablama.</p>
            </div>
            <div className="flex flex-col gap-5 rounded-3xl bg-brand-surface p-6">
              <p className="whitespace-pre text-[17px] font-semibold text-link">{"✓  Təhlükəsiz çatdırılma"}</p>
              <p className="text-sm text-muted">Kuryer məlumatları sifariş təsdiqindən sonra görünəcək. Bağlamanı canlı izləyə biləcəksiniz.</p>
            </div>
          </aside>
        </form>
      </Container>
    </div>
    </>
  );
}
