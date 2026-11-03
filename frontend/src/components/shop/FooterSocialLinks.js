const paths = {
  facebook: "M13.5 22v-9h3l.5-3.5h-3.5V7.25c0-1 .28-1.75 1.75-1.75H17V2.37A23 23 0 0 0 14.45 2C11.92 2 10 3.54 10 6.37V9.5H7V13h3v9h3.5Z",
  tiktok: "M16 2h-3v13.5a3 3 0 1 1-3-3v-3a6 6 0 1 0 6 6V8.1a8 8 0 0 0 5 1.65v-3A5 5 0 0 1 16 2Z",
};

export default function FooterSocialLinks({ email, phone }) {
  const links = [
    { name: "facebook", label: "Theo dõi trên Facebook", href: "https://web.facebook.com/NMHuy10105", external: true },
    { name: "tiktok", label: "Theo dõi trên TikTok", href: "https://www.tiktok.com/@kizz10706", external: true },
    { name: "email", label: `Gửi email tới ${email}`, href: `mailto:${email}` },
    { name: "phone", label: `Gọi ${phone}`, href: `tel:${phone.replace(/[^+0-9]/g, "")}` },
  ];
  return <div className="footer-follow">
    <h2>Kết nối với HH Shop</h2>
    <nav className="footer-social-links" aria-label="Mạng xã hội và liên hệ">
      {links.map(link => <a key={link.name} className={`footer-social-link footer-social-${link.name}`} href={link.href} aria-label={link.label} target={link.external ? "_blank" : undefined} rel={link.external ? "noopener noreferrer" : undefined}>
        <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
          {paths[link.name] ? <path fill="currentColor" d={paths[link.name]} /> : link.name === "email" ? <g fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/></g> : <path fill="currentColor" d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.56 3.58.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.19 2.46.56 3.58a1 1 0 0 1-.25 1.01l-2.19 2.2Z"/>}
        </svg>
        <span className="footer-social-tooltip" aria-hidden="true">{link.label}</span>
      </a>)}
    </nav>
  </div>;
}
