export default function FlagBadge({ label, active }) {
  return (
    <span
      className={`px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset ${
        active
          ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20'
          : 'bg-gray-50 text-gray-500 ring-gray-500/10 dark:bg-gray-400/5 dark:text-gray-500 dark:ring-gray-400/10'
      }`}
    >
      {label}
    </span>
  );
}
