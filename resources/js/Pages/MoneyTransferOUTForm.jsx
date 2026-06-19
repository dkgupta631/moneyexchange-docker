import { useState, useEffect, useRef } from "react";
import { useForm, Head, usePage } from "@inertiajs/react";

// Bank list — filenames exactly as seen in the bank-images folder
const BANKS = [
    { symbol: "BBL",    value: "002", name: "Bangkok Bank",                         file: "BBL.png"    },
    { symbol: "KBANK",  value: "004", name: "Kasikorn Bank",                        file: "KBANK.png"  },
    { symbol: "KTB",    value: "006", name: "Krungthai Bank",                       file: "KTB.png"    },
    { symbol: "TMB",    value: "011", name: "TMB Thanachart Bank",                  file: "TMB.png"    },
    { symbol: "SCB",    value: "014", name: "Siam Commercial Bank",                 file: "SCB.jpg"    },
    { symbol: "CITI",   value: "017", name: "Citibank N.A.",                        file: "CITI.jpg"   },
    { symbol: "SCBT",   value: "020", name: "Standard Chartered Bank (Thailand)",   file: "SCBT.png"   },
    { symbol: "CIMB",   value: "022", name: "CIMB Thai Bank",                       file: "CIMB.png"   },
    { symbol: "UOB",    value: "024", name: "UOB Bank",                              file: "UOB.png"    },
    { symbol: "BAY",    value: "025", name: "Bank of Ayudhya",                      file: "BAY.jpg"    },
    { symbol: "GOV",    value: "030", name: "Government Savings Bank",              file: "GOV.jpg"    },
    { symbol: "GHB",    value: "033", name: "Government Housing Bank",              file: "GHB.jpg"    },
    { symbol: "AGRI",   value: "034", name: "Bank for Agriculture (BAAC)",          file: "AGRI.png"   },
    { symbol: "ISBT",   value: "066", name: "Islamic Bank of Thailand",             file: "ISBT.jpg"   },
    { symbol: "TISCO",  value: "067", name: "TISCO Bank",                           file: "TISCO.png"  },
    { symbol: "KK",     value: "069", name: "Kiatnakin Bank",                       file: "KK.jpg"     },
    { symbol: "ACL",    value: "070", name: "ACL (ACLEDA) Bank",                   file: "ACL.png"    },
    { symbol: "TCRB",   value: "071", name: "Thai Credit Retail Bank",             file: "TCRB.png"   },
    { symbol: "LHBANK", value: "073", name: "Land and House Bank",                 file: "LHBANK.jpg" },
];

const AMOUNT_SUGGESTIONS = [
    500, 1000, 1500, 2000, 2500, 3000, 4000, 5000,
    7500, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000,
];

/* Real bank image — falls back to a coloured initial circle if the image fails */
function BankImg({ bank, size, appUrl }) {
    const sz = size || 34;
    const [errored, setErrored] = useState(false);
    if (errored) {
        return (
            <div style={{
                width: sz, height: sz, borderRadius: "50%",
                background: "linear-gradient(135deg, #4a2280, #9B59B6)",
                display: "flex", alignItems: "center", justifyContent: "center",
                flexShrink: 0,
                fontSize: Math.max(8, sz * 0.28) + "px",
                fontWeight: "800", color: "#fff", letterSpacing: "-0.5px",
            }}>
                {bank.symbol.slice(0, 3)}
            </div>
        );
    }
    return (
        <img
            src={appUrl + "/website/assets/bank-images/" + bank.file}
            alt={bank.name}
            onError={() => setErrored(true)}
            style={{
                width: sz, height: sz,
                borderRadius: "50%",
                objectFit: "cover",
                flexShrink: 0,
                borderWidth: "2px",
                borderStyle: "solid",
                borderColor: "rgba(91,45,142,0.15)",
                background: "#f8f5ff",
            }}
        />
    );
}

/* ── Fee Tier Badge ── */
function FeeTierBadge({ amount, bigFee, littleFee, t }) {
    const num = parseFloat(amount);
    if (!num || num <= 0) return null;
    const isBig = num >= 100000;
    return (
        <div style={{
            display: "inline-flex", alignItems: "center", gap: "6px",
            background: isBig ? "rgba(22,163,74,.09)" : "rgba(220,38,38,.08)",
            color: isBig ? "#16a34a" : "#dc2626",
            borderWidth: "1px", borderStyle: "solid",
            borderColor: isBig ? "rgba(22,163,74,.25)" : "rgba(220,38,38,.20)",
            borderRadius: "999px", padding: "3px 11px", fontSize: "10.5px", fontWeight: "700"
        }}>
            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            {isBig
                ? `${bigFee}% ${t('fee')} (≥ ฿100,000)`
                : `${littleFee}% ${t('fee')} (< ฿100,000)`
            }
        </div>
    );
}

/* ══════════════════════════════════════════════
   CONFIRMATION POPUP
══════════════════════════════════════════════ */
function ConfirmPopup({ data, summary, selectedBank, feeMode, feePercentage, onReconfirm, onBack, processing, t, appUrl, formatAccNumber, fmt }) {
    const noFee = feeMode === "no-fee";

    useEffect(() => {
        document.body.classList.add("modal-open");
        return () => document.body.classList.remove("modal-open");
    }, []);

    return (
        <div style={P.overlay}>
            <div style={P.backdrop} onClick={onBack} />
            <div style={P.modal}>
                {/* Glow decoration */}
                <div style={P.modalGlow} />

                {/* Header */}
                <div style={P.modalHeader}>
                    <div style={P.modalHeaderGlow} />
                    <div style={P.modalIconWrap}>
                        <div style={P.modalIcon}>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/>
                            </svg>
                        </div>
                    </div>
                    <h2 style={P.modalTitle}>{t("Confirm Transfer")}</h2>
                    <p style={P.modalSubtitle}>{t("Please review your transfer details carefully before confirming")}</p>
                    <button type="button" style={P.closeBtn} onClick={onBack}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                {/* Body */}
                <div style={P.modalBody} className="modal-body-scroll">

                    {/* Transfer Type Badge */}
                    <div style={P.typeBadgeRow}>
                        <span style={P.typeBadge}>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                <line x1="12" y1="19" x2="12" y2="5"/>
                                <polyline points="5 12 12 5 19 12"/>
                            </svg>
                            {t("Transfer")} — OUT
                        </span>
                        <span style={noFee ? P.feeBadgeGreen : P.feeBadgePurple}>
                            {noFee ? t("Fee Paid in Cash") : (feePercentage + "% " + t("Fee Applied"))}
                        </span>
                    </div>

                    {/* Sender Section */}
                    {(data.customer_name || data.phone) && (
                        <div style={P.section}>
                            <div style={P.sectionHead}>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7c5cbf" strokeWidth="2.2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span style={P.sectionTitle}>{t("Sender Information")}</span>
                            </div>
                            <div style={P.sectionBody}>
                                {data.customer_name ? (
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t("Customer Name")}</span>
                                        <span style={P.rowValue}>{data.customer_name}</span>
                                    </div>
                                ) : null}
                                {data.phone ? (
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t("Phone")}</span>
                                        <span style={P.rowValue}>{data.phone}</span>
                                    </div>
                                ) : null}
                            </div>
                        </div>
                    )}

                    {/* Recipient Section */}
                    <div style={P.section}>
                        <div style={P.sectionHead}>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7c5cbf" strokeWidth="2.2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span style={P.sectionTitle}>{t("Recipient Details")}</span>
                        </div>
                        <div style={P.sectionBody}>
                            {selectedBank && (
                                <div style={P.bankPreviewRow}>
                                    <BankImg bank={selectedBank} size={46} appUrl={appUrl} />
                                    <div style={P.bankPreviewInfo}>
                                        <span style={P.bankPreviewName}>{selectedBank.name}</span>
                                        <span style={P.bankPreviewCode}>{selectedBank.symbol} &middot; Code {selectedBank.value}</span>
                                    </div>
                                </div>
                            )}
                            <div style={P.rowDivider} />
                            <div style={P.row}>
                                <span style={P.rowLabel}>{t("Account Name")}</span>
                                <span style={P.rowValue}>{data.acc_name}</span>
                            </div>
                            <div style={P.row}>
                                <span style={P.rowLabel}>{t("Account Number")}</span>
                                <span style={P.rowValueMono}>{formatAccNumber(data.acc_number)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Amount Section */}
                    <div style={P.section}>
                        <div style={P.sectionHead}>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7c5cbf" strokeWidth="2.2">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            <span style={P.sectionTitle}>{t("Transfer Summary")}</span>
                        </div>
                        <div style={P.sectionBody}>
                            {summary && (
                                <>
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t("Entered Amount")}</span>
                                        <span style={P.rowValue}>{"฿" + fmt(summary.entered) + " THB"}</span>
                                    </div>
                                    {/* Fee Tier indicator */}
                                    <div style={{ ...P.row, justifyContent: "flex-start", gap: "8px" }}>
                                        <span style={P.rowLabel}>{t("Fee Tier")}</span>
                                        <span style={{
                                            fontSize: "10.5px", fontWeight: "700", padding: "2px 10px", borderRadius: "999px",
                                            background: summary.entered >= 100000 ? "rgba(22,163,74,.09)" : "rgba(220,38,38,.08)",
                                            color: summary.entered >= 100000 ? "#16a34a" : "#dc2626",
                                            borderWidth: "1px", borderStyle: "solid",
                                            borderColor: summary.entered >= 100000 ? "rgba(22,163,74,.25)" : "rgba(220,38,38,.20)"
                                        }}>
                                            {summary.entered >= 100000 ? `≥ ฿100,000 → ${feePercentage}%` : `< ฿100,000 → ${feePercentage}%`}
                                        </span>
                                    </div>
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t("Transfer Fee") + " (" + summary.feePercentage + "%)"}</span>
                                        <span style={noFee ? P.rowValueGreen : P.rowValueRed}>
                                            {summary.fee === 0
                                                ? "฿0.00 THB"
                                                : noFee
                                                    ? ("+ ฿" + fmt(summary.fee) + " THB")
                                                    : ("− ฿" + fmt(summary.fee) + " THB")
                                            }
                                        </span>
                                    </div>
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t("Fee Mode")}</span>
                                        <span style={noFee ? P.modeBadgeGreen : P.modeBadgePurple}>
                                            {noFee ? t("Fee Paid in Cash") : (feePercentage + "% " + t("Deducted"))}
                                        </span>
                                    </div>
                                    <div style={P.amountDivider} />
                                    <div style={P.netRow}>
                                        <span style={P.netLabel}>{t("Net Receive Amount")}</span>
                                        <span style={P.netValue}>{"฿" + fmt(summary.net) + " THB"}</span>
                                    </div>
                                    {noFee && summary.fee > 0 && (
                                        <div style={P.cashNote}>
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" strokeWidth="2.5">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                            {t("Customer pays fee separately in cash")}
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                        {/* Warning note */}
                        <div style={P.warningNote}>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#b45309" strokeWidth="2.2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                            <span>{t("Once confirmed, this transfer will be processed. Please verify all details carefully.")}</span>
                        </div>
                    </div>

                </div>

                {/* Footer Buttons */}
                <div style={P.modalFooter}>
                    <button type="button" style={P.backBtn} onClick={onBack}>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.3">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                        {t("Back")}
                    </button>
                    <button type="button" style={processing ? P.reconfirmBtnDisabled : P.reconfirmBtn} onClick={onReconfirm} disabled={processing}>
                        {processing ? (
                            <>
                                <span style={P.spin} />
                                {t("Processing") + "…"}
                            </>
                        ) : (
                            <>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.3">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                {t("Reconfirm") + " & " + t("Print")}
                            </>
                        )}
                    </button>
                </div>
            </div>
        </div>
    );
}

export default function MoneyTransferOUTForm({ gettransferchanges }) {
    const { appUrl, translations } = usePage().props;
    const t = (key) => translations?.[key] ?? key;

    // ── Fee tiers from DB ──────────────────────────────────────────────
    // Controller passes: { big_amount: 1, little_amount: 2 }
    const bigAmountFee    = parseFloat(gettransferchanges?.big_amount    ?? 1);
    const littleAmountFee = parseFloat(gettransferchanges?.little_amount ?? 2);

    const [feeMode, setFeeMode]         = useState("with-fee");
    const [showConfirm, setShowConfirm] = useState(false);

    // Dynamically computed fee percentage based on entered amount
    const [feePercentage, setFeePercentage] = useState(littleAmountFee);

    const { data, setData, post, processing, errors } = useForm({
        customer_name:         "",
        phone:                 "",
        bank_name:             "",
        bank_symbol:           "",
        acc_name:              "",
        acc_number:            "",
        entered_amount:        "",
        transfer_type:         "Transfer-OUT",
        currency:              "฿",
        trf_fee_in_persentage: feePercentage,
        trf_fee:               "",
        net_amount:            "",
    });

    const [summary,      setSummary]      = useState(null);
    const [amountError,  setAmountError]  = useState("");
    const [dropOpen,     setDropOpen]     = useState(false);
    const [selectedBank, setSelectedBank] = useState(null);
    const [search,       setSearch]       = useState("");
    const [accNameFocus, setAccNameFocus] = useState(false);
    const [accNumFocus,  setAccNumFocus]  = useState(false);
    const [accNameError, setAccNameError] = useState("");
    const [accNumError,  setAccNumError]  = useState("");
    const dropRef = useRef(null);

    useEffect(() => {
        const fn = (e) => {
            if (dropRef.current && !dropRef.current.contains(e.target)) {
                setDropOpen(false);
                setSearch("");
            }
        };
        document.addEventListener("mousedown", fn);
        return () => document.removeEventListener("mousedown", fn);
    }, []);

    // ── Recalculate whenever amount or feeMode changes ─────────────────
    useEffect(() => {
        const amount = parseFloat(data.entered_amount);
        if (!isNaN(amount) && amount > 0) {
            // Determine tier: >= 100,000 → big (1%), < 100,000 → little (2%)
            const activeFee = amount >= 100000 ? bigAmountFee : littleAmountFee;
            setFeePercentage(activeFee);

            const feeAmount = parseFloat(((amount * activeFee) / 100).toFixed(2));
            const net = feeMode === "no-fee"
                ? amount
                : parseFloat((amount - feeAmount).toFixed(2));
            setSummary({ entered: amount, fee: feeAmount, net, feePercentage: activeFee, noFeeMode: feeMode === "no-fee" });
            setData(prev => ({
                ...prev,
                trf_fee_in_persentage: activeFee,
                trf_fee:               feeAmount.toFixed(2),
                net_amount:            net.toFixed(2),
            }));
        } else {
            setFeePercentage(littleAmountFee);
            setSummary(null);
            setData(prev => ({ ...prev, trf_fee_in_persentage: littleAmountFee, trf_fee: "0.00", net_amount: "" }));
        }
    }, [data.entered_amount, feeMode]);

    const validateAmount = (val) => {
        const num = parseFloat(val);
        if (isNaN(num) || val === "") { setAmountError(""); return; }
        if (num < 500)         setAmountError("Minimum amount is ฿500 THB");
        else if (num > 100000) setAmountError("Maximum amount is ฿100,000 THB");
        else                   setAmountError("");
    };

    const handleAccNameChange = (val) => {
        const cleaned = val.replace(/[^a-zA-ZÀ-ÿก-๙\s.\-']/g, "");
        setData("acc_name", cleaned);
        setAccNameError(val !== cleaned ? t("Account name must contain letters only") : "");
    };

    const handleAccNumberChange = (val) => {
        const cleaned = val.replace(/[^0-9]/g, "");
        setData("acc_number", cleaned);
        setAccNumError(val !== cleaned ? t("Account number must contain digits only") : "");
    };

    const formatAccNumber = (num) => {
        const d = num.replace(/\D/g, "");
        if (d.length <= 4)  return d;
        if (d.length <= 9)  return d.slice(0,4) + "-" + d.slice(4);
        return d.slice(0,4) + "-" + d.slice(4,9) + "-" + d.slice(9,10);
    };

    const handleBankSelect = (bank) => {
        setSelectedBank(bank);
        setData(prev => ({ ...prev, bank_name: bank.name, bank_symbol: bank.symbol }));
        setDropOpen(false);
        setSearch("");
    };

    const handleConfirmClick = (e) => {
        e.preventDefault();
        if (canSubmit) setShowConfirm(true);
    };

    const handleReconfirm = () => {
        post("/money-transfer-OUT/store");
    };

    const handleBack = () => {
        setShowConfirm(false);
    };

    const fmt = (v) => new Intl.NumberFormat("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v);

    const filteredBanks = BANKS.filter(b =>
        b.name.toLowerCase().includes(search.toLowerCase()) ||
        b.symbol.toLowerCase().includes(search.toLowerCase())
    );

    const canSubmit = !processing && !amountError && !accNameError && !accNumError
        && data.entered_amount && data.bank_name
        && data.acc_name.trim().length > 0
        && data.acc_number.trim().length > 0;

    const enteredNum = parseFloat(data.entered_amount) || 0;

    return (
        <>
            <Head title={t("Transfer-OUT")} />
            <div style={S.page}>
                <div style={S.orb1} /><div style={S.orb2} /><div style={S.orb3} />

                {/* ══ CONFIRMATION POPUP ══ */}
                {showConfirm && (
                    <ConfirmPopup
                        data={data}
                        summary={summary}
                        selectedBank={selectedBank}
                        feeMode={feeMode}
                        feePercentage={feePercentage}
                        onReconfirm={handleReconfirm}
                        onBack={handleBack}
                        processing={processing}
                        t={t}
                        appUrl={appUrl}
                        formatAccNumber={formatAccNumber}
                        fmt={fmt}
                    />
                )}

                <div style={S.card}>
                    {/* ══ HEADER ══ */}
                    <div style={S.header}>
                        <div style={S.headerGlow} />
                        <div style={S.hIconWrap}>
                            <div style={S.hIcon}>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                    <path d="M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                        </div>
                        <h1 style={S.hTitle}>G+ Services</h1>
                        <p style={S.hSub}>{t("Money Transfer")} — OUT</p>
                        <span style={S.hBadge}>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                            </svg>
                            {t("Transfer")}-OUT
                        </span>
                    </div>

                    <form onSubmit={handleConfirmClick} style={S.form}>
                        {/* ── Sender Row ── */}
                        <div style={S.sectionLabel}>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#7c5cbf" strokeWidth="2.2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>{t("Sender Information")}</span>
                        </div>

                        <div style={S.row}>
                            <Field label={t("Customer Name")} optional t={t} style={{ flex: 1 }} error={errors.customer_name}>
                                <input type="text" value={data.customer_name} onChange={e => setData("customer_name", e.target.value)} placeholder={t("Full name")} style={{ ...S.input, ...(errors.customer_name ? S.inputErr : {}) }} />
                            </Field>
                            <Field label={t("Phone")} optional t={t} style={{ flex: 1 }}>
                                <input type="text" value={data.phone} onChange={e => setData("phone", e.target.value.replace(/[^0-9\-+\s]/g, "").slice(0, 13))} placeholder="08x-xxx-xxxx" style={S.input} maxLength={13} />
                            </Field>
                        </div>

                        {/* ══ TO SECTION ══ */}
                        <div style={S.toSection}>
                            <div style={S.toHeader}>
                                <div style={S.toHeaderLeft}>
                                    <div style={S.toIconBox}>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span style={S.toTitle}>{t("Transfer To")}</span>
                                        <span style={S.toSubTitle}>{t("Recipient Details")}</span>
                                    </div>
                                </div>
                                <div style={S.toSecureBadge}>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    {t("Secure")}
                                </div>
                            </div>

                            <div style={S.toBody}>
                                {/* ── Bank Name dropdown ── */}
                                <div style={S.toFieldGroup} ref={dropRef}>
                                    <label style={S.toLabel}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                            <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                                        </svg>
                                        {t("Bank Name")} <span style={S.req}>*</span>
                                    </label>
                                    <div style={{ ...S.dropTrigger, ...(dropOpen ? S.dropTriggerOpen : {}), ...(errors.bank_name ? S.inputErr : {}) }} onClick={() => setDropOpen(o => !o)}>
                                        <div style={S.dropInner}>
                                            {selectedBank ? (
                                                <>
                                                    <BankImg bank={selectedBank} size={36} appUrl={appUrl} />
                                                    <div style={{ display: "flex", flexDirection: "column", gap: "1px", flex: 1 }}>
                                                        <span style={S.dropSelText}>{selectedBank.name}</span>
                                                        <span style={S.dropSelCode}>{selectedBank.symbol} · {selectedBank.value}</span>
                                                    </div>
                                                    <span style={S.dropBadge}>{selectedBank.symbol}</span>
                                                </>
                                            ) : (
                                                <>
                                                    <div style={S.dummyCircle}>
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9b7ec8" strokeWidth="2">
                                                            <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                                                        </svg>
                                                    </div>
                                                    <span style={S.dropPlh}>{t("Select a Bank")}</span>
                                                </>
                                            )}
                                        </div>
                                        <span style={{ ...S.chevron, transform: dropOpen ? "rotate(180deg)" : "rotate(0)" }}>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c5cbf" strokeWidth="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        </span>
                                    </div>

                                    {dropOpen && (
                                        <div style={S.dropPanel}>
                                            <div style={S.searchRow}>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9B59B6" strokeWidth="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                                <input autoFocus type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder={t("Search bank…")} style={S.searchInput} onClick={e => e.stopPropagation()} />
                                                {search && <button type="button" style={S.searchClear} onClick={e => { e.stopPropagation(); setSearch(""); }}>✕</button>}
                                            </div>
                                            <div style={S.dropScroll}>
                                                {filteredBanks.length === 0 ? (
                                                    <div style={S.noResult}>
                                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#c4b3d9" strokeWidth="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                                        <span>{t("No bank found")}</span>
                                                    </div>
                                                ) : filteredBanks.map(bank => (
                                                    <div key={bank.value} className="bank-row" style={{ ...S.dropItem, ...(selectedBank?.value === bank.value ? S.dropItemActive : {}) }} onClick={() => handleBankSelect(bank)}>
                                                        <BankImg bank={bank} size={40} appUrl={appUrl} />
                                                        <div style={S.bankMeta}>
                                                            <span style={S.bankNm}>{bank.name}</span>
                                                            <span style={S.bankCd}>{bank.symbol} · Code {bank.value}</span>
                                                        </div>
                                                        {selectedBank?.value === bank.value && (
                                                            <div style={S.tickCircle}>
                                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                            </div>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    {errors.bank_name && <span style={S.err}>{errors.bank_name}</span>}
                                </div>

                                {/* ── Divider ── */}
                                <div style={S.toDivider}>
                                    <div style={S.toDivLine} />
                                    <div style={S.toDivIcon}>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9b7ec8" strokeWidth="2.2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div style={S.toDivLine} />
                                </div>

                                {/* ── Account Name ── */}
                                <div style={S.toFieldGroup}>
                                    <label style={S.toLabel}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        {t("Account Name")} <span style={S.req}>*</span>
                                        <span style={S.fieldHint}>{t("Letters only")}</span>
                                    </label>
                                    <div style={{ position: "relative" }}>
                                        <div style={S.inputIconLeft}>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={accNameFocus ? "#5B2D8E" : "#b8a8ce"} strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <input type="text" value={data.acc_name} onFocus={() => setAccNameFocus(true)} onBlur={() => setAccNameFocus(false)} onChange={e => handleAccNameChange(e.target.value)} placeholder={t("e.g. Somchai Jaidee")} style={{ ...S.toInput, ...S.inputWithIcon, ...(accNameFocus ? S.toInputFocus : {}), ...(errors.acc_name || accNameError ? S.toInputErr : {}) }} autoComplete="off" inputMode="text" />
                                        {data.acc_name && !accNameError && !errors.acc_name && (
                                            <div style={S.inputIconRight}><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
                                        )}
                                    </div>
                                    {(errors.acc_name || accNameError) && (
                                        <span style={S.toErr}>
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                            {errors.acc_name || accNameError}
                                        </span>
                                    )}
                                </div>

                                {/* ── Account Number ── */}
                                <div style={S.toFieldGroup}>
                                    <label style={S.toLabel}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        {t("Account Number")} <span style={S.req}>*</span>
                                        <span style={S.fieldHint}>{t("Numbers only")}</span>
                                    </label>
                                    <div style={{ position: "relative" }}>
                                        <div style={S.inputIconLeft}>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={accNumFocus ? "#5B2D8E" : "#b8a8ce"} strokeWidth="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        </div>
                                        <input type="text" value={formatAccNumber(data.acc_number)} onFocus={() => setAccNumFocus(true)} onBlur={() => setAccNumFocus(false)} onChange={e => handleAccNumberChange(e.target.value)} placeholder="XXXX-XXXXX-X" maxLength={12} style={{ ...S.toInput, ...S.inputWithIcon, ...S.accNumFont, ...(accNumFocus ? S.toInputFocus : {}), ...(errors.acc_number || accNumError ? S.toInputErr : {}) }} inputMode="numeric" autoComplete="off" />
                                        {data.acc_number && !accNumError && !errors.acc_number && (
                                            <div style={S.inputIconRight}><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
                                        )}
                                    </div>
                                    {(errors.acc_number || accNumError) && (
                                        <span style={S.toErr}>
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                            {errors.acc_number || accNumError}
                                        </span>
                                    )}
                                </div>

                                {/* Recipient preview card */}
                                {selectedBank && data.acc_name && data.acc_number && !accNameError && !accNumError && (
                                    <div style={S.recipientCard}>
                                        <div style={S.recipientCardGlow} />
                                        <div style={S.recipientLeft}><BankImg bank={selectedBank} size={44} appUrl={appUrl} /></div>
                                        <div style={S.recipientInfo}>
                                            <span style={S.recipientName}>{data.acc_name}</span>
                                            <span style={S.recipientBank}>{selectedBank.name}</span>
                                            <span style={S.recipientNum}>{formatAccNumber(data.acc_number)}</span>
                                        </div>
                                        <div style={S.recipientCheck}>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* ── Transfer Amount Section ── */}
                        <div style={S.sectionLabel}><span>{t("Transfer Amount")}</span></div>

                        {/* ── Fee Tier Info Box ── */}
                        <div style={S.feeTierBox}>
                            <div style={S.feeTierHeader}>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#5B2D8E" strokeWidth="2.2">
                                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <span style={S.feeTierTitle}>{t("Fee Structure")}</span>
                            </div>
                            <div style={S.feeTierGrid}>
                                <div style={{ ...S.feeTierItem, ...(enteredNum > 0 && enteredNum < 100000 ? S.feeTierItemActive : {}) }}>
                                    <div style={S.feeTierRange}>{"< ฿100,000"}</div>
                                    <div style={S.feeTierPct}>{littleAmountFee}%</div>
                                    <div style={S.feeTierLabel}>{t("fee applied")}</div>
                                    {enteredNum > 0 && enteredNum < 100000 && <div style={S.feeTierActiveDot} />}
                                </div>
                                <div style={S.feeTierDivider}>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c4b3d9" strokeWidth="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </div>
                                <div style={{ ...S.feeTierItem, ...(enteredNum >= 100000 ? S.feeTierItemBigActive : {}) }}>
                                    <div style={S.feeTierRange}>{"≥ ฿100,000"}</div>
                                    <div style={{ ...S.feeTierPct, color: enteredNum >= 100000 ? "#16a34a" : "#5B2D8E" }}>{bigAmountFee}%</div>
                                    <div style={S.feeTierLabel}>{t("fee applied")}</div>
                                    {enteredNum >= 100000 && <div style={{ ...S.feeTierActiveDot, background: "#16a34a" }} />}
                                </div>
                            </div>
                            {enteredNum > 0 && !amountError && (
                                <div style={S.feeTierActive}>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {t("Current fee")}: <strong>{feePercentage}%</strong> ({enteredNum >= 100000 ? `≥ ฿100,000` : `< ฿100,000`})
                                </div>
                            )}
                        </div>

                        {/* ══ TRANSFER CHARGE TOGGLE ══ */}
                        <div style={S.feeToggleBox}>
                            <div style={S.feeToggleHeader}>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#5B2D8E" strokeWidth="2.2">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <span style={S.feeToggleTitle}>{t("Transfer Charge")}</span>
                            </div>
                            <div style={S.feeRadioRow}>
                                {/* Option 1: with-fee */}
                                <label style={{ ...S.feeRadioLabel, ...(feeMode === "with-fee" ? S.feeRadioLabelActive : {}) }}>
                                    <input type="radio" name="feeMode" value="with-fee" checked={feeMode === "with-fee"} onChange={() => setFeeMode("with-fee")} style={S.radioHidden} />
                                    <div style={{ ...S.radioCircle, ...(feeMode === "with-fee" ? S.radioCircleActive : {}) }}>
                                        {feeMode === "with-fee" && <div style={S.radioDot} />}
                                    </div>
                                    <div style={S.feeRadioText}>
                                        <span style={S.feeRadioMain}>{t("Transfer fee included")}</span>
                                        <span style={{ ...S.feeRadioSub, color: feeMode === "with-fee" ? "#9B59B6" : "#b8a8ce" }}>{feePercentage}% {t("deducted")}</span>
                                    </div>
                                    <span style={{
                                        ...S.feeBadge,
                                        background:        feeMode === "with-fee" ? "rgba(91,45,142,.12)" : "#f5f0fc",
                                        color:             feeMode === "with-fee" ? "#5B2D8E"             : "#b8a8ce",
                                        borderTopColor:    feeMode === "with-fee" ? "rgba(91,45,142,.3)"  : "transparent",
                                        borderRightColor:  feeMode === "with-fee" ? "rgba(91,45,142,.3)"  : "transparent",
                                        borderBottomColor: feeMode === "with-fee" ? "rgba(91,45,142,.3)"  : "transparent",
                                        borderLeftColor:   feeMode === "with-fee" ? "rgba(91,45,142,.3)"  : "transparent",
                                    }}>{feePercentage}%</span>
                                </label>

                                <div style={S.feeRadioDivider} />

                                {/* Option 2: no-fee */}
                                <label style={{ ...S.feeRadioLabel, ...(feeMode === "no-fee" ? S.feeRadioLabelNoFeeActive : {}) }}>
                                    <input type="radio" name="feeMode" value="no-fee" checked={feeMode === "no-fee"} onChange={() => setFeeMode("no-fee")} style={S.radioHidden} />
                                    <div style={{ ...S.radioCircle, ...(feeMode === "no-fee" ? S.radioCircleNoFeeActive : {}) }}>
                                        {feeMode === "no-fee" && <div style={S.radioDotGreen} />}
                                    </div>
                                    <div style={S.feeRadioText}>
                                        <span style={S.feeRadioMain}>{t("Transfer fee excluded")}</span>
                                        <span style={{ ...S.feeRadioSub, color: feeMode === "no-fee" ? "#16a34a" : "#b8a8ce" }}>{t("fee paid in separately")}</span>
                                    </div>
                                    <span style={{
                                        ...S.feeBadge,
                                        background:        feeMode === "no-fee" ? "rgba(22,163,74,.10)" : "#f5f0fc",
                                        color:             feeMode === "no-fee" ? "#16a34a"             : "#b8a8ce",
                                        borderTopColor:    feeMode === "no-fee" ? "rgba(22,163,74,.3)"  : "transparent",
                                        borderRightColor:  feeMode === "no-fee" ? "rgba(22,163,74,.3)"  : "transparent",
                                        borderBottomColor: feeMode === "no-fee" ? "rgba(22,163,74,.3)"  : "transparent",
                                        borderLeftColor:   feeMode === "no-fee" ? "rgba(22,163,74,.3)"  : "transparent",
                                    }}>Cash</span>
                                </label>
                            </div>
                        </div>

                        {/* ── Amount Input ── */}
                        <div style={S.fieldGroup}>
                            <label style={S.label}>
                                {t("Amount")} (THB) <span style={S.req}>*</span>
                                {enteredNum > 0 && !amountError && (
                                    <span style={{ marginLeft: "auto" }}>
                                        <FeeTierBadge amount={data.entered_amount} bigFee={bigAmountFee} littleFee={littleAmountFee} t={t} />
                                    </span>
                                )}
                            </label>
                            <div style={S.amtWrap}>
                                <span style={S.amtPfx}>฿</span>
                                <input type="number" list="amt-list" value={data.entered_amount} onChange={e => { setData("entered_amount", e.target.value); validateAmount(e.target.value); }} placeholder="500 – 100,000" min="500" max="100000" step="0.01" style={{ ...S.input, ...S.amtInput, ...(errors.entered_amount || amountError ? S.inputErr : {}) }} />
                                <datalist id="amt-list">{AMOUNT_SUGGESTIONS.map(v => <option key={v} value={v} />)}</datalist>
                            </div>
                            {(errors.entered_amount || amountError) && <span style={S.err}>{errors.entered_amount || amountError}</span>}
                            <div style={S.pills}>
                                {[500, 1000, 2000, 5000, 10000, 20000].map(v => (
                                    <button key={v} type="button" style={{ ...S.pill, ...(parseFloat(data.entered_amount) === v ? S.pillOn : {}) }} onClick={() => { setData("entered_amount", String(v)); validateAmount(String(v)); }}>
                                        ฿{v.toLocaleString()}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* ── Transfer Summary ── */}
                        {summary && !amountError && (
                            <div style={S.summary}>
                                <div style={S.sumHead}>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#5B2D8E" strokeWidth="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                    <span style={S.sumTitle}>{t("Transfer Summary")}</span>
                                    <span style={{
                                        marginLeft: "auto", fontSize: "10px", fontWeight: "700",
                                        padding: "2px 9px", borderRadius: "999px",
                                        background: summary.noFeeMode ? "rgba(22,163,74,.10)" : "rgba(91,45,142,.10)",
                                        color: summary.noFeeMode ? "#16a34a" : "#5B2D8E",
                                        borderWidth: "1px", borderStyle: "solid",
                                        borderColor: summary.noFeeMode ? "rgba(22,163,74,.25)" : "rgba(91,45,142,.20)",
                                    }}>
                                        {summary.noFeeMode ? t("Fee Paid in Cash") : (feePercentage + "% " + t("Fee Applied"))}
                                    </span>
                                </div>
                                <div style={S.div} />
                                <SR label={t("Entered Amount")} val={"฿" + fmt(summary.entered) + " THB"} />
                                <SR
                                    label={t("Transfer Fee") + " (" + summary.feePercentage + "%)"}
                                    val={summary.fee === 0 ? "฿0.00 THB" : summary.noFeeMode ? ("+ ฿" + fmt(summary.fee) + " THB") : ("− ฿" + fmt(summary.fee) + " THB")}
                                    red={!summary.noFeeMode && summary.fee > 0}
                                    green={summary.noFeeMode && summary.fee > 0}
                                />
                                {summary.noFeeMode && summary.fee > 0 && (
                                    <div style={S.cashFeeNote}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {t("Customer pays fee in separately")}
                                    </div>
                                )}
                                <div style={S.div} />
                                <div style={{ ...S.sRow, paddingTop: 5 }}>
                                    <span style={S.sumTotalLbl}>{t("Net Receive Amount")}</span>
                                    <span style={S.sumTotal}>{"฿" + fmt(summary.net) + " THB"}</span>
                                </div>
                            </div>
                        )}

                        {/* ── Submit ── */}
                        <button type="submit" disabled={!canSubmit} style={{ ...S.btn, ...(!canSubmit ? S.btnOff : {}) }}>
                            {processing ? (
                                <><span style={S.spin} /> {t("Processing")}…</>
                            ) : (
                                <>
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    {t("Confirm")} / {t("Print")}
                                </>
                            )}
                        </button>
                    </form>
                </div>

                <style>{`
                    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap');
                    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                    input[type=number]::-webkit-inner-spin-button,
                    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
                    input:focus { outline: none; }
                    input::placeholder { color: #c4b3d9; }
                    .bank-row:hover { background: #f3ecfc !important; }
                    .fee-radio-label:hover { background: #f8f4fd !important; }
                    @keyframes fadeUp  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
                    @keyframes popDown { from { opacity:0; transform:scaleY(.92) translateY(-6px); } to { opacity:1; transform:scaleY(1) translateY(0); } }
                    @keyframes spin    { to { transform:rotate(360deg); } }
                    @keyframes cardIn  { from{opacity:0;transform:scale(.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
                    @keyframes overlayIn { from{opacity:0} to{opacity:1} }
                    @keyframes modalIn { from{opacity:0;transform:scale(.94) translateY(24px)} to{opacity:1;transform:scale(1) translateY(0)} }
                    .modal-body-scroll::-webkit-scrollbar { display: none; }
                    body.modal-open { overflow: hidden !important; }
                `}</style>
            </div>
        </>
    );
}

function Field({ label, required, optional, t, error, children, style }) {
    return (
        <div style={{ ...S.fieldGroup, ...style }}>
            <label style={S.label}>
                {label}
                {required && <span style={S.req}> *</span>}
                {optional && <span style={S.opt}> ({t("optional")})</span>}
            </label>
            {children}
            {error && <span style={S.err}>{error}</span>}
        </div>
    );
}

function SR({ label, val, red, green }) {
    return (
        <div style={S.sRow}>
            <span style={S.sLbl}>{label}</span>
            <span style={{ ...S.sVal, ...(red ? { color: "#dc2626", fontWeight: 700 } : {}), ...(green ? { color: "#16a34a", fontWeight: 700 } : {}) }}>{val}</span>
        </div>
    );
}

/* ══════════════════════════════════════════
   POPUP STYLES  (P)
══════════════════════════════════════════ */
const P = {
    overlay: { position: "fixed", inset: 0, zIndex: 9999, display: "flex", alignItems: "center", justifyContent: "center", padding: "16px", animation: "overlayIn .2s ease both" },
    backdrop: { position: "absolute", inset: 0, background: "rgba(5,1,15,.90)", backdropFilter: "blur(18px) brightness(0.2) saturate(0.5)", WebkitBackdropFilter: "blur(18px) brightness(0.2) saturate(0.5)", cursor: "pointer" },
    modal: { position: "relative", background: "#fff", borderRadius: "24px", width: "100%", maxWidth: "520px", maxHeight: "92vh", display: "flex", flexDirection: "column", overflow: "hidden", boxShadow: "0 32px 80px rgba(45,16,96,.28), 0 8px 24px rgba(0,0,0,.12)", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.12)", animation: "modalIn .3s cubic-bezier(.22,.68,0,1.2) both" },
    modalGlow: { position: "absolute", top: "-60px", right: "-60px", width: "200px", height: "200px", borderRadius: "50%", background: "radial-gradient(circle, rgba(155,89,182,.18) 0%, transparent 70%)", pointerEvents: "none", zIndex: 0 },
    modalHeader: { background: "linear-gradient(140deg, #2d1060 0%, #4a2280 45%, #7B3FBE 80%, #9B59B6 100%)", borderTopLeftRadius: "22px", borderTopRightRadius: "22px", padding: "28px 28px 24px", textAlign: "center", position: "relative", overflow: "hidden" },
    modalHeaderGlow: { position: "absolute", inset: 0, background: "radial-gradient(ellipse at 50% 0%, rgba(200,150,255,.22) 0%, transparent 60%)", pointerEvents: "none" },
    modalIconWrap: { position: "relative", zIndex: 1, marginBottom: "10px" },
    modalIcon: { width: "48px", height: "48px", borderRadius: "14px", background: "rgba(255,255,255,.18)", display: "flex", alignItems: "center", justifyContent: "center", margin: "0 auto", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(255,255,255,.28)", boxShadow: "0 4px 16px rgba(0,0,0,.18)" },
    modalTitle: { fontFamily: "'DM Sans', sans-serif", fontSize: "20px", fontWeight: "800", color: "#fff", marginBottom: "5px", position: "relative", zIndex: 1 },
    modalSubtitle: { fontSize: "12px", color: "rgba(255,255,255,.68)", position: "relative", zIndex: 1, lineHeight: "1.5" },
    closeBtn: { position: "absolute", top: "14px", right: "14px", background: "rgba(255,255,255,.15)", border: "none", borderRadius: "50%", width: "32px", height: "32px", display: "flex", alignItems: "center", justifyContent: "center", color: "rgba(255,255,255,.85)", cursor: "pointer", zIndex: 2, transition: "background .15s" },
    modalBody: { padding: "20px 22px", display: "flex", flexDirection: "column", gap: "14px", overflowY: "auto", flex: 1, scrollbarWidth: "none", msOverflowStyle: "none" },
    typeBadgeRow: { display: "flex", alignItems: "center", justifyContent: "space-between", gap: "8px" },
    typeBadge: { display: "inline-flex", alignItems: "center", gap: "5px", background: "rgba(91,45,142,.09)", color: "#5B2D8E", borderRadius: "999px", padding: "4px 12px", fontSize: "11px", fontWeight: "700", letterSpacing: ".5px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(91,45,142,.2)" },
    feeBadgePurple: { display: "inline-flex", alignItems: "center", gap: "5px", background: "rgba(91,45,142,.09)", color: "#5B2D8E", borderRadius: "999px", padding: "4px 12px", fontSize: "11px", fontWeight: "700", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(91,45,142,.2)" },
    feeBadgeGreen:  { display: "inline-flex", alignItems: "center", gap: "5px", background: "rgba(22,163,74,.09)", color: "#16a34a", borderRadius: "999px", padding: "4px 12px", fontSize: "11px", fontWeight: "700", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(22,163,74,.2)" },
    section: { background: "linear-gradient(145deg, #faf7ff 0%, #f5f0fc 100%)", borderRadius: "14px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.11)" },
    sectionHead: { display: "flex", alignItems: "center", gap: "7px", padding: "10px 14px 9px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(91,45,142,.09)", background: "linear-gradient(135deg, rgba(74,34,128,.05) 0%, rgba(155,89,182,.05) 100%)" },
    sectionTitle: { fontSize: "11px", fontWeight: "800", color: "#4a2280", letterSpacing: ".4px", textTransform: "uppercase" },
    sectionBody: { padding: "12px 14px", display: "flex", flexDirection: "column", gap: "9px" },
    bankPreviewRow: { display: "flex", alignItems: "center", gap: "12px", padding: "8px 0 4px" },
    bankPreviewInfo: { display: "flex", flexDirection: "column", gap: "2px" },
    bankPreviewName: { fontSize: "14px", fontWeight: "700", color: "#1A0A2E" },
    bankPreviewCode: { fontSize: "11px", color: "#9B59B6" },
    rowDivider: { height: "1px", background: "rgba(91,45,142,.10)", margin: "2px 0" },
    row: { display: "flex", justifyContent: "space-between", alignItems: "center", gap: "8px" },
    rowLabel: { fontSize: "12px", color: "#7a5a9a", fontWeight: "600", flexShrink: 0 },
    rowValue: { fontSize: "13px", fontWeight: "700", color: "#1A0A2E", textAlign: "right" },
    rowValueMono: { fontSize: "13px", fontWeight: "700", color: "#1A0A2E", textAlign: "right", fontFamily: "'IBM Plex Mono', monospace", letterSpacing: ".8px" },
    rowValueRed:   { fontSize: "13px", fontWeight: "700", color: "#dc2626", textAlign: "right" },
    rowValueGreen: { fontSize: "13px", fontWeight: "700", color: "#16a34a", textAlign: "right" },
    modeBadgePurple: { fontSize: "11px", fontWeight: "700", padding: "2px 10px", borderRadius: "999px", background: "rgba(91,45,142,.09)", color: "#5B2D8E", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(91,45,142,.2)" },
    modeBadgeGreen:  { fontSize: "11px", fontWeight: "700", padding: "2px 10px", borderRadius: "999px", background: "rgba(22,163,74,.09)", color: "#16a34a", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(22,163,74,.2)" },
    amountDivider: { height: "1px", background: "rgba(91,45,142,.13)", margin: "4px 0" },
    netRow: { display: "flex", justifyContent: "space-between", alignItems: "center", paddingTop: "2px" },
    netLabel: { fontSize: "13.5px", fontWeight: "800", color: "#1A0A2E" },
    netValue: { fontSize: "22px", fontWeight: "800", color: "#5B2D8E", letterSpacing: "-.5px" },
    cashNote: { display: "flex", alignItems: "center", gap: "6px", fontSize: "11px", color: "#16a34a", fontWeight: "600", background: "rgba(22,163,74,.07)", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(22,163,74,.18)", borderRadius: "8px", padding: "6px 10px" },
    warningNote: { display: "flex", alignItems: "flex-start", gap: "8px", background: "rgba(251,191,36,.08)", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(180,83,9,.18)", borderRadius: "10px", padding: "10px 12px", fontSize: "11.5px", color: "#92400e", fontWeight: "600", lineHeight: "1.5" },
    modalFooter: { padding: "0 22px 22px", display: "flex", gap: "10px" },
    backBtn: { flex: 1, display: "flex", alignItems: "center", justifyContent: "center", gap: "7px", background: "#f5f0fc", color: "#5B2D8E", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.25)", borderRadius: "13px", padding: "13px 20px", fontSize: "13.5px", fontWeight: "700", cursor: "pointer", fontFamily: "'DM Sans', sans-serif", transition: "background .15s" },
    reconfirmBtn: { flex: 2, display: "flex", alignItems: "center", justifyContent: "center", gap: "8px", background: "linear-gradient(135deg,#3d1a72 0%,#5B2D8E 50%,#9B59B6 100%)", color: "#fff", border: "none", borderRadius: "13px", padding: "13px 20px", fontSize: "13.5px", fontWeight: "700", cursor: "pointer", fontFamily: "'DM Sans', sans-serif", boxShadow: "0 6px 20px rgba(91,45,142,.38)", transition: "opacity .2s, box-shadow .2s" },
    reconfirmBtnDisabled: { flex: 2, display: "flex", alignItems: "center", justifyContent: "center", gap: "8px", background: "linear-gradient(135deg,#3d1a72 0%,#5B2D8E 50%,#9B59B6 100%)", color: "#fff", border: "none", borderRadius: "13px", padding: "13px 20px", fontSize: "13.5px", fontWeight: "700", cursor: "not-allowed", fontFamily: "'DM Sans', sans-serif", opacity: 0.5 },
    spin: { width: "15px", height: "15px", borderTopWidth: "2.5px", borderTopStyle: "solid", borderTopColor: "#fff", borderRightWidth: "2.5px", borderRightStyle: "solid", borderRightColor: "rgba(255,255,255,.3)", borderBottomWidth: "2.5px", borderBottomStyle: "solid", borderBottomColor: "rgba(255,255,255,.3)", borderLeftWidth: "2.5px", borderLeftStyle: "solid", borderLeftColor: "rgba(255,255,255,.3)", borderRadius: "50%", display: "inline-block", animation: "spin .7s linear infinite" },
};

/* ══════════════════════════════════════════
   FORM STYLES  (S)
══════════════════════════════════════════ */
const S = {
    page: { minHeight: "100vh", background: "linear-gradient(160deg, #f0ebf8 0%, #e8dff5 40%, #ede5f5 100%)", display: "flex", alignItems: "center", justifyContent: "center", padding: "40px 16px", fontFamily: "'DM Sans', 'Nunito', sans-serif", position: "relative", overflow: "hidden" },
    orb1: { position: "fixed", top: "-180px", right: "-120px", width: "500px", height: "500px", borderRadius: "50%", background: "radial-gradient(circle, rgba(91,45,142,.14) 0%, transparent 70%)", pointerEvents: "none" },
    orb2: { position: "fixed", bottom: "-150px", left: "-100px", width: "440px", height: "440px", borderRadius: "50%", background: "radial-gradient(circle, rgba(155,89,182,.10) 0%, transparent 70%)", pointerEvents: "none" },
    orb3: { position: "fixed", top: "40%", left: "60%", width: "300px", height: "300px", borderRadius: "50%", background: "radial-gradient(circle, rgba(120,60,200,.07) 0%, transparent 70%)", pointerEvents: "none" },
    card: { background: "#fff", borderRadius: "28px", boxShadow: "0 20px 60px rgba(91,45,142,.16), 0 4px 16px rgba(0,0,0,.06)", width: "100%", maxWidth: "660px", animation: "fadeUp .5s cubic-bezier(.22,.68,0,1.2) both", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.08)", marginTop: "80px", overflow: "hidden" },
    header: { background: "linear-gradient(140deg, #2d1060 0%, #4a2280 40%, #7B3FBE 80%, #9B59B6 100%)", padding: "32px 36px 28px", textAlign: "center", position: "relative", overflow: "hidden" },
    headerGlow: { position: "absolute", inset: 0, background: "radial-gradient(ellipse at 50% 0%, rgba(200,150,255,.25) 0%, transparent 60%)", pointerEvents: "none" },
    hIconWrap: { position: "relative", zIndex: 1, marginBottom: "12px" },
    hIcon: { width: "52px", height: "52px", borderRadius: "16px", background: "rgba(255,255,255,.15)", display: "flex", alignItems: "center", justifyContent: "center", margin: "0 auto", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(255,255,255,.25)", boxShadow: "0 4px 20px rgba(0,0,0,.15)" },
    hTitle: { fontFamily: "'DM Sans', sans-serif", fontSize: "24px", fontWeight: "700", color: "#fff", letterSpacing: "2.5px", marginBottom: "4px", position: "relative", zIndex: 1 },
    hSub: { color: "rgba(255,255,255,.7)", fontSize: "12.5px", letterSpacing: ".5px", marginBottom: "12px", position: "relative", zIndex: 1 },
    hBadge: { display: "inline-flex", alignItems: "center", gap: "5px", background: "rgba(255,255,255,.18)", color: "#fff", borderRadius: "999px", padding: "4px 14px", fontSize: "11px", fontWeight: "700", letterSpacing: ".7px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(255,255,255,.3)", position: "relative", zIndex: 1 },
    form: { padding: "24px 26px 32px", display: "flex", flexDirection: "column", gap: "16px" },
    row: { display: "flex", gap: "12px" },
    fieldGroup: { display: "flex", flexDirection: "column", gap: "5px", position: "relative" },
    label: { fontSize: "12px", fontWeight: "700", color: "#2d1a4e", letterSpacing: ".3px", display: "flex", alignItems: "center", gap: "5px" },
    req: { color: "#9B59B6" },
    opt: { color: "#b8a8ce", fontWeight: "600", fontSize: "11px" },
    input: { borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "#E0D4EF", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#E0D4EF", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#E0D4EF", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#E0D4EF", borderTopLeftRadius: "11px", borderTopRightRadius: "11px", borderBottomLeftRadius: "11px", borderBottomRightRadius: "11px", padding: "11px 14px", fontSize: "13.5px", color: "#1A0A2E", background: "#FDFBFF", transition: "border-top-color .2s, border-right-color .2s, border-bottom-color .2s, border-left-color .2s, box-shadow .2s", width: "100%", fontFamily: "'DM Sans', sans-serif" },
    inputErr: { borderTopColor: "#ef4444", borderRightColor: "#ef4444", borderBottomColor: "#ef4444", borderLeftColor: "#ef4444", background: "#fef2f2" },
    err: { fontSize: "11.5px", color: "#ef4444", fontWeight: "700", display: "flex", alignItems: "center", gap: "4px" },
    sectionLabel: { display: "flex", alignItems: "center", gap: "7px", fontSize: "11.5px", fontWeight: "700", color: "#7c5cbf", letterSpacing: ".4px", textTransform: "uppercase", marginBottom: "-4px" },
    toSection: { background: "linear-gradient(145deg, #faf7ff 0%, #f5f0fc 100%)", borderRadius: "18px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.14)", overflow: "hidden", boxShadow: "0 4px 20px rgba(91,45,142,.08), inset 0 1px 0 rgba(255,255,255,.8)" },
    toHeader: { background: "linear-gradient(135deg, rgba(74,34,128,.06) 0%, rgba(155,89,182,.06) 100%)", padding: "14px 18px", display: "flex", alignItems: "center", justifyContent: "space-between", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(91,45,142,.10)" },
    toHeaderLeft: { display: "flex", alignItems: "center", gap: "10px" },
    toIconBox: { width: "32px", height: "32px", borderRadius: "10px", background: "linear-gradient(135deg, #4a2280, #9B59B6)", display: "flex", alignItems: "center", justifyContent: "center", boxShadow: "0 3px 10px rgba(91,45,142,.30)" },
    toTitle: { display: "block", fontSize: "13.5px", fontWeight: "700", color: "#2d1a4e" },
    toSubTitle: { display: "block", fontSize: "10.5px", color: "#9b7ec8", marginTop: "1px" },
    toSecureBadge: { display: "flex", alignItems: "center", gap: "4px", fontSize: "10px", fontWeight: "700", color: "#22c55e", background: "rgba(34,197,94,.08)", padding: "3px 9px", borderRadius: "999px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(34,197,94,.2)", letterSpacing: ".4px" },
    toBody: { padding: "16px 18px", display: "flex", flexDirection: "column", gap: "13px" },
    toFieldGroup: { display: "flex", flexDirection: "column", gap: "5px", position: "relative" },
    toLabel: { fontSize: "11.5px", fontWeight: "700", color: "#4a2280", display: "flex", alignItems: "center", gap: "5px", letterSpacing: ".2px" },
    fieldHint: { marginLeft: "auto", fontSize: "10px", color: "#b8a8ce", fontWeight: "600", letterSpacing: ".2px" },
    toInput: { borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "#ddd5ef", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#ddd5ef", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#ddd5ef", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#ddd5ef", borderTopLeftRadius: "11px", borderTopRightRadius: "11px", borderBottomLeftRadius: "11px", borderBottomRightRadius: "11px", padding: "11px 14px", fontSize: "13.5px", color: "#1A0A2E", background: "#fff", transition: "border-top-color .2s, border-right-color .2s, border-bottom-color .2s, border-left-color .2s, box-shadow .2s", width: "100%", fontFamily: "'DM Sans', sans-serif" },
    toInputFocus: { borderTopColor: "#5B2D8E", borderRightColor: "#5B2D8E", borderBottomColor: "#5B2D8E", borderLeftColor: "#5B2D8E", boxShadow: "0 0 0 3px rgba(91,45,142,.12)" },
    toInputErr: { borderTopColor: "#ef4444", borderRightColor: "#ef4444", borderBottomColor: "#ef4444", borderLeftColor: "#ef4444", background: "#fef2f2" },
    inputWithIcon: { paddingLeft: "40px" },
    accNumFont: { fontFamily: "'IBM Plex Mono', monospace", letterSpacing: "1px", fontSize: "14px" },
    inputIconLeft: { position: "absolute", left: "13px", top: "50%", transform: "translateY(-50%)", display: "flex", alignItems: "center", zIndex: 1, pointerEvents: "none" },
    inputIconRight: { position: "absolute", right: "12px", top: "50%", transform: "translateY(-50%)", display: "flex", alignItems: "center", zIndex: 1 },
    toErr: { fontSize: "11.5px", color: "#ef4444", fontWeight: "700", display: "flex", alignItems: "center", gap: "4px" },
    toDivider: { display: "flex", alignItems: "center", gap: "10px", padding: "0 4px" },
    toDivLine: { flex: 1, height: "1px", background: "rgba(91,45,142,.12)" },
    toDivIcon: { width: "24px", height: "24px", borderRadius: "50%", background: "rgba(91,45,142,.08)", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 },
    recipientCard: { display: "flex", alignItems: "center", gap: "12px", background: "linear-gradient(135deg, #4a2280 0%, #7B3FBE 100%)", borderRadius: "14px", padding: "14px 16px", position: "relative", overflow: "hidden", animation: "cardIn .3s cubic-bezier(.22,.68,0,1.2) both", boxShadow: "0 6px 24px rgba(74,34,128,.30)" },
    recipientCardGlow: { position: "absolute", top: "-30px", right: "-30px", width: "100px", height: "100px", borderRadius: "50%", background: "radial-gradient(circle, rgba(255,255,255,.12) 0%, transparent 70%)", pointerEvents: "none" },
    recipientLeft: { flexShrink: 0 },
    recipientInfo: { flex: 1, display: "flex", flexDirection: "column", gap: "2px" },
    recipientName: { fontSize: "14px", fontWeight: "700", color: "#fff" },
    recipientBank: { fontSize: "11px", color: "rgba(255,255,255,.65)" },
    recipientNum: { fontSize: "12px", color: "rgba(255,255,255,.85)", fontFamily: "'IBM Plex Mono', monospace", letterSpacing: ".8px", marginTop: "2px" },
    recipientCheck: { width: "28px", height: "28px", borderRadius: "50%", background: "rgba(255,255,255,.15)", display: "flex", alignItems: "center", justifyContent: "center", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(34,197,94,.5)", flexShrink: 0 },
    dropTrigger: { borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "#ddd5ef", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#ddd5ef", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#ddd5ef", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#ddd5ef", borderTopLeftRadius: "11px", borderTopRightRadius: "11px", borderBottomLeftRadius: "11px", borderBottomRightRadius: "11px", padding: "8px 12px", background: "#fff", cursor: "pointer", display: "flex", alignItems: "center", justifyContent: "space-between", transition: "border-top-color .2s, border-right-color .2s, border-bottom-color .2s, border-left-color .2s, box-shadow .2s", userSelect: "none", minHeight: "50px" },
    dropTriggerOpen: { borderTopColor: "#5B2D8E", borderRightColor: "#5B2D8E", borderBottomColor: "#5B2D8E", borderLeftColor: "#5B2D8E", boxShadow: "0 0 0 3px rgba(91,45,142,.12)", borderBottomLeftRadius: 0, borderBottomRightRadius: 0 },
    dropInner: { display: "flex", alignItems: "center", gap: "10px", flex: 1 },
    dropSelText: { fontSize: "13.5px", fontWeight: "700", color: "#1A0A2E", flex: 1 },
    dropSelCode: { fontSize: "10.5px", color: "#9B59B6", marginTop: "1px" },
    dropPlh: { color: "#c4b3d9", fontSize: "13px" },
    dropBadge: { fontSize: "10px", color: "#9B59B6", fontWeight: "700", background: "#F0E8FA", padding: "2px 8px", borderRadius: "999px" },
    dummyCircle: { width: "36px", height: "36px", borderRadius: "50%", background: "#f0e8fa", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 },
    chevron: { color: "#9B59B6", display: "flex", transition: "transform .2s ease" },
    dropPanel: { position: "absolute", top: "100%", left: 0, right: 0, background: "#fff", borderTopWidth: 0, borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#5B2D8E", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#5B2D8E", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#5B2D8E", borderBottomLeftRadius: "14px", borderBottomRightRadius: "14px", boxShadow: "0 20px 50px rgba(91,45,142,.22)", zIndex: 999, animation: "popDown .18s ease both", overflow: "hidden" },
    searchRow: { display: "flex", alignItems: "center", gap: "8px", padding: "10px 14px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "#f0e8fa", background: "#faf7fd" },
    searchInput: { border: "none", outline: "none", background: "transparent", fontSize: "13px", color: "#1A0A2E", flex: 1, fontFamily: "'DM Sans', sans-serif" },
    searchClear: { border: "none", background: "none", color: "#b8a8ce", cursor: "pointer", fontSize: "12px", padding: "0 2px", lineHeight: 1 },
    dropScroll: { maxHeight: "260px", overflowY: "auto" },
    dropItem: { display: "flex", alignItems: "center", gap: "12px", padding: "10px 16px", cursor: "pointer", transition: "background .12s", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "#f8f4fd" },
    dropItemActive: { background: "#f5f0fa" },
    bankMeta: { display: "flex", flexDirection: "column", flex: 1 },
    bankNm: { fontSize: "13px", fontWeight: "700", color: "#1A0A2E" },
    bankCd: { fontSize: "10.5px", color: "#9B59B6", marginTop: "1px" },
    tickCircle: { width: "22px", height: "22px", borderRadius: "50%", background: "linear-gradient(135deg,#4a2280,#9B59B6)", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 },
    noResult: { padding: "24px 16px", textAlign: "center", color: "#b8a8ce", fontSize: "13px", display: "flex", flexDirection: "column", alignItems: "center", gap: "8px" },
    // ── Fee Tier Box (purple-themed) ──
    feeTierBox: { background: "linear-gradient(145deg, #faf7ff 0%, #f5f0fc 100%)", borderRadius: "16px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.14)", overflow: "hidden" },
    feeTierHeader: { display: "flex", alignItems: "center", gap: "7px", padding: "10px 16px 9px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(91,45,142,.10)", background: "rgba(74,34,128,.04)" },
    feeTierTitle: { fontSize: "11px", fontWeight: "800", color: "#4a2280", textTransform: "uppercase", letterSpacing: ".5px" },
    feeTierGrid: { display: "flex", alignItems: "center", padding: "12px 16px", gap: "4px" },
    feeTierItem: { flex: 1, textAlign: "center", padding: "10px 8px", borderRadius: "10px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(91,45,142,.10)", background: "#fff", position: "relative", transition: "all .2s" },
    feeTierItemActive: { background: "rgba(220,38,38,.05)", borderColor: "rgba(220,38,38,.25)", boxShadow: "0 2px 10px rgba(220,38,38,.10)" },
    feeTierItemBigActive: { background: "rgba(22,163,74,.07)", borderColor: "rgba(22,163,74,.30)", boxShadow: "0 2px 10px rgba(22,163,74,.12)" },
    feeTierRange: { fontSize: "11px", fontWeight: "700", color: "#7a5a9a", marginBottom: "4px" },
    feeTierPct: { fontSize: "22px", fontWeight: "800", color: "#5B2D8E", fontFamily: "'DM Sans', sans-serif", lineHeight: 1.1 },
    feeTierLabel: { fontSize: "10px", color: "#b8a8ce", marginTop: "2px", fontWeight: "600" },
    feeTierActiveDot: { position: "absolute", top: "6px", right: "6px", width: "7px", height: "7px", borderRadius: "50%", background: "#dc2626" },
    feeTierDivider: { display: "flex", alignItems: "center", justifyContent: "center", padding: "0 4px", flexShrink: 0 },
    feeTierActive: { margin: "0 16px 12px", fontSize: "11.5px", fontWeight: "600", color: "#5B2D8E", background: "rgba(91,45,142,.07)", borderRadius: "8px", padding: "7px 12px", display: "flex", alignItems: "center", gap: "6px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(91,45,142,.15)" },
    // ──
    feeToggleBox: { background: "linear-gradient(145deg, #faf7ff 0%, #f5f0fc 100%)", borderRadius: "16px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(91,45,142,.14)", overflow: "hidden", boxShadow: "0 2px 12px rgba(91,45,142,.07)" },
    feeToggleHeader: { display: "flex", alignItems: "center", gap: "7px", padding: "11px 16px 10px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(91,45,142,.10)", background: "linear-gradient(135deg, rgba(74,34,128,.05) 0%, rgba(155,89,182,.05) 100%)" },
    feeToggleTitle: { fontSize: "11.5px", fontWeight: "700", color: "#4a2280", letterSpacing: ".3px", textTransform: "uppercase" },
    feeRadioRow: { display: "flex", flexDirection: "column" },
    feeRadioDivider: { height: "1px", background: "rgba(91,45,142,.08)", margin: "0 16px" },
    feeRadioLabel: { display: "flex", alignItems: "center", gap: "12px", padding: "12px 16px", cursor: "pointer", transition: "background .15s", userSelect: "none" },
    feeRadioLabelActive:      { background: "rgba(91,45,142,.05)" },
    feeRadioLabelNoFeeActive: { background: "rgba(22,163,74,.04)" },
    radioHidden: { position: "absolute", opacity: 0, width: 0, height: 0, pointerEvents: "none" },
    radioCircle: { width: "18px", height: "18px", borderRadius: "50%", flexShrink: 0, borderWidth: "2px", borderStyle: "solid", borderColor: "#ddd5ef", display: "flex", alignItems: "center", justifyContent: "center", background: "#fff", transition: "border-color .15s" },
    radioCircleActive:      { borderColor: "#5B2D8E", background: "#fff" },
    radioCircleNoFeeActive: { borderColor: "#16a34a", background: "#fff" },
    radioDot:      { width: "8px", height: "8px", borderRadius: "50%", background: "#5B2D8E" },
    radioDotGreen: { width: "8px", height: "8px", borderRadius: "50%", background: "#16a34a" },
    feeRadioText: { display: "flex", flexDirection: "column", gap: "1px", flex: 1 },
    feeRadioMain: { fontSize: "13px", fontWeight: "700", color: "#1A0A2E" },
    feeRadioSub:  { fontSize: "11px", fontWeight: "500", transition: "color .15s" },
    feeBadge: { fontSize: "11px", fontWeight: "700", padding: "3px 10px", borderRadius: "999px", borderTopWidth: "1px", borderTopStyle: "solid", borderRightWidth: "1px", borderRightStyle: "solid", borderBottomWidth: "1px", borderBottomStyle: "solid", borderLeftWidth: "1px", borderLeftStyle: "solid", transition: "all .15s", flexShrink: 0 },
    amtWrap: { position: "relative" },
    amtPfx: { position: "absolute", left: "14px", top: "50%", transform: "translateY(-50%)", color: "#5B2D8E", fontWeight: "800", fontSize: "16px", zIndex: 1 },
    amtInput: { paddingLeft: "30px" },
    pills: { display: "flex", flexWrap: "wrap", gap: "6px", marginTop: "6px" },
    pill: { padding: "5px 12px", borderRadius: "999px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "#ddd5ef", background: "#FDFBFF", color: "#5B2D8E", fontSize: "11.5px", fontWeight: "700", cursor: "pointer", transition: "all .15s", fontFamily: "'DM Sans', sans-serif" },
    pillOn: { background: "#5B2D8E", color: "#fff", borderColor: "#5B2D8E" },
    summary: { background: "linear-gradient(135deg, #F8F4FD 0%, #EDE0F7 100%)", borderTopLeftRadius: "14px", borderTopRightRadius: "14px", borderBottomLeftRadius: "14px", borderBottomRightRadius: "14px", padding: "16px 18px", borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "rgba(91,45,142,.14)", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "rgba(91,45,142,.14)", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "rgba(91,45,142,.14)", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "rgba(91,45,142,.14)", animation: "fadeUp .3s ease both" },
    sumHead: { display: "flex", alignItems: "center", gap: "7px", marginBottom: "9px" },
    sumTitle: { fontWeight: "800", color: "#5B2D8E", fontSize: "13px" },
    div: { height: "1px", background: "rgba(91,45,142,.13)", margin: "7px 0" },
    sRow: { display: "flex", justifyContent: "space-between", alignItems: "center", padding: "3px 0" },
    sLbl: { fontSize: "12.5px", color: "#7a5a9a" },
    sVal: { fontSize: "12.5px", color: "#1A0A2E", fontWeight: "600" },
    sumTotalLbl: { fontSize: "13.5px", fontWeight: "800", color: "#1A0A2E" },
    sumTotal: { fontSize: "20px", fontWeight: "800", color: "#5B2D8E", letterSpacing: "-.5px" },
    cashFeeNote: { display: "flex", alignItems: "center", gap: "6px", fontSize: "11px", color: "#16a34a", fontWeight: "600", background: "rgba(22,163,74,.07)", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(22,163,74,.18)", borderRadius: "8px", padding: "6px 10px", marginTop: "2px" },
    btn: { marginTop: "4px", background: "linear-gradient(135deg,#3d1a72 0%,#5B2D8E 50%,#9B59B6 100%)", color: "#fff", border: "none", borderRadius: "14px", padding: "15px 24px", fontSize: "14.5px", fontWeight: "700", cursor: "pointer", display: "flex", alignItems: "center", justifyContent: "center", gap: "9px", letterSpacing: ".3px", boxShadow: "0 6px 24px rgba(91,45,142,.38)", transition: "opacity .2s, box-shadow .2s, transform .1s", fontFamily: "'DM Sans', sans-serif" },
    btnOff: { opacity: .4, cursor: "not-allowed", boxShadow: "none" },
    spin: { width: "16px", height: "16px", borderTopWidth: "2.5px", borderTopStyle: "solid", borderTopColor: "#fff", borderRightWidth: "2.5px", borderRightStyle: "solid", borderRightColor: "rgba(255,255,255,.35)", borderBottomWidth: "2.5px", borderBottomStyle: "solid", borderBottomColor: "rgba(255,255,255,.35)", borderLeftWidth: "2.5px", borderLeftStyle: "solid", borderLeftColor: "rgba(255,255,255,.35)", borderRadius: "50%", display: "inline-block", animation: "spin .7s linear infinite" },
};