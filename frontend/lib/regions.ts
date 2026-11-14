/**
 * Azerbaijan's places for delivery, grouped by the 14 economic regions (Presidential Decree of 7 July 2021).
 * Covers all 66 districts (rayonlar) and the cities of republican subordination; a name shared by a city
 * and its district (Lənkəran, Şəki, Yevlax) is listed once. Xırdalan (Abşeron district) is added as a major
 * delivery destination.
 */
export const AZ_REGIONS: { region: string; places: string[] }[] = [
  { region: "Bakı", places: ["Bakı"] },
  { region: "Abşeron-Xızı", places: ["Sumqayıt", "Xırdalan", "Abşeron", "Xızı"] },
  { region: "Dağlıq Şirvan", places: ["Şamaxı", "Ağsu", "İsmayıllı", "Qobustan"] },
  { region: "Gəncə-Daşkəsən", places: ["Gəncə", "Naftalan", "Daşkəsən", "Göygöl", "Goranboy", "Samux"] },
  { region: "Qarabağ", places: ["Xankəndi", "Şuşa", "Ağcabədi", "Ağdam", "Bərdə", "Füzuli", "Xocalı", "Xocavənd", "Tərtər"] },
  { region: "Qazax-Tovuz", places: ["Qazax", "Ağstafa", "Gədəbəy", "Şəmkir", "Tovuz"] },
  { region: "Quba-Xaçmaz", places: ["Quba", "Qusar", "Xaçmaz", "Siyəzən", "Şabran"] },
  { region: "Lənkəran-Astara", places: ["Lənkəran", "Astara", "Cəlilabad", "Lerik", "Masallı", "Yardımlı"] },
  { region: "Mərkəzi Aran", places: ["Mingəçevir", "Yevlax", "Ağdaş", "Göyçay", "Kürdəmir", "Ucar", "Zərdab"] },
  { region: "Mil-Muğan", places: ["Beyləqan", "İmişli", "Saatlı", "Sabirabad"] },
  { region: "Şəki-Zaqatala", places: ["Şəki", "Zaqatala", "Balakən", "Qax", "Qəbələ", "Oğuz"] },
  { region: "Şərqi Zəngəzur", places: ["Cəbrayıl", "Kəlbəcər", "Laçın", "Qubadlı", "Zəngilan"] },
  { region: "Şirvan-Salyan", places: ["Şirvan", "Salyan", "Biləsuvar", "Hacıqabul", "Neftçala"] },
  { region: "Naxçıvan", places: ["Naxçıvan", "Babək", "Culfa", "Kəngərli", "Ordubad", "Sədərək", "Şahbuz", "Şərur"] },
];

/** Every place, in region order (Bakı first). */
export const AZ_PLACES: string[] = AZ_REGIONS.flatMap((r) => r.places);

/** The cities featured in the Figma city dialog, shown first as quick picks. */
export const POPULAR_PLACES = ["Bakı", "Sumqayıt", "Xırdalan", "Gəncə", "Lənkəran", "Mingəçevir", "Naxçıvan", "Qəbələ", "Quba", "Şəki", "Şamaxı", "Şirvan"];

/** Case- and diacritic-insensitive match for Azerbaijani names (e.g. "seki" finds "Şəki"). */
export function matchesPlace(name: string, query: string): boolean {
  const fold = (s: string) =>
    s.toLocaleLowerCase("az").replace(/[əçğıöşüi̇]/g, (c) => ({ ə: "e", ç: "c", ğ: "g", ı: "i", ö: "o", ş: "s", ü: "u", "i̇": "i" })[c] ?? c);
  return fold(name).includes(fold(query.trim()));
}
