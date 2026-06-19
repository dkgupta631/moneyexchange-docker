import { useState, useRef } from "react";
import { usePage, Head, Link } from "@inertiajs/react";

// ─── Same thermal print CSS pattern as ShowMoneyExchangeInvoices ───────────
const PRINT_STYLE = `
@media print {
  @page { size: 80mm auto; margin: 0; }
  body * { visibility: hidden !important; }
  #transfer-receipt-root, #transfer-receipt-root * { visibility: visible !important; }
  #transfer-receipt-root {
    position: fixed !important; inset: 0 !important;
    width: 80mm !important; margin: 0 auto !important;
    padding: 0 !important; background: #fff !important;
    font-family: 'Courier New', monospace !important;
    font-size: 11px !important; color: #000 !important;
    box-shadow: none !important; border-radius: 0 !important;
    transform: none !important;
  }
  .no-print { display: none !important; }
}
`;

export default function MoneyTransferInvoice({ invoice }) {
    const { translations } = usePage().props;
    const t = (key) => translations?.[key] ?? key;
    const receiptRef = useRef(null);

    // ── Zoom state ─────────────────────────────────────────────────────────
    const [zoom, setZoom] = useState(2.2);
    const zoomIn    = () => setZoom(z => Math.min(+(z + 0.1).toFixed(1), 3.0));
    const zoomOut   = () => setZoom(z => Math.max(+(z - 0.1).toFixed(1), 0.5));
    const zoomReset = () => setZoom(2.2);

    /* ── Helpers ── */
    const fmt = (v) =>
        new Intl.NumberFormat("th-TH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(parseFloat(v) || 0);

    const fmtDate = (dateStr) => {
        if (!dateStr) return "—";
        const d = new Date(dateStr);
        return d.toLocaleDateString("en-GB", {
            day: "2-digit", month: "2-digit", year: "numeric",
        });
    };

    const fmtTime = (dateStr) => {
        if (!dateStr) return "—";
        const d = new Date(dateStr);
        return d.toLocaleTimeString("en-GB", {
            hour: "2-digit", minute: "2-digit", hour12: true,
        }).toUpperCase();
    };

    // ── window.print() — CSS handles thermal layout, transform:none on print
    const handlePrint = () => window.print();

    // ── PNG: temporarily clear zoom transform before capture ───────────────
    const handleSavePNG = async () => {
        if (!receiptRef.current) return;
        if (!window.html2canvas) {
            const script = document.createElement("script");
            script.src = "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js";
            document.head.appendChild(script);
            await new Promise(r => (script.onload = r));
        }
        const el = receiptRef.current;
        const prev = el.style.transform;
        el.style.transform = "none";
        const canvas = await window.html2canvas(el, {
            backgroundColor: "#ffffff", scale: 2, useCORS: true,
        });
        el.style.transform = prev;
        const link = document.createElement("a");
        link.download = `transfer-${invoice?.invoice_number ?? "invoice"}.png`;
        link.href = canvas.toDataURL("image/png");
        link.click();
    };

    const inv = invoice || {};

    return (
        <>
            <Head title={`${t("Money Transfer")} ${t("Invoice")}`} />
            <style>{PRINT_STYLE}</style>

            <div style={S.page}>
                <br/><br/><br/><br/>

                {/* ══ TOP BAR — hidden on print via .no-print ══ */}
                <div className="no-print" style={S.topBar}>
                    <Link href={route('home')} style={S.backBtn}>
                        ← {t("Back")}
                    </Link>
                    <span style={S.breadcrumb}>
                        ({t("Money Transfer")}) · ({t("Invoice")})
                    </span>

                    {/* Zoom controls */}
                    <div style={{ display: "flex", alignItems: "center", gap: 4 }}>
                        <button
                            onClick={zoomOut} disabled={zoom <= 0.5} title="Zoom Out"
                            style={{
                                background: "transparent",
                                border: `1.5px solid ${zoom <= 0.5 ? "rgba(255,255,255,0.1)" : "rgba(255,255,255,0.25)"}`,
                                borderRadius: 8, padding: "5px 11px",
                                color: zoom <= 0.5 ? "rgba(255,255,255,0.25)" : "#fff",
                                cursor: zoom <= 0.5 ? "not-allowed" : "pointer",
                                fontSize: 16, fontWeight: 700, lineHeight: 1, transition: "all .2s",
                            }}
                            onMouseOver={e => { if (zoom > 0.5) e.currentTarget.style.background = "rgba(91,45,142,0.5)"; }}
                            onMouseOut={e =>  { e.currentTarget.style.background = "transparent"; }}
                        >−</button>

                        <button
                            onClick={zoomReset} title="Reset zoom"
                            style={{
                                background: "rgba(91,45,142,0.3)",
                                border: "1.5px solid rgba(255,255,255,0.25)",
                                borderRadius: 8, padding: "5px 10px",
                                color: "rgba(255,255,255,0.7)", cursor: "pointer",
                                fontSize: 11, fontWeight: 700,
                                minWidth: 52, textAlign: "center", transition: "all .2s",
                            }}
                            onMouseOver={e => { e.currentTarget.style.background = "#5B2D8E"; e.currentTarget.style.color = "#fff"; }}
                            onMouseOut={e =>  { e.currentTarget.style.background = "rgba(91,45,142,0.3)"; e.currentTarget.style.color = "rgba(255,255,255,0.7)"; }}
                        >{Math.round(zoom * 100)}%</button>

                        <button
                            onClick={zoomIn} disabled={zoom >= 3.0} title="Zoom In"
                            style={{
                                background: "transparent",
                                border: `1.5px solid ${zoom >= 3.0 ? "rgba(255,255,255,0.1)" : "rgba(255,255,255,0.25)"}`,
                                borderRadius: 8, padding: "5px 11px",
                                color: zoom >= 3.0 ? "rgba(255,255,255,0.25)" : "#fff",
                                cursor: zoom >= 3.0 ? "not-allowed" : "pointer",
                                fontSize: 16, fontWeight: 700, lineHeight: 1, transition: "all .2s",
                            }}
                            onMouseOver={e => { if (zoom < 3.0) e.currentTarget.style.background = "rgba(91,45,142,0.5)"; }}
                            onMouseOut={e =>  { e.currentTarget.style.background = "transparent"; }}
                        >+</button>
                    </div>

                    <div style={S.actions}>
                        <button style={S.savePngBtn} onClick={handleSavePNG}>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            {t('Download')}
                        </button>
                        <button style={S.printBtn} onClick={handlePrint}>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                <polyline points="6 9 6 2 18 2 18 9"/>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                <rect x="6" y="14" width="12" height="8"/>
                            </svg>
                            {t('Confirm')} / {t('Print')}
                        </button>
                    </div>
                </div>

                {/* ════════════  THERMAL RECEIPT + ZOOM  ════════════
                    - No className on this wrapper → NOT hidden by .no-print
                    - body * { visibility:hidden } hides it on print
                    - #transfer-receipt-root { visibility:visible } overrides that
                    - transform:none in print CSS = zoom never affects print
                    - minHeight = natural height × zoom = footer never overlaps
                ══════════════════════════════════════════════════════ */}
                <div style={{
                    minHeight: 620 * zoom,
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "flex-start",
                    overflow: "visible",
                    transition: "min-height .2s",
                }}>
                    <div
                        id="transfer-receipt-root"
                        ref={receiptRef}
                        style={{
                            width: 300,
                            background: "#fff",
                            borderRadius: 4,
                            padding: "18px 18px 22px",
                            boxShadow: "0 20px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(91,45,142,0.27)",
                            fontFamily: "'Courier New', Courier, monospace",
                            fontSize: 11,
                            color: "#111",
                            position: "relative",
                            transform: `scale(${zoom})`,
                            transformOrigin: "top center",
                            transition: "transform .15s ease",
                            flexShrink: 0,
                        }}
                    >
                        {/* Top purple strip */}
                        <div style={{
                            position: "absolute", top: 0, left: 0, right: 0, height: 4,
                            background: "linear-gradient(90deg,#5B2D8E,#9B59B6)",
                            borderRadius: "4px 4px 0 0",
                        }} />

                        {/* Store header */}
                        <div style={{ textAlign: "center", marginTop: 10, marginBottom: 10 }}>
                            <div style={{ fontSize: 15, fontWeight: 700, letterSpacing: 2, color: "#5B2D8E" }}>
                                G+ Services
                            </div>
                            <div style={{ fontSize: 9, color: "#666", lineHeight: 1.55, marginTop: 3 }}>
                                {t("Money Transfer")}<br />
                                Aria Thmey, PoiPet<br />
                                Banteay Meanchey Province
                            </div>
                        </div>

                        <RDiv />

                        <div style={{ textAlign: "center", fontWeight: 700, fontSize: 11.5,
                            letterSpacing: 1.5, color: "#5B2D8E", marginBottom: 7 }}>
                            ───{" "}
                            {inv.transfer_type === "Transfer-IN"
                                ? t("TRANSFER-IN RECEIPT")
                                : t("TRANSFER-OUT RECEIPT")}
                            {" "}───
                        </div>

                        <RRow label={t("Invoice")} value={inv.invoice_number ?? "—"} bold />
                        <RRow label={t("Date")}    value={fmtDate(inv.created_at)} />
                        <RRow label={t("Time")}    value={fmtTime(inv.created_at)} />
                        <RRow label={t("Type")}    value={t(inv.transfer_type ?? "—")} />

                        <RDiv />

                        <div style={{ fontWeight: 700, fontSize: 9.5, color: "#5B2D8E", marginBottom: 4, letterSpacing: 1 }}>
                            {t("Customer Information")}
                        </div>
                        <RRow label={t("Name")}         value={inv.customer_name || "—"} />
                        <RRow label={t("Phone Number")} value={inv.phone || "—"} />

                        <RDiv dashed />

                        <div style={{ fontWeight: 700, fontSize: 9.5, color: "#5B2D8E", marginBottom: 4, letterSpacing: 1 }}>
                            {t("From")}
                        </div>
                        <RRow label={t("Bank Name")}      value={inv.bank_name   || "—"} />
                        <RRow label={t("Account Name")}   value={inv.acc_name    || "—"} />
                        <RRow label={t("Account Number")} value={inv.acc_number  || "—"} />

                        <RDiv />

                        <RRow label={t("Entered Amount")} value={`฿ ${fmt(inv.entered_amount)}`} />
                        <RRow
                            label={`${t("Transfer Fee")} (${inv.trf_fee_in_persentage ?? 0}%)`}
                            value={`฿ ${fmt(inv.trf_fee)}`}
                            valueColor="#dc2626"
                        />

                        {/* Grand total */}
                        <div style={{
                            display: "flex", justifyContent: "space-between",
                            fontWeight: 700, fontSize: 13,
                            marginTop: 6, padding: "6px 8px",
                            background: "#F5F0FA", borderLeft: "3px solid #5B2D8E",
                            borderRadius: 2, color: "#5B2D8E",
                        }}>
                            <span>{t("NET AMOUNT")}</span>
                            <span>฿ {fmt(inv.net_amount)}</span>
                        </div>

                        <RDiv dashed />

                        <div style={{ textAlign: "center", fontSize: 9, color: "#888", lineHeight: 1.8, marginTop: 6 }}>
                            {t("Signature")} &amp; {t("Name of Staff")}<br />
                            <div style={{ borderTop: "1px solid #ccc", width: 100, margin: "5px auto 6px" }} />
                            <span style={{ color: "#9B59B6", fontWeight: 700 }}>{t("Thank you")}!</span><br />
                            {t("Please keep this receipt for your records")}.
                        </div>

                        {/* Bottom strip */}
                        <div style={{
                            position: "absolute", bottom: 0, left: 0, right: 0, height: 3,
                            background: "linear-gradient(90deg,#9B59B6,#5B2D8E)",
                            borderRadius: "0 0 4px 4px",
                        }} />
                    </div>
                </div>

                <div style={{ height: 40 }} />
            </div>
        </>
    );
}

/* ── Row helper — same structure as ShowMoneyExchangeInvoices RRow ── */
function RDiv({ dashed }) {
    return <div style={{ borderTop: dashed ? "1px dashed #ccc" : "1px solid #bbb", margin: "7px 0" }} />;
}
function RRow({ label, value, bold, valueColor }) {
    return (
        <div style={{ display: "flex", justifyContent: "space-between", fontSize: 10.5, marginBottom: 2, fontWeight: bold ? 700 : 400 }}>
            <span style={{ color: "#666" }}>{label}:</span>
            <span style={{ maxWidth: "62%", textAlign: "right", wordBreak: "break-word", color: valueColor ?? "#111" }}>{value}</span>
        </div>
    );
}

/* ── Styles ── */
const S = {
    page: {
        minHeight: "100vh",
        background: "linear-gradient(160deg,#120720 0%,#1A0A2E 55%,#2D1060 100%)",
        fontFamily: "'Segoe UI', sans-serif",
        padding: "28px 16px 60px",
    },
    topBar: {
        display: "flex", alignItems: "center", gap: 10, flexWrap: "wrap",
        maxWidth: 660, margin: "0 auto 22px",
    },
    backBtn: {
        display: "inline-flex", alignItems: "center", gap: 6,
        padding: "8px 16px", borderRadius: 8,
        border: "1px solid rgba(155,89,182,0.28)",
        color: "#D4A8F0", fontSize: 13, fontWeight: 500,
        textDecoration: "none",
        background: "transparent",
        transition: "background .15s",
    },
    breadcrumb: {
        color: "#8B6BAE",
        fontSize: 13,
        flex: 1,
    },
    actions: { display: "flex", gap: 10 },
    savePngBtn: {
        display: "inline-flex", alignItems: "center", gap: 7,
        padding: "8px 16px", borderRadius: 8,
        background: "#5B2D8E", color: "#fff",
        border: "none", fontSize: 12, fontWeight: 600,
        cursor: "pointer",
    },
    printBtn: {
        display: "inline-flex", alignItems: "center", gap: 7,
        padding: "8px 16px", borderRadius: 8,
        background: "#6D28D9", color: "#fff",
        border: "none", fontSize: 12, fontWeight: 600,
        cursor: "pointer",
    },
};