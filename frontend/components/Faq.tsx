/** "+" when collapsed, "−" when open — right-aligned on the question row. Needs a `group` <details> ancestor. */
export function FaqSign() {
  return (
    <span aria-hidden className="shrink-0">
      <span className="group-open:hidden">+</span>
      <span className="hidden group-open:inline">−</span>
    </span>
  );
}

/** Summary row: question on the left, +/− on the right, same line. */
export const faqSummary = "flex cursor-pointer items-start justify-between gap-4 font-semibold text-ink";

/** Figma "FAQ" items.
 *  Desktop (2:20936…): white card, radius 24, p 24, gap 16; question 18/600, answer 16 muted; 32px apart.
 *  Mobile (2:20290): radius 20, p 20, question 16/600, answer 14; 24px apart.
 *  The +/− is right-aligned on the question row (changed on request from the design's inline placement). */
export function Faq({ items, mobileItems, className }: { items: { q: string; a: string }[]; mobileItems?: { q: string; a?: string }[]; className?: string }) {
  return (
    <div className={`flex flex-col gap-6 lg:gap-8 ${className ?? ""}`}>
      {items.map((f, i) => (
        <details key={f.q} open={i === 0} className="group rounded-[20px] bg-card p-5 leading-[1.4] lg:rounded-3xl lg:p-6">
          <summary className={`${faqSummary} text-base lg:text-lg`}>
            <span>
              <span className="lg:hidden">{mobileItems?.[i]?.q ?? f.q}</span>
              <span className="hidden lg:inline">{f.q}</span>
            </span>
            <FaqSign />
          </summary>
          <p className="mt-4 text-sm text-muted lg:text-base"><span className="lg:hidden">{mobileItems?.[i]?.a ?? f.a}</span><span className="hidden lg:inline">{f.a}</span></p>
        </details>
      ))}
    </div>
  );
}
