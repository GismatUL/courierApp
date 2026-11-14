import { LinkButton } from "@/components/ui";
export default function NotFound() {
  return (
    <div className="mx-auto max-w-md px-4 py-24 text-center">
      <div className="text-7xl font-extrabold text-link">404</div>
      <p className="mt-4 text-slate-500">Səhifə tapılmadı</p>
      <LinkButton href="/" className="mt-8">Ana səhifə</LinkButton>
    </div>
  );
}
