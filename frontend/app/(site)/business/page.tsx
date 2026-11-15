import clsx from "clsx";
import { Band, BusinessLeadSection, H2, IntegrationsSection } from "@/components/sections";
import { Container, LinkButton } from "@/components/ui";

/* Figma frame "05 · Biznes üçün" (2:20241). */

const TIERS = [
  { volume: "<30 çatdırılma / ay", name: "Başlanğıc", price: "Standart tarif", note: "Müqaviləsiz birdəfəlik göndərişlər", featured: false },
  { volume: "30–300 çatdırılma / ay", name: "Böyüyən biznes", price: "−10%-dək", note: "Şəxsi menecer və sonradan ödəniş", featured: true },
  { volume: "300+ çatdırılma / ay", name: "Fərdi həll", price: "Xüsusi təklif", note: "API, SLA və xüsusi əməliyyat dəstəyi", featured: false },
];
const CASES = [
  ["ALMA market", "−28%", "Əməliyyat vaxtına qənaət", "Gündəlik sifarişləri API ilə avtomatik ötürən yerli market."],
  ["BLOOM", "96%", "Vaxtında çatdırılma", "Bayram günlərində də hər ünvana vaxtında çatan gül mağazası."],
  ["urban.store", "2×", "Daha çox göndəriş", "Sınaq və geri qaytarma xidməti ilə daha rahat alış-veriş."],
];
const INCLUDED = ["Elektron müqavilə və hesab-faktura", "Ay sonunda vahid ödəniş", "Artırılmış bəyan edilmiş dəyər", "API və komanda üzvləri", "Nağd ödənişin toplanması"];

export default function BusinessPage() {
  return (
    <>
      {/* Business hero 2:20243 */}
      <section className="bg-night">
        <Container className="flex flex-col gap-6 py-[72px] leading-[1.4] lg:flex-row lg:items-start">
          <div className="flex flex-col items-start gap-4 lg:w-[720px] lg:min-w-0 lg:shrink">
            <p className="text-xs font-bold text-[#FFB800]">Kuryer.net DEYİL — BİZNESİNİZİN BİR HİSSƏSİ</p>
            <h1 className="text-[40px] font-bold text-white md:text-[56px]">Çatdırılmanı bizə,<br />böyüməyi özünüzə<br />həvalə edin.</h1>
            <p className="text-lg text-[#C6D3E5]">Onlayn mağazalardan böyük şirkətlərə. Bütün Azərbaycan üzrə bir müqavilə, bir hesab, etibarlı logistika.</p>
            <LinkButton href="#lead" variant="accent" size="lg" className="w-full max-w-[300px]">Biznes təklifi al&nbsp;&nbsp;↗</LinkButton>
            <p className="whitespace-pre-wrap text-sm font-medium text-white">{"30+ çatdırılma / ay  ·  Fərdi tarif  ·  Şəxsi menecer"}</p>
          </div>
          <div className="flex flex-col gap-5 rounded-3xl bg-[#1B2C46] p-8 text-white lg:w-[568px] lg:min-w-0 lg:shrink">
            <h2 className="text-[26px] font-bold">Biznes hesabınıza daxildir</h2>
            {INCLUDED.map((x) => <p key={x} className="whitespace-pre-wrap text-[17px] font-medium">{`✓  ${x}`}</p>)}
          </div>
        </Container>
      </section>

      {/* Volume pricing 2:20244 */}
      <Band tone="subtle" className="py-16 leading-[1.4]">
        <H2>Həcm artır. Tarif azalır.</H2>
        <p className="text-[15px] text-muted">Aşağıdakı endirimlər nümunədir. Dəqiq təklif marşrut və həcmə görə hazırlanır.</p>
        <div className="grid gap-6 md:grid-cols-3 md:items-start">
          {TIERS.map((t) => (
            <div key={t.name} className={clsx("flex flex-col gap-5 rounded-3xl p-7", t.featured ? "bg-brand-surface" : "bg-card")}>
              <p className="text-sm font-semibold text-link">{t.volume}</p>
              <h3 className="text-[25px] font-bold text-ink">{t.name}</h3>
              <p className="text-[32px] font-bold text-ink">{t.price}</p>
              <p className="text-[15px] text-muted">{t.note}</p>
              <LinkButton href="#lead" variant={t.featured ? "accent" : "soft"} size="lg" className="w-full">Təklif al&nbsp;&nbsp;↗</LinkButton>
            </div>
          ))}
        </div>
      </Band>

      {/* Case studies 2:20245 */}
      <Band className="py-16 leading-[1.4]">
        <H2>Hər biznesə uyğun çatdırılma.</H2>
        <p className="text-[13px] text-muted">Nümunə müştəri hekayələri · təqdimat məqsədli göstəricilər</p>
        <div className="grid gap-6 md:grid-cols-3 md:items-start">
          {CASES.map(([brand, metric, title, text]) => (
            <div key={brand} className="flex flex-col gap-5 rounded-3xl bg-surface p-6">
              <p className="text-[23px] font-bold text-muted">{brand}</p>
              <p className="text-[52px] font-bold text-link">{metric}</p>
              <h3 className="text-xl font-semibold text-ink">{title}</h3>
              <p className="text-[15px] text-muted">{text}</p>
            </div>
          ))}
        </div>
      </Band>

      <IntegrationsSection />
      <div id="lead" className="scroll-mt-[112px] lg:scroll-mt-[94px]">
        <BusinessLeadSection />
      </div>
    </>
  );
}
