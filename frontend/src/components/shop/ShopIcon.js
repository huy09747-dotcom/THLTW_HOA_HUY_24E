export default function ShopIcon({ name, className = "" }) {
  const paths = {
    heart: <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z" />,
    search: <><circle cx="10.5" cy="10.5" r="6.5" /><path d="m16 16 4.5 4.5" /></>,
    user: <><circle cx="12" cy="8" r="4" /><path d="M4 21v-2a8 8 0 0 1 16 0v2" /></>,
    cart: <><path d="M2 3h3l3 12h11l3-9H6" /><circle cx="9" cy="20" r="1" /><circle cx="19" cy="20" r="1" /></>,
    cartPlus: <><path d="M2 3h3l3 12h11l2-6M14 3v6m-3-3h6" /><circle cx="9" cy="20" r="1" /><circle cx="19" cy="20" r="1" /></>,
    orders: <><rect x="5" y="3" width="14" height="18" rx="2" /><path d="M9 8h6M9 12h6M9 16h4" /></>,
    loading: <path d="M20 12a8 8 0 1 1-8-8" />,
  };

  return (
    <svg
      className={`shop-icon ${className}`}
      width="22"
      height="22"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="1.7"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden="true"
      focusable="false"
    >
      {paths[name]}
    </svg>
  );
}
