# Yetir — Courier delivery frontend (Next.js)

Next.js 14 (App Router) + TypeScript + Tailwind CSS frontend for an on-demand courier platform in Azerbaijan, built pixel-perfect from the Figma design (Inter; blue `#0A6CFF` + amber `#FFB800`). Desktop follows the 1440 frames, below 1024px the 390 mobile frames. Pages live in the `app/(site)` route group (site header/footer); `/dashboard` and `/design-system` have their own layout.

## Run
```bash
npm install      # or pnpm install
npm run dev      # http://localhost:3000
npm run build && npm start
```

## Pages
| Route | Screen |
|---|---|
| `/` | Homepage (hero + price calculator, categories, logo marquee, business lead form, tracking, app, integrations, FAQ, cities) |
| `/order` | Order creation — vehicle, fast/scheduled, multi-stop route with autocomplete, parcel, extras, payment, promo, sticky price summary (price sheet on mobile) |
| `/tracking` | Live tracking — map, ETA, courier card, status timeline, proof of delivery |
| `/dashboard` | Client dashboard — orders + filters, addresses, balance/invoices, API keys, team, notifications |
| `/business` | For business landing — terms, volume pricing, cases, lead form |
| `/courier`, `/courier/signup` | Courier landing with earnings calculator, sign-up form |
| `/pricing` | Tariff cards per vehicle |
| `/login`, `/login/verify` | Phone login + SMS OTP, account type selection |
| `/design-system` | Tokens and components |

Dark mode (Figma frame 14 colors): the ☾/☀ toggle in the header switches it and remembers the choice; until then it follows the OS setting.  Language switcher AZ/RU/EN covers header + hero (`lib/i18n.tsx`); the rest of the copy is Azerbaijani — move it into the dictionary or `next-intl` when you add full translations.

## Where to plug in the backend
- `lib/pricing.ts` — price formula + `estimateKm` (placeholder; replace with Google/Yandex/OSRM distance).
- `lib/data.ts` — cities, FAQ.
- `components/BakuMap.tsx` is the schematic Figma map; swap for Google Maps / Mapbox / Leaflet.
- `app/(site)/tracking/page.tsx` — simulated ETA countdown; replace with WebSocket/polling (e.g. Laravel Reverb / Pusher).
- Forms (`LeadForm`, courier signup, login/OTP, order, dashboard filters) only update local state — wire them to your API.

Brand: **Yetir** (domain `yetir.az`). Prices, metrics, partner names, legal details (company name, VÖEN) and the promo `[CODE]` are still placeholders.
