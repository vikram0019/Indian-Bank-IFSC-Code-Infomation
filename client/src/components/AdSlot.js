const VARIANTS = {
  sidebar: 'w-[300px] h-[250px]',
  footer: 'w-full max-w-[728px] h-[90px] mx-auto',
  inline: 'w-full max-w-[468px] h-[60px] mx-auto',
};

// Placeholder ad slot. Swap the inner div for the real AdSense <ins> snippet
// once a publisher ID is available (see project-planning/scope.md — Monetization).
export default function AdSlot({ variant = 'sidebar', className = '' }) {
  return (
    <div
      className={`${VARIANTS[variant] || VARIANTS.sidebar} flex items-center justify-center border border-dashed border-gray-300 dark:border-gray-700 text-xs text-gray-400 dark:text-gray-600 rounded ${className}`}
    >
      Ad placeholder ({variant})
    </div>
  );
}
