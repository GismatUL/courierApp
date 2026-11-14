import { AZ_PLACES } from "./regions";

/** All delivery places in Azerbaijan (see lib/regions.ts). */
export const CITIES = AZ_PLACES;

export const FAQ = [
  { q: "Necə sifariş verə bilərəm?", a: "Ünvanları daxil edin, nəqliyyat seçin və qiyməti hesablayın. Sifarişi təsdiq etdikdən sonra uyğun kuryer tapacağıq." },
  { q: "Çatdırılmanı konkret vaxta sifariş etmək olar?", a: "Bəli. «Planlı» rejimini seçib tarix və vaxt aralığını göstərin. 4 saatlıq interval seçsəniz qiymət daha ucuz olur." },
  { q: "Çatdırılma nə qədər vaxt aparır?", a: "Ekspress çatdırılma şəhər daxilində 30–60 dəqiqə, şəhərlərarası çatdırılma isə məsafədən asılı olaraq həmin gün ərzində həyata keçirilir." },
  { q: "Qiymət necə hesablanır?", a: "Yekun qiymət məsafə, nəqliyyat növü, çəki, əlavə ünvanların sayı, gözləmə vaxtı və əlavə xidmətlərdən asılıdır." },
  { q: "Biznes üçün müqavilə bağlamaq mümkündür?", a: "Bəli. Ayda 30+ çatdırılma üçün xüsusi tariflər, şəxsi menecer, post-ödəniş və hesab-faktura ilə iş təklif edirik." },
  { q: "Hansı ödəniş üsulları var?", a: "Nağd, bank kartı və şirkətlər üçün bank köçürməsi (müqavilə əsasında)." },
  { q: "Hansı şəhərlərdə işləyirsiniz?", a: "Bakı, Sumqayıt, Gəncə, Mingəçevir, Lənkəran, Şəki və Azərbaycanın digər şəhərlərində." },
  { q: "Bağlamam sığortalanırmı?", a: "Elan olunmuş dəyər çərçivəsində tam kompensasiya ödəyirik. Sığorta sifariş zamanı avtomatik aktivləşir." },
];

/** Mobile frame (2:20290) uses shorter question wording; answers are shared with FAQ. */
export const FAQ_MOBILE: { q: string; a?: string }[] = [
  { q: "Necə sifariş verə bilərəm?", a: "Ünvanları daxil edin, nəqliyyat seçin və qiyməti hesablayın." },
  { q: "Konkret vaxta sifariş olar?" },
  { q: "Çatdırılma nə qədər vaxt aparır?" },
  { q: "Qiymət necə hesablanır?" },
  { q: "Biznes müqaviləsi mümkündür?" },
  { q: "Necə ödəyə bilərəm?" },
  { q: "Hansı şəhərlərdə işləyirsiniz?" },
  { q: "Bağlamam sığortalanırmı?" },
];
