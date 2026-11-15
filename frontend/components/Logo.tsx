import Link from "next/link";
import Image from "next/image";
import clsx from "clsx";

/** Figma "Kuryer.net / Official logo" (59:987 header 158 × 51.3, 59:989 mobile 164 × 53.3, 59:988 footer 174 × 56.5).
 *  The 237 × 77 red mark is used as-is on light and dark backgrounds (its letters are cut out, so it cannot be recoloured). */
export function Logo({ className }: { className?: string }) {
  return (
    <Link href="/" aria-label="Kuryer.net — ana səhifə" className={clsx("block shrink-0", className)}>
      <Image
        src="/figma/brand/logo.png"
        alt="Kuryer.net"
        width={237}
        height={77}
        priority
        className="h-auto w-full"
      />
    </Link>
  );
}
