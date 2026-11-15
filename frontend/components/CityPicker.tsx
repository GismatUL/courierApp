"use client";
import { useEffect, useId, useMemo, useRef, useState } from "react";
import clsx from "clsx";
import { useI18n } from "@/lib/i18n";
import { AZ_PLACES, AZ_REGIONS, POPULAR_PLACES, matchesPlace } from "@/lib/regions";
import { Chevron } from "./ui";

/** City trigger + "Çatdırılma bölgəniz" dialog (updated Figma header › Selected city).
 *  The dialog keeps the design's 12 quick picks, then lists every place in Azerbaijan by economic region,
 *  with a search box (search added because the full list has 75 places).
 *  `variant="desktop"`: 108 × 42 button, bordered on hover; `"mobile"`: plain "Bakı" + chevron text. */
export function CityPicker({ variant, className }: { variant: "desktop" | "mobile"; className?: string }) {
  const { city, setCity } = useI18n();
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState("");
  const titleId = useId();
  const searchRef = useRef<HTMLInputElement>(null);
  const triggerRef = useRef<HTMLButtonElement>(null);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => { if (e.key === "Escape") setOpen(false); };
    document.addEventListener("keydown", onKey);
    document.body.style.overflow = "hidden";
    searchRef.current?.focus();
    const trigger = triggerRef.current;
    return () => {
      document.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
      trigger?.focus();
    };
  }, [open]);

  const results = useMemo(() => (query.trim() ? AZ_PLACES.filter((p) => matchesPlace(p, query)) : null), [query]);
  const close = () => { setOpen(false); setQuery(""); };
  const choose = (c: string) => { setCity(c); close(); };

  const Option = ({ name }: { name: string }) => (
    <button
      type="button"
      aria-current={name === city}
      onClick={() => choose(name)}
      className={clsx(
        "rounded-[10px] p-3 text-left text-base leading-[1.4] transition-colors hover:bg-[#FEF7F7] hover:text-link dark:hover:bg-brand-surface",
        name === city ? "font-semibold text-link" : "text-[#242325] dark:text-ink",
      )}
    >
      {name}
    </button>
  );
  const grid = "grid grid-cols-1 gap-x-[42px] gap-y-1 sm:grid-cols-2 md:grid-cols-3";
  const groupTitle = "mb-1 mt-6 px-3 text-xs font-semibold uppercase tracking-wide text-[#686468] dark:text-muted";

  return (
    <>
      <button
        ref={triggerRef}
        type="button"
        aria-haspopup="dialog"
        aria-expanded={open}
        onClick={() => setOpen(true)}
        className={clsx(
          variant === "desktop"
            ? "flex h-[42px] w-full items-center justify-between rounded-xl border border-transparent px-2.5 text-ink transition-colors hover:border-[#E6E1E1] hover:bg-[#F7F6F5] dark:hover:border-line dark:hover:bg-surface"
            : "text-left text-sm font-semibold text-ink",
          className,
        )}
      >
        {variant === "desktop" ? (
          <>
            <span className="flex min-w-0 items-center gap-[7px] text-sm font-semibold"><span className="text-link" aria-hidden>⌖</span><span className="truncate">{city}</span></span>
            <Chevron className="text-[#686468] dark:text-muted" />
          </>
        ) : (
          <span className="flex items-center gap-1">{city}<Chevron /></span>
        )}
      </button>

      {open && (
        <div className="fixed inset-0 z-[999] flex items-start justify-center bg-night/60 px-6 pt-[6vh] backdrop-blur-[2px]">
          <button type="button" aria-label="Bağla" tabIndex={-1} className="absolute inset-0 cursor-default" onClick={close} />
          <section
            role="dialog"
            aria-modal="true"
            aria-labelledby={titleId}
            className="relative z-[1] max-h-[88vh] w-full max-w-[760px] overflow-y-auto rounded-[20px] bg-card px-[38px] pb-[38px] pt-8 shadow-[0_28px_80px_rgba(36,35,37,0.28)]"
          >
            <div className="mb-2.5 flex items-start justify-between gap-6">
              <div>
                <h2 id={titleId} className="text-[32px] font-bold leading-[1.25] text-ink">Çatdırılma bölgəniz</h2>
                <p className="mt-2 text-sm leading-[1.5] text-[#686468] dark:text-muted">Sifariş vermək istədiyiniz şəhəri seçin.</p>
              </div>
              <button
                type="button"
                aria-label="Bağla"
                onClick={close}
                className="-mr-2 -mt-2 flex size-[42px] items-center justify-center rounded-full text-[28px] font-light text-[#686468] transition-colors hover:bg-[#F7F6F5] hover:text-ink dark:text-muted dark:hover:bg-surface"
              >
                ×
              </button>
            </div>
            <div className="my-[22px] h-px bg-[#E6E1E1] dark:bg-line" />
            <label className="mb-4 flex items-center gap-2 rounded-xl border border-line bg-card px-4 py-3 focus-within:border-brand-500">
              <span aria-hidden className="text-muted">⌕</span>
              <input
                ref={searchRef}
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Şəhər və ya rayon axtar"
                aria-label="Şəhər və ya rayon axtar"
                className="w-full bg-transparent text-[15px] text-ink outline-none placeholder:text-muted"
              />
            </label>

            {results ? (
              results.length ? (
                <div className={grid}>{results.map((p) => <Option key={p} name={p} />)}</div>
              ) : (
                <p className="px-3 py-3 text-base text-muted">“{query}” tapılmadı.</p>
              )
            ) : (
              <>
                <h3 className={clsx(groupTitle, "mt-0")}>Əsas şəhərlər</h3>
                <div className={grid}>{POPULAR_PLACES.map((p) => <Option key={p} name={p} />)}</div>
                {AZ_REGIONS.map((r) => (
                  <div key={r.region}>
                    <h3 className={groupTitle}>{r.region}</h3>
                    <div className={grid}>{r.places.map((p) => <Option key={p} name={p} />)}</div>
                  </div>
                ))}
              </>
            )}
          </section>
        </div>
      )}
    </>
  );
}
