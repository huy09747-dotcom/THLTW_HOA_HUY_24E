"use client";

import Image from "next/image";
import { useState } from "react";

export default function ContactShortcuts() {
  const [showZalo, setShowZalo] = useState(false);

  return (
    <>
      <nav className="contact-shortcuts" aria-label="Liên hệ nhanh">
        <button type="button" className="contact-shortcut contact-shortcut-zalo" aria-label="Xem danh thiếp Zalo" title="Zalo" onClick={() => setShowZalo(value => !value)}>
          <span className="contact-zalo-mark" aria-hidden="true">Zalo</span>
        </button>
        <a className="contact-shortcut contact-shortcut-messenger" href="https://m.me/NMHuy10105" target="_blank" rel="noreferrer" aria-label="Nhắn tin qua Messenger" title="Messenger">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.14 2 11.25c0 2.91 1.44 5.51 3.69 7.2V22l3.37-1.85c.93.26 1.92.4 2.94.4 5.52 0 10-4.14 10-9.25S17.52 2 12 2Zm1 12.45-2.55-2.72-4.97 2.72 5.47-5.81 2.61 2.72 4.91-2.72L13 14.45Z"/></svg>
        </a>
        <a className="contact-shortcut contact-shortcut-phone" href="tel:0398716527" aria-label="Gọi điện thoại" title="Gọi 0398 716 527">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.56 3.58.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.19 2.46.56 3.58a1 1 0 0 1-.25 1.01l-2.19 2.2Z"/></svg>
        </a>
      </nav>
      {showZalo && <div className="zalo-contact-backdrop" role="presentation" onClick={() => setShowZalo(false)}>
        <section className="zalo-contact-card" role="dialog" aria-modal="true" aria-labelledby="zalo-contact-title" onClick={event => event.stopPropagation()}>
          <button type="button" className="zalo-contact-close" onClick={() => setShowZalo(false)} aria-label="Đóng">×</button>
          <Image src="/zalo-contact-qr.png" alt="Mã QR danh thiếp Zalo Khánh Hòa" width={420} height={612} priority />
          <h2 id="zalo-contact-title">Quét mã QR để liên hệ Zalo</h2>
          <p>Mở Zalo trên điện thoại và quét mã danh thiếp Khánh Hòa.</p>
        </section>
      </div>}
    </>
  );
}
