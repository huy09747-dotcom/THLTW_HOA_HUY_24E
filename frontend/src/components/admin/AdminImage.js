"use client";

import { useState } from "react";
import { imageUrl } from "@/utils/format";
import styles from "@/app/admin/admin.module.css";

export default function AdminImage({ image, name }) {
  const [failed, setFailed] = useState(null);
  const source = imageUrl(image);

  if (!source || failed === source) {
    return <span className={styles.imageFallback} aria-label="Chưa có hình ảnh">—</span>;
  }

  return (
    // eslint-disable-next-line @next/next/no-img-element
    <img
      src={source}
      alt={name}
      className={styles.thumbnail}
      loading="lazy"
      onError={() => setFailed(source)}
    />
  );
}
