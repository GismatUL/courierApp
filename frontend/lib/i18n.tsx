"use client";
import { createContext, useContext, useEffect, useState, type ReactNode } from "react";

export type Lang = "az" | "ru" | "en";

/** Azerbaijani locative (-da / -də) by vowel harmony on the last vowel: Bakıda, Gəncədə, Şəkidə. */
export function locative(place: string): string {
  const last = [...place.toLocaleLowerCase("az")].reverse().find((ch) => "aıoueəiöü".includes(ch));
  return place + (last && "aıou".includes(last) ? "da" : "də");
}

const dict = {
  az: {
    business: "Biznes üçün", courier: "Kuryer ol", pricing: "Tariflər", help: "Kömək",
    login: "Daxil ol", order: "Sifariş et",
    heroTitle: (c: string) => `${locative(c)} kuryer\nçatdırılması —\n30 dəqiqədən`,
    heroSub: "Bir sənəddən böyük bağlamaya qədər.\nİndi göndərin, bu gün ünvanına çatsın.",
    calc: "Qiyməti hesabla", from: "HARADAN", to: "HARAYA", vehicle: "NƏQLİYYAT",
    forBusiness: "Biznes üçün xüsusi şərtlər",
  },
  ru: {
    business: "Для бизнеса", courier: "Стать курьером", pricing: "Тарифы", help: "Помощь",
    login: "Войти", order: "Заказать",
    heroTitle: (c: string) => `Курьерская доставка: ${c} — от 30 минут`,
    heroSub: "От документов до крупногабаритных грузов. В любой город Азербайджана в тот же день.",
    calc: "Рассчитать", from: "Откуда", to: "Куда", vehicle: "Транспорт",
    forBusiness: "Особые условия для бизнеса",
  },
  en: {
    business: "For business", courier: "Become a courier", pricing: "Pricing", help: "Help",
    login: "Log in", order: "Order delivery",
    heroTitle: (c: string) => `Courier delivery in ${c} — from 30 minutes`,
    heroSub: "From documents to large cargo. Anywhere in Azerbaijan, delivered the same day.",
    calc: "Calculate price", from: "From", to: "To", vehicle: "Vehicle",
    forBusiness: "Special terms for business",
  },
};

type Dict = (typeof dict)["az"];
const Ctx = createContext<{ lang: Lang; setLang: (l: Lang) => void; t: Dict; city: string; setCity: (c: string) => void }>({
  lang: "az", setLang: () => {}, t: dict.az, city: "Bakı", setCity: () => {},
});

export function I18nProvider({ children }: { children: ReactNode }) {
  const [lang, setLangState] = useState<Lang>("az");
  const [city, setCity] = useState("Bakı");
  useEffect(() => {
    try {
      const l = localStorage.getItem("lang") as Lang | null;
      if (l && l in dict) setLangState(l);
    } catch {}
  }, []);
  const setLang = (l: Lang) => {
    setLangState(l);
    try { localStorage.setItem("lang", l); } catch {}
    document.documentElement.lang = l;
  };
  return <Ctx.Provider value={{ lang, setLang, t: dict[lang], city, setCity }}>{children}</Ctx.Provider>;
}

export const useI18n = () => useContext(Ctx);
