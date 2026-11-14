import Link from "next/link";
import { StoreBadges } from "./Illustrations";
import { Container } from "./ui";

const cols: { title: string; links: [string, string][] }[] = [
  { title: "Xidmətlər", links: [["Biznes üçün", "/business"], ["Mağazalar üçün", "/business"], ["Tariflər", "/pricing"], ["API", "#"], ["Kuryer ol", "/courier"]] },
  { title: "Şirkət", links: [["Haqqımızda", "#"], ["Rəylər", "#"], ["Bloq", "#"], ["Əlaqə", "#"], ["FAQ", "/#faq"]] },
  { title: "Faydalı", links: [["İstifadə şərtləri", "#"], ["Məxfilik siyasəti", "#"], ["App Store ↗", "#"], ["Google Play ↗", "#"]] },
];
const SOCIAL = ["Instagram", "Facebook", "Telegram", "WhatsApp", "TikTok"];
const MOBILE_COLS: [string, string][][] = [
  [["Biznes üçün", "/business"], ["Mağazalar üçün", "/business"], ["API", "#"], ["Tariflər", "/pricing"], ["Kuryer ol", "/courier"], ["FAQ", "/#faq"]],
  [["Haqqımızda", "#"], ["Rəylər", "#"], ["Bloq", "#"], ["Əlaqə", "#"], ["Şərtlər", "#"], ["Məxfilik", "#"]],
];

function SocialIcons() {
  return (
    <div className="flex gap-5">
      {SOCIAL.map((s) => (
        <a key={s} href="#" aria-label={s}>
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={`/figma/social/${s.toLowerCase()}.svg`} alt="" width={24} height={24} />
        </a>
      ))}
    </div>
  );
}

/** Figma "Footer / Desktop" (2:21006): background/dark, px 64, py 56, gap 32. */
export function Footer() {
  return (
    <footer className="bg-night text-white">
      {/* Mobile — Figma 2:20293: px 20, py 40, gap 24 */}
      <Container className="flex flex-col gap-6 py-10 leading-[1.4] lg:hidden">
        <Link href="/" className="text-[30px] font-bold">Yetir↗</Link>
        <p className="text-[15px] text-[#B9C5D7]">Azərbaycan boyu, qapıdan qapıya.</p>
        <p className="text-[15px] font-medium">Dəstək 24/7<br /><a href="tel:+994120000000">+994 12 000 00 00</a><br /><a href="mailto:support@yetir.az">support@yetir.az</a></p>
        <div className="grid grid-cols-2 gap-4 text-sm text-[#B9C5D7]">
          {MOBILE_COLS.map((col, i) => (
            <ul key={i}>{col.map(([l, href]) => <li key={l}><Link href={href} className="hover:text-white">{l}</Link></li>)}</ul>
          ))}
        </div>
        <p className="text-[13px] font-medium">Instagram / Facebook / Telegram<br />WhatsApp / TikTok</p>
        <p className="whitespace-pre text-sm font-medium">{"  App Store        ▷ Google Play"}</p>
        <StoreBadges className="flex-col gap-6" />
        <SocialIcons />
        <p className="text-sm text-[#B9C5D7]">© 2026 Yetir<br />[Hüquqi şəxsin adı] MMC<br />VÖEN: [əlavə ediləcək]</p>
      </Container>

      {/* Desktop — Figma 2:21006 */}
      <Container className="hidden flex-col gap-8 py-14 lg:flex">
        <div className="grid gap-10 leading-[1.4] md:grid-cols-2 lg:grid-cols-[390fr_267fr_267fr_267fr]">
          <div className="flex flex-col gap-4">
            <Link href="/" className="text-[32px] font-bold">Yetir↗</Link>
            <p className="text-[15px] text-[#B9C5D7]">Azərbaycan boyu, qapıdan qapıya.</p>
            <p className="text-[15px] font-medium">
              Dəstək: 24/7<br />
              <a href="tel:+994120000000">+994 12 000 00 00</a><br />
              <a href="mailto:support@yetir.az">support@yetir.az</a>
            </p>
          </div>
          {cols.map((c) => (
            <div key={c.title} className="flex flex-col gap-4">
              <h3 className="text-[17px] font-bold">{c.title}</h3>
              <ul className="text-sm text-[#B9C5D7]">
                {c.links.map(([l, href]) => <li key={l}><Link href={href} className="hover:text-white">{l}</Link></li>)}
              </ul>
            </div>
          ))}
        </div>
        <p className="whitespace-pre-wrap text-sm font-medium leading-[1.4]">
          {SOCIAL.map((s, i) => <span key={s}>{i > 0 && "   /   "}<a href="#" className="hover:text-amber-500">{s}</a></span>)}
        </p>
        {/* Copyright on the left, store badges on the right of the same row (changed on request from the frame's stacked layout). */}
        <div className="flex items-center justify-between gap-8">
          <p className="whitespace-pre-wrap text-xs leading-[1.4] text-[#B9C5D7]">
            {"© 2026 Yetir  ·  [Hüquqi şəxsin adı] MMC  ·  VÖEN: [əlavə ediləcək]"}<br />
            Nümunə əlaqə məlumatları. Xidmət şərtləri və şəhər üzrə mövcudluq təsdiqlənməlidir.
          </p>
          <StoreBadges className="shrink-0 flex-nowrap" />
        </div>
        <div className="flex gap-5">
          {SOCIAL.map((s) => (
            <a key={s} href="#" aria-label={s}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={`/figma/social/${s.toLowerCase()}.svg`} alt="" width={24} height={24} />
            </a>
          ))}
        </div>
      </Container>
    </footer>
  );
}
