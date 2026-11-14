import Link from "next/link";
import clsx from "clsx";
import type { ReactNode } from "react";
import { LeadForm } from "./LeadForm";
import { Container, LinkButton } from "./ui";

/* Section building blocks shared by several Figma frames (home, business, …).
   Desktop (≥ lg) follows the 1440 frames; below lg the 390 mobile frames (px 20, py 40, gap 24). */

/** Mobile-only / desktop-only wrappers for places where the frames use different copy or structure. */
export const Mob = ({ children, className }: { children: ReactNode; className?: string }) => <div className={clsx("contents lg:hidden", className)}>{children}</div>;
export const Desk = ({ children, className }: { children: ReactNode; className?: string }) => <div className={clsx("hidden lg:contents", className)}>{children}</div>;

export function Band({ tone = "white", className, children, id }: { tone?: "white" | "subtle" | "brand" | "blue"; className?: string; children: ReactNode; id?: string }) {
  const bg = { white: "bg-card", subtle: "bg-surface", brand: "bg-brand-surface", blue: "bg-brand-500" }[tone];
  return (
    <section id={id} className={bg}>
      <Container className={clsx("flex flex-col gap-6 py-10 lg:gap-8", className)}>{children}</Container>
    </section>
  );
}
export const Eyebrow = ({ children, className }: { children: ReactNode; className?: string }) => (
  <p className={clsx("text-xs font-bold leading-[1.4] text-link", className)}>{children}</p>
);
/** Section heading: 30px (mobile "Section / 30") → 40px (desktop "Heading / 40"). */
export const H2 = ({ children, className }: { children: ReactNode; className?: string }) => (
  <h2 className={clsx("text-[30px] font-bold leading-[1.4] text-ink lg:text-[40px]", className)}>{children}</h2>
);

/** Figma "Business lead" (home 2:20218 / mobile 2:20281, business 2:20247). */
export function BusinessLeadSection() {
  return (
    <Band className="lg:py-16">
      <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-14">
        <div className="flex flex-col items-start gap-6 lg:w-[600px] lg:min-w-0 lg:shrink lg:gap-4">
          <Desk><Eyebrow className="leading-[1.35]">BİZNESİNİZİN SÜRƏTİNƏ UYĞUN</Eyebrow></Desk>
          <h2 className="text-[30px] font-bold leading-[1.4] text-ink lg:text-[42px] lg:leading-[1.35]">Daim göndərirsiniz?<br />Birlikdə böyüyək.</h2>
          <Mob><p className="text-base text-muted">Ayda 30 çatdırılmadan xüsusi tarif. Şəxsi menecer, sonradan ödəniş və artırılmış bəyan edilmiş dəyər.</p></Mob>
          <Desk>
            <p className="text-lg leading-[1.35] text-muted">Ayda 30 çatdırılmadan başlayaraq biznesinizə özəl tariflər.</p>
            {["Şəxsi menecer", "Sonradan ödəniş və vahid hesab-faktura", "Artırılmış bəyan edilmiş dəyər"].map((x) => (
              <p key={x} className="whitespace-pre text-base font-medium leading-[1.35] text-ink">{`✓  ${x}`}</p>
            ))}
            <LinkButton href="/order" variant="soft" size="lg" className="w-full max-w-[310px]">Birdəfəlik sifariş et&nbsp;&nbsp;↗</LinkButton>
          </Desk>
        </div>
        <LeadForm className="lg:w-[656px] lg:min-w-0 lg:shrink" />
        <Mob><Link href="/order" className="whitespace-pre text-[15px] font-semibold leading-[1.4] text-link">{"Birdəfəlik sifariş et  ↗"}</Link></Mob>
      </div>
    </Band>
  );
}

const INTEGRATIONS = [
  { mark: "{ }", t: "API inteqrasiyası", d: "Sifarişləri və statusları mövcud sisteminizdən idarə edin.", m: "Sifarişləri sisteminizdən idarə edin.", l: "API sənədləri", href: "#" },
  { mark: "↔", t: "E-ticarət plaginləri", d: "WooCommerce, OpenCart, Shopify və fərdi həllər.", m: "WooCommerce, OpenCart, Shopify və fərdi həllər.", l: "İnteqrasiyaları gör", href: "#" },
  { mark: "↗", t: "Sayt və tətbiq", d: "İstənilən cihazdan, əlavə quraşdırma olmadan sifariş.", m: "İstənilən cihazdan sifariş verin.", l: "Sifariş yarat", href: "/order" },
];

/** Figma "Integrations" (home 2:20224 / mobile 2:20287, business 2:20246). */
export function IntegrationsSection() {
  return (
    <Band className="leading-[1.4] lg:py-[72px]">
      <H2>Sizin sisteminizlə<br className="lg:hidden" /> bir addımda.</H2>
      <div className="grid gap-6 lg:grid-cols-3 lg:items-start lg:gap-5">
        {INTEGRATIONS.map((x) => (
          <div key={x.t} className="flex flex-col gap-4 rounded-[20px] bg-surface p-5 lg:rounded-3xl lg:p-6">
            <p className="hidden text-[30px] font-bold text-link lg:block">{x.mark}</p>
            <h3 className="text-[23px] font-bold text-ink">{x.t}</h3>
            <p className="text-[15px] text-muted lg:text-base"><span className="lg:hidden">{x.m}</span><span className="hidden lg:inline">{x.d}</span></p>
            <Link href={x.href} className="text-sm font-semibold text-link">{x.l}&nbsp;&nbsp;↗</Link>
          </div>
        ))}
      </div>
    </Band>
  );
}
