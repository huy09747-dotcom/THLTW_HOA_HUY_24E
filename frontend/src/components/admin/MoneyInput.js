"use client";

import { useState } from "react";
import styles from "@/app/admin/admin.module.css";

export default function MoneyInput({ id, name, initialValue, required, onValueChange }) {
  const initial = Number(initialValue);
  const [digits, setDigits] = useState(
    initial >= 1000 && initial % 1000 === 0 ? String(initial / 1000) : ""
  );
  const display = digits.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  return (
    <div className={styles.moneyInput}>
      <input
        id={id}
        type="text"
        inputMode="numeric"
        autoComplete="off"
        value={display}
        placeholder="0"
        required={required}
        aria-label="Số tiền theo nghìn đồng"
        onChange={(event) => {
          const next = event.target.value.replace(/[^0-9]/g, "").replace(/^0+/, "").slice(0, 12);
          setDigits(next);
          onValueChange?.(next ? Number(next) * 1000 : "");
        }}
      />
      <span aria-hidden="true">.000 ₫</span>
      <input type="hidden" name={name} value={digits ? Number(digits) * 1000 : ""} />
    </div>
  );
}
