export default function ShopLogo({ className = "", size = 48 }) {
  return (
    // eslint-disable-next-line @next/next/no-img-element
    <img
      src="/hh-shop-logo.svg"
      alt=""
      width={size}
      height={size}
      className={`shop-logo ${className}`}
      aria-hidden="true"
    />
  );
}
