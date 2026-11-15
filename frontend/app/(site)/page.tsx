import Link from "next/link";
import clsx from "clsx";
import { CourierPhoto, HeroBlock } from "@/components/HeroBlock";
import { Faq } from "@/components/Faq";
import { StoreBadges } from "@/components/Illustrations";
import { PhoneTracking, PhoneTrackingMobile } from "@/components/PhoneTracking";
import { FigmaIcon, LinkButton } from "@/components/ui";
import { Band, BusinessLeadSection, Desk, Eyebrow, H2, IntegrationsSection, Mob } from "@/components/sections";
import { FAQ, FAQ_MOBILE } from "@/lib/data";

/* Figma frames "01 · Ana səhifə" (2:20213, desktop ≥ lg) and "11 · Mobil / Ana səhifə" (2:20276, < lg).
   Each section notes both node ids. */

// [icon, desktop title, desktop text, mobile title, mobile text]
const CATEGORIES = [
  ["box", "Onlayn mağazalar", "Qapıda ödənişin qəbulu", "Onlayn mağazalar", "Qapıda ödəniş"],
  ["bag", "Geyim", "Sınaq və geri qaytarma", "Geyim", "Sınaq və qaytarma"],
  ["food", "Restoranlar", "İsti və vaxtında", "Restoranlar", "İsti və vaxtında"],
  ["doc", "Sənədlər", "İmzadan imzaya", "Sənədlər", "İmzadan imzaya"],
  ["bag", "Ərzaq", "Termoçanta ilə", "Ərzaq", "Termoçanta ilə"],
  ["flower", "Güllər və hədiyyələr", "Xüsusi anlara qayğı", "Güllər və hədiyyələr", "Xüsusi anlar üçün"],
  ["shield", "Aptek məhsulları", "Diqqətli daşınma", "Aptek", "Diqqətli daşınma"],
  ["box", "Mebel və iri əşyalar", "1 500 kq-dək yüklər", "Mebel və iri əşyalar", "1 500 kq-dək"],
  ["box", "Başqa bir şey?", "Sizin üçün həll tapaq", "Başqa bir şey?", "Bir həll tapaq"],
];
const CLIENTS = [
  ["ALMA market", "kitab.az", "BLOOM", "evim", "urban.store"],
  ["local coffee", "NOVA shop", "flora", "Bakı print", "freshbox"],
];
const CLIENTS_MOBILE = [["ALMA market", "kitab.az"], ["BLOOM", "urban.store"]];
// [icon, title, desktop text, mobile text]
const BENEFITS = [
  ["money", "Ədalətli qiymət", "Yalnız məsafə, çəki və seçdiyiniz xidmət üçün ödəyin. Gizli ödəniş yoxdur.", "Məsafə, çəki və seçdiyiniz xidmət üçün ödəyin. Gizli ödəniş yoxdur."],
  ["clock", "24/7 yanınızdayıq", "Sifarişdən təhvilədək dəstək komandamız hər zaman əlçatandır.", "Sifarişdən təhvilədək hər zaman əlçatan dəstək."],
  ["shield", "Bağlamanız qorunur", "Bəyan edilmiş dəyərə uyğun sığorta və şəffaf kompensasiya şərtləri.", "Bəyan edilmiş dəyərə uyğun sığorta və şəffaf kompensasiya."],
];
const PAYMENTS = ["₼  Nağd ödəniş", "▰  Bank kartı", "↔  Şirkətlər üçün köçürmə"];
const CITY_LINKS = ["Bakı", "Sumqayıt", "Gəncə", "Mingəçevir", "Şirvan", "Lənkəran", "Şəki", "Quba", "Naxçıvan", "Xırdalan", "Qəbələ"];

export default function Home() {
  return (
    <>
      <HeroBlock />

      {/* Delivery categories 2:20216 / 2:20279 */}
      <Band className="lg:py-[72px]">
        <H2>Siz göndərin.<br className="lg:hidden" /> Biz çatdıraq.</H2>
        <Desk><p className="text-[17px] leading-[1.35] text-muted">Hər bağlama üçün uyğun kuryer və nəqliyyat.</p></Desk>
        <div className="grid grid-cols-2 gap-x-4 gap-y-6 lg:grid-cols-3 lg:items-start lg:gap-x-5 lg:gap-y-8">
          {CATEGORIES.map(([icon, t, d, mt, md]) => (
            <div key={t} className="flex flex-col gap-4 rounded-[20px] bg-surface p-4 leading-[1.4] lg:flex-row lg:rounded-3xl lg:p-6">
              <FigmaIcon name={icon} />
              <div className="flex flex-col gap-4 lg:leading-[1.35]">
                <h3 className="text-base font-semibold text-ink lg:text-xl"><span className="lg:hidden">{mt}</span><span className="hidden lg:inline">{t}</span></h3>
                <p className="text-xs text-muted lg:text-sm"><span className="lg:hidden">{md}</span><span className="hidden lg:inline">{d}</span></p>
              </div>
            </div>
          ))}
        </div>
      </Band>

      {/* Social proof 2:20217 / 2:20280 — logo rows scroll in opposite directions on desktop */}
      <section className="overflow-hidden bg-surface">
        <div className="mx-auto flex max-w-page flex-col gap-6 px-5 py-10 leading-[1.4] lg:gap-8 lg:px-16 lg:py-14">
          <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-10">
            <p className="text-[52px] font-bold text-link lg:w-[500px] lg:min-w-0 lg:shrink lg:text-[72px] lg:leading-[1.35]">50 000+</p>
            <Mob>
              <p className="text-[21px] font-semibold text-ink">çatdırılma / ay<br />Azərbaycanın hər yerində</p>
              <p className="text-xs text-muted">Nümunə göstərici və tərəfdaşlar</p>
            </Mob>
            <Desk>
              <div className="flex flex-col gap-4">
                <p className="text-[30px] font-bold text-ink">çatdırılma / ay</p>
                <p className="text-sm leading-[1.35] text-muted">Azərbaycanın hər yerində bizneslər üçün.<br />Nümunə göstərici və tərəfdaş adları.</p>
              </div>
            </Desk>
          </div>
          <Mob>
            {CLIENTS_MOBILE.map((row) => (
              <div key={row[0]} className="grid grid-cols-2 gap-4">
                {row.map((l) => <div key={l} className="rounded-[20px] bg-card p-[14px] text-[19px] font-bold text-muted">{l}</div>)}
              </div>
            ))}
          </Mob>
          <Desk>
            {CLIENTS.map((row, r) => (
              <div key={r} className="overflow-hidden">
                <div className={clsx("flex w-max gap-6 hover:[animation-play-state:paused]", r === 0 ? "animate-marquee" : "animate-marquee-rev")}>
                  {[...row, ...row, ...row, ...row].map((l, i) => (
                    <div key={i} aria-hidden={i >= row.length} className="w-[243px] shrink-0 rounded-3xl bg-card p-6 text-[23px] font-bold leading-[1.35] text-muted">{l}</div>
                  ))}
                </div>
              </div>
            ))}
          </Desk>
        </div>
      </section>

      <BusinessLeadSection />

      {/* Benefits 2:20219 / 2:20282 */}
      <Band tone="subtle" className="lg:py-16">
        <div className="grid gap-6 lg:grid-cols-3 lg:items-start lg:gap-5">
          {BENEFITS.map(([icon, t, d, m]) => (
            <div key={t} className="flex flex-col gap-4 rounded-[20px] bg-card p-5 leading-[1.4] lg:rounded-3xl lg:p-6">
              <FigmaIcon name={icon} className="hidden lg:block" />
              <h3 className="text-[23px] font-bold text-ink lg:text-2xl">{t}</h3>
              <p className="text-[15px] text-muted lg:text-base"><span className="lg:hidden">{m}</span><span className="hidden lg:inline">{d}</span></p>
            </div>
          ))}
        </div>
      </Band>

      {/* Delivery control 2:20220 / 2:20283 */}
      <Band className="lg:py-[72px]">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-[72px]">
          <div className="flex flex-col items-start gap-6 leading-[1.4] lg:w-[650px] lg:min-w-0 lg:shrink lg:gap-4">
            <Desk><Eyebrow>NƏZARƏT HƏMİŞƏ SİZDƏDİR</Eyebrow></Desk>
            <h2 className="text-[30px] font-bold text-ink lg:text-[42px]">Bağlamanız hardadır?<br />Bir baxışda bilin.</h2>
            <Mob><p className="text-base text-muted">Canlı xəritə, statuslar və yolda redaktə. Nəzarət həmişə sizdədir.</p></Mob>
            <Desk>
              <p className="text-lg text-muted">Kuryeri xəritədə canlı izləyin. Statuslardan xəbərdar olun, ünvan və qeydləri yolda dəyişin.</p>
              {["01   Kuryerinizin canlı mövqeyi", "02   Hər mərhələdə status bildirişi", "03   Sifarişi yolda redaktə edin"].map((x) => (
                <p key={x} className="whitespace-pre text-[17px] font-semibold text-ink">{x}</p>
              ))}
              <LinkButton href="/order" variant="accent" size="lg" className="w-full max-w-[290px]">Çatdırılmaya başla&nbsp;&nbsp;↗</LinkButton>
            </Desk>
          </div>
          <PhoneTrackingMobile className="lg:hidden" />
          <div className="hidden justify-center rounded-3xl bg-brand-surface p-8 lg:flex lg:w-[590px] lg:min-w-0 lg:shrink">
            <PhoneTracking />
          </div>
        </div>
      </Band>

      {/* Recipients 2:20221 / 2:20284 */}
      <Band tone="subtle" className="leading-[1.4] lg:py-16">
        <Mob>
          <h2 className="text-[30px] font-bold text-ink">Alıcılarınız da<br />rahat olsun.</h2>
          <p className="text-base text-muted">Brendli SMS, canlı izləmə linki və asan vaxt dəyişmə.</p>
          <div className="flex flex-col gap-4 rounded-[20px] bg-card p-5">
            <p className="whitespace-pre text-xs font-semibold text-muted">{"Kuryer.net  ·  İndi"}</p>
            <p className="text-[17px] font-medium text-ink">Salam, Aysel! Bağlamanız 14:42-də gələcək. İzləyin və ya vaxtı dəyişin.</p>
            <Link href="/tracking" className="whitespace-pre text-sm font-semibold text-link">{"kuryer.net/izle/AZ2048  ↗"}</Link>
          </div>
        </Mob>
        <Desk>
          <div className="flex flex-row items-start gap-14">
            <div className="flex w-[600px] min-w-0 shrink flex-col gap-4 rounded-3xl bg-card p-8">
              <p className="whitespace-pre text-xs font-bold text-muted">{"MESAJLAR  ·  Kuryer.net"}</p>
              <div className="flex flex-col gap-4 rounded-3xl bg-brand-surface p-6 text-ink">
                <p className="text-[22px] font-bold">Bağlamanız yoldadır! 📦</p>
                <p className="text-base">Salam, Aysel! Kuryer.net kuryeri təxminən 14:42-də gələcək. Canlı izləyin və ya vaxtı dəyişin.</p>
                <Link href="/tracking" className="text-[15px] font-semibold text-link">kuryer.net/izle/AZ2048&nbsp;&nbsp;↗</Link>
              </div>
            </div>
            <div className="flex w-[656px] min-w-0 shrink flex-col gap-4">
              <h2 className="text-[38px] font-bold text-ink">Alıcılarınız da<br />rahat olsun.</h2>
              <p className="text-lg text-muted">Brendinizin adı ilə SMS, canlı izləmə linki və rahat vaxt dəyişmə. “Kuryer hardadır?” suallarına son.</p>
            </div>
          </div>
        </Desk>
      </Band>

      {/* Cash collection 2:20222 / 2:20285 */}
      <Band className="leading-[1.4] lg:py-16">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-14">
          <div className="flex flex-col items-start gap-6 lg:w-[650px] lg:min-w-0 lg:shrink lg:gap-4">
            <Desk><Eyebrow>MƏHSULUN ÖDƏNİŞİNİ DƏ BİZƏ ETİBAR EDİN</Eyebrow></Desk>
            <h2 className="text-[30px] font-bold text-ink lg:text-[38px]">Çatdıraq.<br className="lg:hidden" /> Ödənişi də alaq.</h2>
            <p className="text-base text-muted lg:text-[17px]">
              <span className="lg:hidden">Kuryer məhsulun pulunu ödəsin və ya alıcıdan nağd ödənişi toplasın. Məbləği sifarişdə qeyd edin.</span>
              <span className="hidden lg:inline">Kuryer malın pulunu əvvəlcədən ödəyə və ya alıcıdan nağd ödənişi toplaya bilər. Məbləği sifarişdə qeyd edin.</span>
            </p>
            <Desk><LinkButton href="/business" variant="soft" size="lg" className="w-full max-w-[260px]">Ətraflı məlumat&nbsp;&nbsp;↗</LinkButton></Desk>
          </div>
          <div className="flex flex-col gap-4 whitespace-pre rounded-[20px] bg-amber-surface p-5 text-ink lg:w-[606px] lg:min-w-0 lg:shrink lg:rounded-3xl lg:p-8">
            <Desk><p className="text-xs font-semibold">SİFARİŞ #AZ-2050</p></Desk>
            <p className="text-lg font-medium lg:text-[22px]">
              <span className="lg:hidden">{"Məhsul      45,00 ₼\nÇatdırılma    8,40 ₼"}</span>
              <span className="hidden lg:inline">{"Məhsulun dəyəri             45,00 ₼\nÇatdırılma                       8,40 ₼"}</span>
            </p>
            <p className="text-2xl font-bold"><span className="lg:hidden">{"Alıcıdan      53,40 ₼"}</span><span className="hidden lg:inline">{"Alıcıdan alınacaq          53,40 ₼"}</span></p>
            <Desk><p className="text-sm font-medium">✓ Məbləğ hesabınıza köçürülür</p></Desk>
          </div>
        </div>
      </Band>

      {/* Mobile app 2:20223 / 2:20286 */}
      <Band tone="blue" className="lg:py-16">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-[72px]">
          <div className="flex flex-col gap-6 leading-[1.4] text-white lg:w-[700px] lg:min-w-0 lg:shrink lg:gap-4">
            <Desk><p className="text-xs font-bold text-[#FCEBEC]">BÜTÜN ÇATDIRILMALAR CİBİNİZDƏ</p></Desk>
            <h2 className="text-[34px] font-bold lg:text-[48px]">Bir tətbiq.<br />Min bir rahatlıq.</h2>
            <p className="text-base lg:text-lg">
              <span className="lg:hidden">Sifariş verin, izləyin, təkrar göndərin.</span>
              <span className="hidden lg:inline">Sifariş verin, kuryeri izləyin, təkrar göndərin.<br />Hər şey bir toxunuş uzaqlığında.</span>
            </p>
            <PhoneTrackingMobile className="lg:hidden" />
            <Desk><StoreBadges /></Desk>
            <p className="whitespace-pre text-[13px] font-medium lg:text-sm">
              <span className="lg:hidden">{"★ 4,9 / 5 · Nümunə reytinq"}</span>
              <span className="hidden lg:inline">{"★ 4,9 / 5  ·  Nümunə tətbiq reytinqi"}</span>
            </p>
            <Mob><StoreBadges className="flex-col gap-6 [&>a]:w-full" /></Mob>
            <Desk><p className="text-sm font-semibold">QR ilə yüklə&nbsp;&nbsp;↗</p></Desk>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src="/figma/phone/qr.svg" alt="Tətbiqi yükləmək üçün QR kod" width={132} height={132} />
            <p className="text-[11px]">Nümunə QR · tətbiq linki ilə əvəz edin</p>
          </div>
          <div className="hidden justify-center gap-5 lg:flex lg:w-[540px] lg:min-w-0 lg:shrink">
            <PhoneTracking width={260} />
            <PhoneTracking width={260} />
          </div>
        </div>
      </Band>

      <IntegrationsSection />

      {/* Courier recruitment 2:20225 / 2:20288 */}
      <Band tone="subtle" className="lg:py-16">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-14">
          <div className="flex flex-col items-start gap-6 leading-[1.4] lg:w-[650px] lg:min-w-0 lg:shrink lg:gap-4">
            <Desk><Eyebrow>ŞƏHƏRİ TANIYIRSAN? QAZANMAĞA BAŞLA.</Eyebrow></Desk>
            <h2 className="text-[34px] font-bold text-ink lg:text-[44px]">Sənin vaxtın.<br />Sənin qazancın.</h2>
            <CourierPhoto className="h-[218.75px] w-full max-w-[350px] lg:hidden" />
            <p className="text-base text-muted lg:text-lg">10 dəqiqəyə onlayn qeydiyyat. Sərbəst qrafik. Gündəlik qazanc.</p>
            <LinkButton href="/courier" variant="accent" size="lg" className="w-full lg:max-w-[300px]">Qazanmağa başla&nbsp;&nbsp;↗</LinkButton>
          </div>
          <div className="hidden lg:block lg:h-[330px] lg:w-[606px] lg:min-w-0 lg:shrink">
            <CourierPhoto className="h-[284.375px] w-full max-w-[455px] lg:ml-[65px]" />
          </div>
        </div>
      </Band>

      {/* Payment methods 2:20226 / 2:20289 */}
      <Band className="leading-[1.4] lg:py-12">
        <h2 className="text-[28px] font-bold text-ink lg:text-[32px]"><span className="lg:hidden">Rahat üsulla ödəyin.</span><span className="hidden lg:inline">Sizə rahat olan üsulla ödəyin.</span></h2>
        <div className="grid gap-6 lg:grid-cols-3 lg:items-start lg:gap-5">
          {PAYMENTS.map((p) => (
            <div key={p} className="whitespace-pre rounded-[20px] bg-surface p-5 text-[17px] font-semibold text-ink lg:rounded-3xl lg:p-6 lg:text-[19px]">{p}</div>
          ))}
        </div>
        <p className="text-sm text-muted"><span className="lg:hidden">Şirkətlər üçün müqavilə və hesab-faktura.</span><span className="hidden lg:inline">Hüquqi şəxslər üçün müqavilə və hesab-faktura təqdim olunur.</span></p>
      </Band>

      {/* FAQ 2:20227 / 2:20290 */}
      <Band id="faq" tone="subtle" className="scroll-mt-[112px] lg:scroll-mt-[94px] lg:py-16">
        <H2><span className="lg:hidden">Sualınız var?</span><span className="hidden lg:inline">Sualınız var? Cavabımız hazırdır.</span></H2>
        <Faq items={FAQ} mobileItems={FAQ_MOBILE} />
      </Band>

      {/* Cities 2:20228 / 2:20291 */}
      <Band className="leading-[1.4] lg:py-16">
        <H2>Təkcə Bakıda yox.<br className="lg:hidden" /> Hər yerdə.</H2>
        <Desk><p className="text-[17px] text-muted">Şəhərinizi seçin, uyğun xidməti və çatdırılma vaxtını görün.</p></Desk>
        <ul className="grid grid-cols-2 gap-x-4 gap-y-6 lg:grid-cols-4 lg:gap-x-6 lg:gap-y-8">
          {[...CITY_LINKS, "Digər şəhərlər"].map((c) => (
            <li key={c}>
              <Link href="/order" className="whitespace-pre text-base font-semibold text-link lg:text-lg">
                <span className="lg:hidden">{`${c} ↗`}</span>
                <span className="hidden lg:inline">{c === "Digər şəhərlər" ? "Digər şəhərlər  ↗  ↗" : `${c}  ↗`}</span>
              </Link>
            </li>
          ))}
        </ul>
      </Band>

      {/* Business channels 2:20229 / 2:20292 */}
      <Band tone="brand" className="lg:py-12">
        <div className="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-8">
          <div className="flex flex-col gap-6 leading-[1.4] lg:w-[800px] lg:min-w-0 lg:shrink lg:gap-4">
            <h2 className="text-[28px] font-bold text-ink lg:text-[30px]">Biznes üçün<br className="lg:hidden" /> faydalı xəbərlər</h2>
            <p className="text-base text-muted"><span className="lg:hidden">Tariflər, inteqrasiyalar və çatdırılma məsləhətləri.</span><span className="hidden lg:inline">Yeni tariflər, inteqrasiyalar və çatdırılma məsləhətləri.</span></p>
          </div>
          {["Telegram", "WhatsApp"].map((c) => (
            <a key={c} href="#" className="whitespace-pre rounded-xl bg-brand-surface p-[18px] text-center text-[15px] font-semibold leading-[1.4] text-ink lg:w-[224px]">{`${c}  ↗`}</a>
          ))}
        </div>
      </Band>
    </>
  );
}
