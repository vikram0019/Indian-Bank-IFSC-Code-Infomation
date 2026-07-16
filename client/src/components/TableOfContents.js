export default function TableOfContents({ items }) {
  return (
    <nav className="sticky top-8 self-start hidden lg:block text-sm">
      <p className="font-semibold mb-2 text-gray-700 dark:text-gray-300">On this page</p>
      <ul className="space-y-1.5 border-l border-gray-200 dark:border-gray-800 pl-3">
        {items.map((item) => (
          <li key={item.id}>
            <a
              href={`#${item.id}`}
              className="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
            >
              {item.label}
            </a>
          </li>
        ))}
      </ul>
    </nav>
  );
}
