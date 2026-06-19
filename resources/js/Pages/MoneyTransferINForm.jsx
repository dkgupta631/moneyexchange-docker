import { useState, useEffect, useRef } from "react";
import { useForm, Head, usePage } from "@inertiajs/react";

const BANKS = [
    { symbol: "BBL",    value: "002", name: "Bangkok Bank",                        file: "BBL.png"    },
    { symbol: "KBANK",  value: "004", name: "Kasikorn Bank",                       file: "KBANK.png"  },
    { symbol: "KTB",    value: "006", name: "Krungthai Bank",                      file: "KTB.png"    },
    { symbol: "TMB",    value: "011", name: "TMB Thanachart Bank",                 file: "TMB.png"    },
    { symbol: "SCB",    value: "014", name: "Siam Commercial Bank",                file: "SCB.jpg"    },
    { symbol: "CITI",   value: "017", name: "Citibank N.A.",                       file: "CITI.jpg"   },
    { symbol: "SCBT",   value: "020", name: "Standard Chartered Bank (Thailand)",  file: "SCBT.png"   },
    { symbol: "CIMB",   value: "022", name: "CIMB Thai Bank",                      file: "CIMB.png"   },
    { symbol: "UOB",    value: "024", name: "UOB Bank",                             file: "UOB.png"    },
    { symbol: "BAY",    value: "025", name: "Bank of Ayudhya",                     file: "BAY.jpg"    },
    { symbol: "GOV",    value: "030", name: "Government Savings Bank",             file: "GOV.jpg"    },
    { symbol: "GHB",    value: "033", name: "Government Housing Bank",             file: "GHB.jpg"    },
    { symbol: "AGRI",   value: "034", name: "Bank for Agriculture (BAAC)",         file: "AGRI.png"   },
    { symbol: "ISBT",   value: "066", name: "Islamic Bank of Thailand",            file: "ISBT.jpg"   },
    { symbol: "TISCO",  value: "067", name: "TISCO Bank",                          file: "TISCO.png"  },
    { symbol: "KK",     value: "069", name: "Kiatnakin Bank",                      file: "KK.jpg"     },
    { symbol: "ACL",    value: "070", name: "ACL (ACLEDA) Bank",                  file: "ACL.png"    },
    { symbol: "TCRB",   value: "071", name: "Thai Credit Retail Bank",            file: "TCRB.png"   },
    { symbol: "LHBANK", value: "073", name: "Land and House Bank",                file: "LHBANK.jpg" },
];

const AMOUNT_SUGGESTIONS = [
    500, 1000, 1500, 2000, 2500, 3000, 4000, 5000,
    7500, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000,
];

function BankImg({ bank, size = 34, appUrl }) {
    const [errored, setErrored] = useState(false);
    if (errored) {
        return (
            <div style={{ width: size, height: size, borderRadius: "50%", background: "linear-gradient(135deg, #1a6b3c, #27ae60)", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0, fontSize: Math.max(8, size * 0.28) + "px", fontWeight: "800", color: "#fff", letterSpacing: "-0.5px" }}>
                {bank.symbol.slice(0, 3)}
            </div>
        );
    }
    return (
        <img src={`${appUrl}/website/assets/bank-images/${bank.file}`} alt={bank.name} onError={() => setErrored(true)}
            style={{ width: size, height: size, borderRadius: "50%", objectFit: "cover", flexShrink: 0, borderWidth: "2px", borderStyle: "solid", borderColor: "rgba(39,174,96,0.15)", background: "#f0faf4" }}
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
            background: isBig ? "rgba(13,148,136,.10)" : "rgba(220,38,38,.08)",
            color: isBig ? "#0d9488" : "#dc2626",
            borderWidth: "1px", borderStyle: "solid",
            borderColor: isBig ? "rgba(13,148,136,.25)" : "rgba(220,38,38,.20)",
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

/* ── Confirmation Popup (green theme) ── */
function ConfirmPopup({ data, summary, feeMode, feePercentage, selectedBank, formatAccNumber, fmt, t, appUrl, onConfirm, onBack, processing }) {
    const noFee = feeMode === "no-fee";

    return (
        <div style={P.overlay}>
            <div style={P.modal}>
                <div style={P.modalGlow} />

                {/* Header */}
                <div style={P.mHeader}>
                    <div style={P.mHeaderBg} />
                    <div style={P.mIconWrap}>
                        <div style={P.mIcon}>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                <path d="M9 12l2 2 4-4"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h2 style={P.mTitle}>{t('Confirm Transfer')}</h2>
                    <p style={P.mSub}>{t('Please review your transfer details carefully before confirming')}</p>
                    <div style={P.mTypeBadge}>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
                        </svg>
                        Transfer-IN
                    </div>
                </div>

                <div style={P.mBody}>
                    {/* Receiver section */}
                    {(data.customer_name || data.phone) && (
                        <div style={P.section}>
                            <div style={P.sectionHead}>
                                <div style={P.sectionDot} />
                                <span style={P.sectionTitle}>{t('Customer Information')}</span>
                            </div>
                            <div style={P.sectionCard}>
                                {data.customer_name && (
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t('Customer Name')}</span>
                                        <span style={P.rowVal}>{data.customer_name}</span>
                                    </div>
                                )}
                                {data.phone && (
                                    <div style={P.row}>
                                        <span style={P.rowLabel}>{t('Phone')}</span>
                                        <span style={P.rowVal}>{data.phone}</span>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {/* Sender / Bank section */}
                    <div style={P.section}>
                        <div style={P.sectionHead}>
                            <div style={P.sectionDot} />
                            <span style={P.sectionTitle}>{t('Transfer From (Sender Details)')}</span>
                        </div>
                        <div style={P.senderCard}>
                            <div style={P.senderCardGlow} />
                            {selectedBank && <BankImg bank={selectedBank} size={48} appUrl={appUrl} />}
                            <div style={{ flex: 1 }}>
                                <div style={P.senderName}>{data.acc_name}</div>
                                <div style={P.senderBank}>{data.bank_name}</div>
                                <div style={P.senderNum}>{formatAccNumber(data.acc_number)}</div>
                            </div>
                            <div style={P.senderSymbol}>{selectedBank?.symbol}</div>
                        </div>
                    </div>

                    {/* Amount section */}
                    <div style={P.section}>
                        <div style={P.sectionHead}>
                            <div style={P.sectionDot} />
                            <span style={P.sectionTitle}>{t('Transfer Summary')}</span>
                        </div>
                        <div style={P.sectionCard}>
                            <div style={P.row}>
                                <span style={P.rowLabel}>{t('Entered Amount')}</span>
                                <span style={P.rowVal}>฿{fmt(summary.entered)} THB</span>
                            </div>
                            {/* Fee Tier indicator */}
                            <div style={{ ...P.row, justifyContent: "flex-start", gap: "8px" }}>
                                <span style={P.rowLabel}>{t('Fee Tier')}</span>
                                <span style={{
                                    fontSize: "10.5px", fontWeight: "700", padding: "2px 10px", borderRadius: "999px",
                                    background: summary.entered >= 100000 ? "rgba(13,148,136,.10)" : "rgba(220,38,38,.08)",
                                    color: summary.entered >= 100000 ? "#0d9488" : "#dc2626",
                                    borderWidth: "1px", borderStyle: "solid",
                                    borderColor: summary.entered >= 100000 ? "rgba(13,148,136,.25)" : "rgba(220,38,38,.20)"
                                }}>
                                    {summary.entered >= 100000 ? `≥ ฿100,000 → ${feePercentage}%` : `< ฿100,000 → ${feePercentage}%`}
                                </span>
                            </div>
                            <div style={P.divider} />
                            <div style={P.row}>
                                <span style={P.rowLabel}>{t('Fee Mode')}</span>
                                <span style={{ ...P.modeBadge, background: noFee ? "rgba(13,148,136,.10)" : "rgba(26,122,71,.10)", color: noFee ? "#0d9488" : "#1a7a47", borderColor: noFee ? "rgba(13,148,136,.25)" : "rgba(26,122,71,.20)" }}>
                                    {noFee ? t('Fee Paid in Cash') : `${feePercentage}% ${t('Deducted')}`}
                                </span>
                            </div>
                            <div style={P.row}>
                                <span style={P.rowLabel}>{t('Transfer Fee')} ({feePercentage}%)</span>
                                <span style={{ ...P.rowVal, color: noFee ? "#0d9488" : "#dc2626", fontWeight: 700 }}>
                                    {noFee ? `+ ฿${fmt(summary.fee)} THB` : `− ฿${fmt(summary.fee)} THB`}
                                </span>
                            </div>
                            {noFee && (
                                <div style={P.cashNote}>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0d9488" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {t('Customer pays fee in cash separately')}
                                </div>
                            )}
                            <div style={P.divider} />
                            <div style={P.netRow}>
                                <span style={P.netLabel}>{t('Net Receive Amount')}</span>
                                <span style={P.netAmount}>฿{fmt(summary.net)} THB</span>
                            </div>
                        </div>
                    </div>

                    {/* Warning notice */}
                    <div style={P.notice}>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1a7a47" strokeWidth="2.2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <span style={P.noticeText}>{t('Once confirmed, this transfer will be processed. Please verify all details carefully.')}</span>
                    </div>
                </div>

                {/* Footer buttons */}
                <div style={P.mFooter}>
                    <button type="button" style={P.backBtn} onClick={onBack}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                        {t('Back')}
                    </button>
                    <button type="button" style={P.confirmBtn} onClick={onConfirm} disabled={processing}>
                        {processing ? (
                            <><span style={P.spin} />{t('Processing')}…</>
                        ) : (
                            <>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
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

export default function MoneyTransferINForm({ gettransferchanges }) {
    const { appUrl, translations } = usePage().props;
    const t = (key) => translations?.[key] ?? key;

    // ── Fee tiers from DB ──────────────────────────────────────────────
    // gettransferchanges is now an array/object with both tiers
    // Controller passes: { big_amount: 1, little_amount: 2 }
    const bigAmountFee    = parseFloat(gettransferchanges?.big_amount    ?? 1);
    const littleAmountFee = parseFloat(gettransferchanges?.little_amount ?? 2);

    const [feeMode, setFeeMode]     = useState("with-fee");
    const [showConfirm, setShowConfirm] = useState(false);

    // Dynamically computed fee percentage based on entered amount
    const [feePercentage, setFeePercentage] = useState(littleAmountFee);

    const { data, setData, post, processing, errors } = useForm({
        customer_name: "", phone: "", bank_name: "", bank_symbol: "",
        acc_name: "", acc_number: "", entered_amount: "",
        transfer_type: "Transfer-IN", currency: "฿",
        trf_fee_in_persentage: feePercentage, trf_fee: "", net_amount: "",
    });

    const [summary, setSummary]           = useState(null);
    const [amountError, setAmountError]   = useState("");
    const [dropOpen, setDropOpen]         = useState(false);
    const [selectedBank, setSelectedBank] = useState(null);
    const [search, setSearch]             = useState("");
    const [accNameFocus, setAccNameFocus] = useState(false);
    const [accNumFocus, setAccNumFocus]   = useState(false);
    const [accNameError, setAccNameError] = useState("");
    const [accNumError, setAccNumError]   = useState("");
    const dropRef = useRef(null);

    useEffect(() => {
        const fn = (e) => { if (dropRef.current && !dropRef.current.contains(e.target)) { setDropOpen(false); setSearch(""); } };
        document.addEventListener("mousedown", fn);
        return () => document.removeEventListener("mousedown", fn);
    }, []);

    // ── Recalculate whenever amount or feeMode changes ─────────────────
    useEffect(() => {
        const amount = parseFloat(data.entered_amount);
        if (!isNaN(amount) && amount > 0) {
            // Determine tier
            const activeFee = amount >= 100000 ? bigAmountFee : littleAmountFee;
            setFeePercentage(activeFee);

            const feeAmount = parseFloat(((amount * activeFee) / 100).toFixed(2));
            const net = feeMode === "no-fee" ? amount : parseFloat((amount - feeAmount).toFixed(2));
            setSummary({ entered: amount, fee: feeAmount, net, feePercentage: activeFee, noFeeMode: feeMode === "no-fee" });
            setData(prev => ({ ...prev, trf_fee_in_persentage: activeFee, trf_fee: feeAmount.toFixed(2), net_amount: net.toFixed(2) }));
        } else {
            setFeePercentage(littleAmountFee);
            setSummary(null);
            setData(prev => ({ ...prev, trf_fee_in_persentage: littleAmountFee, trf_fee: "0.00", net_amount: "" }));
        }
    }, [data.entered_amount, feeMode]);

    const validateAmount = (val) => {
        const num = parseFloat(val);
        if (isNaN(num) || val === "") { setAmountError(""); return; }
        if (num < 500)      setAmountError("Minimum amount is ฿500 THB");
        else if (num > 100000) setAmountError("Maximum amount is ฿100,000 THB");
        else                setAmountError("");
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
        if (d.length <= 4) return d;
        if (d.length <= 9) return `${d.slice(0,4)}-${d.slice(4)}`;
        return `${d.slice(0,4)}-${d.slice(4,9)}-${d.slice(9,10)}`;
    };

    const handleBankSelect = (bank) => {
        setSelectedBank(bank);
        setData(prev => ({ ...prev, bank_name: bank.name, bank_symbol: bank.symbol }));
        setDropOpen(false); setSearch("");
    };

    const fmt = (v) => new Intl.NumberFormat("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v);

    const filteredBanks = BANKS.filter(b =>
        b.name.toLowerCase().includes(search.toLowerCase()) || b.symbol.toLowerCase().includes(search.toLowerCase())
    );

    const canSubmit = !processing && !amountError && !accNameError && !accNumError
        && data.entered_amount && data.bank_name && data.acc_name.trim().length > 0 && data.acc_number.trim().length > 0;

    const handleFormSubmit = (e) => {
        e.preventDefault();
        if (canSubmit) setShowConfirm(true);
    };

    const handleReconfirm = () => {
        post("/money-transfer-in/store");
    };

    const enteredNum = parseFloat(data.entered_amount) || 0;

    return (
        <>
            <Head title={t('Transfer-IN')} />
            <div style={S.page}>
                <div style={S.orb1} /><div style={S.orb2} /><div style={S.orb3} />

                {/* ── Confirmation Popup ── */}
                {showConfirm && summary && (
                    <ConfirmPopup
                        data={data} summary={summary} feeMode={feeMode}
                        feePercentage={feePercentage} selectedBank={selectedBank}
                        formatAccNumber={formatAccNumber} fmt={fmt} t={t} appUrl={appUrl}
                        onConfirm={handleReconfirm} onBack={() => setShowConfirm(false)}
                        processing={processing}
                    />
                )}

                <div style={S.card}>
                    {/* ══ HEADER ══ */}
                    <div style={S.header}>
                        <div style={S.headerGlow} />
                        <div style={S.hIconWrap}>
                            <div style={S.hIcon}>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                        </div>
                        <h1 style={S.hTitle}>G+ Services</h1>
                        <p style={S.hSub}>{t('Money Transfer')} — IN</p>
                        <span style={S.hBadge}>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
                            </svg>
                            {t('Transfer')}-IN
                        </span>
                    </div>

                    <form onSubmit={handleFormSubmit} style={S.form}>
                        {/* ── Receiver Row ── */}
                        <div style={S.sectionLabel}>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1a7a47" strokeWidth="2.2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>{t('Customer Information')}</span>
                        </div>

                        <div style={S.row}>
                            <Field label={t('Customer Name')} optional t={t} style={{ flex: 1 }} error={errors.customer_name}>
                                <input type="text" value={data.customer_name} onChange={e => setData("customer_name", e.target.value)} placeholder={t('Full name')} style={{ ...S.input, ...(errors.customer_name ? S.inputErr : {}) }} />
                            </Field>
                            <Field label={t('Phone')} optional t={t} style={{ flex: 1 }}>
                                <input type="text" value={data.phone} onChange={e => setData("phone", e.target.value.replace(/[^0-9\-+\s]/g, "").slice(0, 13))} placeholder="08x-xxx-xxxx" style={S.input} maxLength={13} />
                            </Field>
                        </div>

                        {/* ══ FROM SECTION ══ */}
                        <div style={S.fromSection}>
                            <div style={S.fromHeader}>
                                <div style={S.fromHeaderLeft}>
                                    <div style={S.fromIconBox}>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span style={S.fromTitle}>{t('Transfer From')}</span>
                                        <span style={S.fromSubTitle}>{t('Sender Details')}</span>
                                    </div>
                                </div>
                                <div style={S.fromSecureBadge}>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                    {t('Secure')}
                                </div>
                            </div>

                            <div style={S.fromBody}>
                                {/* Bank dropdown */}
                                <div style={S.fromFieldGroup} ref={dropRef}>
                                    <label style={S.fromLabel}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                                            <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                                        </svg>
                                        {t('Bank Name')} <span style={S.req}>*</span>
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
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#27ae60" strokeWidth="2">
                                                            <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                                                        </svg>
                                                    </div>
                                                    <span style={S.dropPlh}>{t('Select a Bank')}</span>
                                                </>
                                            )}
                                        </div>
                                        <span style={{ ...S.chevron, transform: dropOpen ? "rotate(180deg)" : "rotate(0)" }}>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a7a47" strokeWidth="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        </span>
                                    </div>
                                    {dropOpen && (
                                        <div style={S.dropPanel}>
                                            <div style={S.searchRow}>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#27ae60" strokeWidth="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                                <input autoFocus type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder={t('Search bank…')} style={S.searchInput} onClick={e => e.stopPropagation()} />
                                                {search && <button type="button" style={S.searchClear} onClick={e => { e.stopPropagation(); setSearch(""); }}>✕</button>}
                                            </div>
                                            <div style={S.dropScroll}>
                                                {filteredBanks.length === 0 ? (
                                                    <div style={S.noResult}>
                                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#a8d5b5" strokeWidth="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                                        <span>{t('No bank found')}</span>
                                                    </div>
                                                ) : filteredBanks.map(bank => (
                                                    <div key={bank.value} className="bank-row" style={{ ...S.dropItem, ...(selectedBank?.value === bank.value ? S.dropItemActive : {}) }} onClick={() => handleBankSelect(bank)}>
                                                        <BankImg bank={bank} size={40} appUrl={appUrl} />
                                                        <div style={S.bankMeta}>
                                                            <span style={S.bankNm}>{bank.name}</span>
                                                            <span style={S.bankCd}>{bank.symbol} · Code {bank.value}</span>
                                                        </div>
                                                        {selectedBank?.value === bank.value && (
                                                            <div style={S.tickCircle}><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    {errors.bank_name && <span style={S.err}>{errors.bank_name}</span>}
                                </div>

                                {/* Divider */}
                                <div style={S.fromDivider}>
                                    <div style={S.fromDivLine} />
                                    <div style={S.fromDivIcon}>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#27ae60" strokeWidth="2.2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div style={S.fromDivLine} />
                                </div>

                                {/* Account Name */}
                                <div style={S.fromFieldGroup}>
                                    <label style={S.fromLabel}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        {t('Account Name')} <span style={S.req}>*</span>
                                        <span style={S.fieldHint}>{t('Letters only')}</span>
                                    </label>
                                    <div style={{ position: "relative" }}>
                                        <div style={S.inputIconLeft}>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={accNameFocus ? "#1a7a47" : "#a8d5b5"} strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </div>
                                        <input type="text" value={data.acc_name} onFocus={() => setAccNameFocus(true)} onBlur={() => setAccNameFocus(false)} onChange={e => handleAccNameChange(e.target.value)} placeholder={t('e.g. Somchai Jaidee')} style={{ ...S.fromInput, ...S.inputWithIcon, ...(accNameFocus ? S.fromInputFocus : {}), ...(errors.acc_name || accNameError ? S.fromInputErr : {}) }} autoComplete="off" inputMode="text" />
                                        {data.acc_name && !accNameError && !errors.acc_name && <div style={S.inputIconRight}><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>}
                                    </div>
                                    {(errors.acc_name || accNameError) && <span style={S.fromErr}><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{errors.acc_name || accNameError}</span>}
                                </div>

                                {/* Account Number */}
                                <div style={S.fromFieldGroup}>
                                    <label style={S.fromLabel}>
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        {t('Account Number')} <span style={S.req}>*</span>
                                        <span style={S.fieldHint}>{t('Numbers only')}</span>
                                    </label>
                                    <div style={{ position: "relative" }}>
                                        <div style={S.inputIconLeft}>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={accNumFocus ? "#1a7a47" : "#a8d5b5"} strokeWidth="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        </div>
                                        <input type="text" value={formatAccNumber(data.acc_number)} onFocus={() => setAccNumFocus(true)} onBlur={() => setAccNumFocus(false)} onChange={e => handleAccNumberChange(e.target.value)} placeholder="XXXX-XXXXX-X" maxLength={12} style={{ ...S.fromInput, ...S.inputWithIcon, ...S.accNumFont, ...(accNumFocus ? S.fromInputFocus : {}), ...(errors.acc_number || accNumError ? S.fromInputErr : {}) }} inputMode="numeric" autoComplete="off" />
                                        {data.acc_number && !accNumError && !errors.acc_number && <div style={S.inputIconRight}><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>}
                                    </div>
                                    {(errors.acc_number || accNumError) && <span style={S.fromErr}><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{errors.acc_number || accNumError}</span>}
                                </div>

                                {/* Sender preview */}
                                {selectedBank && data.acc_name && data.acc_number && !accNameError && !accNumError && (
                                    <div style={S.senderCard}>
                                        <div style={S.senderCardGlow} />
                                        <BankImg bank={selectedBank} size={44} appUrl={appUrl} />
                                        <div style={S.senderInfo}>
                                            <span style={S.senderName}>{data.acc_name}</span>
                                            <span style={S.senderBank}>{selectedBank.name}</span>
                                            <span style={S.senderNum}>{formatAccNumber(data.acc_number)}</span>
                                        </div>
                                        <div style={S.senderCheck}><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Amount section label */}
                        <div style={S.sectionLabel}><span>{t('Transfer Amount')}</span></div>

                        {/* ── Fee Tier Info Box ── */}
                        <div style={S.feeTierBox}>
                            <div style={S.feeTierHeader}>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1a7a47" strokeWidth="2.2">
                                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <span style={S.feeTierTitle}>{t('Fee Structure')}</span>
                            </div>
                            <div style={S.feeTierGrid}>
                                <div style={{ ...S.feeTierItem, ...(enteredNum > 0 && enteredNum < 100000 ? S.feeTierItemActive : {}) }}>
                                    <div style={S.feeTierRange}>{"< ฿100,000"}</div>
                                    <div style={S.feeTierPct}>{littleAmountFee}%</div>
                                    <div style={S.feeTierLabel}>{t('fee applied')}</div>
                                    {enteredNum > 0 && enteredNum < 100000 && (
                                        <div style={S.feeTierActiveDot} />
                                    )}
                                </div>
                                <div style={S.feeTierDivider}>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c8e6d0" strokeWidth="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </div>
                                <div style={{ ...S.feeTierItem, ...(enteredNum >= 100000 ? S.feeTierItemBigActive : {}) }}>
                                    <div style={S.feeTierRange}>{"≥ ฿100,000"}</div>
                                    <div style={{ ...S.feeTierPct, color: enteredNum >= 100000 ? "#0d9488" : "#1a7a47" }}>{bigAmountFee}%</div>
                                    <div style={S.feeTierLabel}>{t('fee applied')}</div>
                                    {enteredNum >= 100000 && (
                                        <div style={{ ...S.feeTierActiveDot, background: "#0d9488" }} />
                                    )}
                                </div>
                            </div>
                            {/* Active tier indicator */}
                            {enteredNum > 0 && !amountError && (
                                <div style={S.feeTierActive}>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    {t('Current fee')}: <strong>{feePercentage}%</strong> ({enteredNum >= 100000 ? `≥ ฿100,000` : `< ฿100,000`})
                                </div>
                            )}
                        </div>

                        {/* Fee toggle */}
                        <div style={S.feeToggleBox}>
                            <div style={S.feeToggleHeader}>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1a7a47" strokeWidth="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <span style={S.feeToggleTitle}>{t('Transfer Charge')}</span>
                            </div>
                            <div style={S.feeRadioRow}>
                                <label style={{ ...S.feeRadioLabel, ...(feeMode === "with-fee" ? S.feeRadioLabelActive : {}) }}>
                                    <input type="radio" name="feeMode" value="with-fee" checked={feeMode === "with-fee"} onChange={() => setFeeMode("with-fee")} style={S.radioHidden} />
                                    <div style={{ ...S.radioCircle, ...(feeMode === "with-fee" ? S.radioCircleActive : {}) }}>{feeMode === "with-fee" && <div style={S.radioDot} />}</div>
                                    <div style={S.feeRadioText}>
                                        <span style={S.feeRadioMain}>{t('Transfer fee included')}</span>
                                        <span style={{ ...S.feeRadioSub, color: feeMode === "with-fee" ? "#1a7a47" : "#a8d5b5" }}>{feePercentage}% {t('deducted')}</span>
                                    </div>
                                    <span style={{ ...S.feeBadge, background: feeMode === "with-fee" ? "rgba(26,122,71,.12)" : "#f4fbf6", color: feeMode === "with-fee" ? "#1a7a47" : "#a8d5b5", borderTopColor: feeMode === "with-fee" ? "rgba(26,122,71,.3)" : "transparent", borderRightColor: feeMode === "with-fee" ? "rgba(26,122,71,.3)" : "transparent", borderBottomColor: feeMode === "with-fee" ? "rgba(26,122,71,.3)" : "transparent", borderLeftColor: feeMode === "with-fee" ? "rgba(26,122,71,.3)" : "transparent" }}>{feePercentage}%</span>
                                </label>
                                <div style={S.feeRadioDivider} />
                                <label style={{ ...S.feeRadioLabel, ...(feeMode === "no-fee" ? S.feeRadioLabelNoFeeActive : {}) }}>
                                    <input type="radio" name="feeMode" value="no-fee" checked={feeMode === "no-fee"} onChange={() => setFeeMode("no-fee")} style={S.radioHidden} />
                                    <div style={{ ...S.radioCircle, ...(feeMode === "no-fee" ? S.radioCircleNoFeeActive : {}) }}>{feeMode === "no-fee" && <div style={S.radioDotTeal} />}</div>
                                    <div style={S.feeRadioText}>
                                        <span style={S.feeRadioMain}>{t('Transfer fee excluded')}</span>
                                        <span style={{ ...S.feeRadioSub, color: feeMode === "no-fee" ? "#0d9488" : "#a8d5b5" }}>{t('fee paid in separately')}</span>
                                    </div>
                                    <span style={{ ...S.feeBadge, background: feeMode === "no-fee" ? "rgba(13,148,136,.10)" : "#f4fbf6", color: feeMode === "no-fee" ? "#0d9488" : "#a8d5b5", borderTopColor: feeMode === "no-fee" ? "rgba(13,148,136,.3)" : "transparent", borderRightColor: feeMode === "no-fee" ? "rgba(13,148,136,.3)" : "transparent", borderBottomColor: feeMode === "no-fee" ? "rgba(13,148,136,.3)" : "transparent", borderLeftColor: feeMode === "no-fee" ? "rgba(13,148,136,.3)" : "transparent" }}>Cash</span>
                                </label>
                            </div>
                        </div>

                        {/* Amount input */}
                        <div style={S.fieldGroup}>
                            <label style={S.label}>
                                {t('Amount')} (THB) <span style={S.req}>*</span>
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
                                    <button key={v} type="button" style={{ ...S.pill, ...(parseFloat(data.entered_amount) === v ? S.pillOn : {}) }} onClick={() => { setData("entered_amount", String(v)); validateAmount(String(v)); }}>฿{v.toLocaleString()}</button>
                                ))}
                            </div>
                        </div>

                        {/* Summary */}
                        {summary && !amountError && (
                            <div style={S.summary}>
                                <div style={S.sumHead}>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1a7a47" strokeWidth="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                    <span style={S.sumTitle}>{t('Transfer Summary')}</span>
                                    <span style={{ marginLeft: "auto", fontSize: "10px", fontWeight: "700", padding: "2px 9px", borderRadius: "999px", background: summary.noFeeMode ? "rgba(13,148,136,.10)" : "rgba(26,122,71,.10)", color: summary.noFeeMode ? "#0d9488" : "#1a7a47", borderWidth: "1px", borderStyle: "solid", borderColor: summary.noFeeMode ? "rgba(13,148,136,.25)" : "rgba(26,122,71,.20)" }}>
                                        {summary.noFeeMode ? t("Fee Paid in Cash") : `${feePercentage}% ${t("Fee Applied")}`}
                                    </span>
                                </div>
                                <div style={S.div} />
                                <SR label={t('Entered Amount')} val={`฿${fmt(summary.entered)} THB`} />
                                <SR label={`${t('Transfer Fee')} (${summary.feePercentage}%)`} val={summary.fee === 0 ? `฿0.00 THB` : summary.noFeeMode ? `+ ฿${fmt(summary.fee)} THB` : `− ฿${fmt(summary.fee)} THB`} red={!summary.noFeeMode && summary.fee > 0} teal={summary.noFeeMode && summary.fee > 0} />
                                {summary.noFeeMode && summary.fee > 0 && (
                                    <div style={S.cashFeeNote}><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0d9488" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{t('Customer pays fee in separately')}</div>
                                )}
                                <div style={S.div} />
                                <div style={{ ...S.sRow, paddingTop: 5 }}>
                                    <span style={S.sumTotalLbl}>{t('Net Receive Amount')}</span>
                                    <span style={S.sumTotal}>฿{fmt(summary.net)} THB</span>
                                </div>
                            </div>
                        )}

                        {/* Submit button — opens popup */}
                        <button type="submit" disabled={!canSubmit} style={{ ...S.btn, ...(!canSubmit ? S.btnOff : {}) }}>
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            {t('Confirm')} / {t('Print')}
                        </button>
                    </form>
                </div>

                <style>{`
                    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap');
                    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
                    input:focus { outline: none; }
                    input::placeholder { color: #a8d5b5; }
                    .bank-row:hover { background: #edfaf3 !important; }
                    @keyframes fadeUp  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
                    @keyframes popDown { from { opacity:0; transform:scaleY(.92) translateY(-6px); } to { opacity:1; transform:scaleY(1) translateY(0); } }
                    @keyframes spin    { to { transform:rotate(360deg); } }
                    @keyframes cardIn  { from{opacity:0;transform:scale(.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
                    @keyframes modalIn { from{opacity:0;transform:scale(.94) translateY(24px)} to{opacity:1;transform:scale(1) translateY(0)} }
                    @keyframes overlayIn { from{opacity:0} to{opacity:1} }
                `}</style>
            </div>
        </>
    );
}

function Field({ label, required, optional, t, error, children, style }) {
    return (
        <div style={{ ...S.fieldGroup, ...style }}>
            <label style={S.label}>{label}{required && <span style={S.req}> *</span>}{optional && <span style={S.opt}> ({t('optional')})</span>}</label>
            {children}
            {error && <span style={S.err}>{error}</span>}
        </div>
    );
}

function SR({ label, val, red, teal }) {
    return (
        <div style={S.sRow}>
            <span style={S.sLbl}>{label}</span>
            <span style={{ ...S.sVal, ...(red ? { color: "#dc2626", fontWeight: 700 } : {}), ...(teal ? { color: "#0d9488", fontWeight: 700 } : {}) }}>{val}</span>
        </div>
    );
}

/* ── Popup styles (green theme) ── */
const P = {
    overlay: { position: "fixed", inset: 0, background: "rgba(5,25,12,.62)", backdropFilter: "blur(8px)", zIndex: 1000, display: "flex", alignItems: "center", justifyContent: "center", padding: "20px 16px", animation: "overlayIn .2s ease both" },
    modal: { background: "#fff", borderRadius: "24px", width: "100%", maxWidth: "520px", boxShadow: "0 32px 80px rgba(26,122,71,.30), 0 8px 24px rgba(0,0,0,.12)", animation: "modalIn .3s cubic-bezier(.22,.68,0,1.2) both", position: "relative", overflow: "hidden", maxHeight: "90vh", display: "flex", flexDirection: "column" },
    modalGlow: { position: "absolute", top: "-60px", right: "-60px", width: "200px", height: "200px", borderRadius: "50%", background: "radial-gradient(circle, rgba(39,174,96,.10) 0%, transparent 70%)", pointerEvents: "none" },
    mHeader: { background: "linear-gradient(140deg, #0d4a26 0%, #1a7a47 40%, #27ae60 80%, #2ecc71 100%)", padding: "28px 28px 24px", textAlign: "center", position: "relative", overflow: "hidden", flexShrink: 0 },
    mHeaderBg: { position: "absolute", inset: 0, background: "radial-gradient(ellipse at 50% 0%, rgba(150,255,180,.22) 0%, transparent 65%)", pointerEvents: "none" },
    mIconWrap: { position: "relative", zIndex: 1, marginBottom: "10px" },
    mIcon: { width: "48px", height: "48px", borderRadius: "14px", background: "rgba(255,255,255,.18)", display: "flex", alignItems: "center", justifyContent: "center", margin: "0 auto", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(255,255,255,.3)", boxShadow: "0 4px 16px rgba(0,0,0,.15)" },
    mTitle: { color: "#fff", fontSize: "18px", fontWeight: "800", letterSpacing: ".3px", position: "relative", zIndex: 1, marginBottom: "4px", fontFamily: "'DM Sans', sans-serif" },
    mSub: { color: "rgba(255,255,255,.7)", fontSize: "12px", position: "relative", zIndex: 1, marginBottom: "10px" },
    mTypeBadge: { display: "inline-flex", alignItems: "center", gap: "5px", background: "rgba(255,255,255,.18)", color: "#fff", borderRadius: "999px", padding: "3px 12px", fontSize: "10.5px", fontWeight: "700", letterSpacing: ".5px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(255,255,255,.3)", position: "relative", zIndex: 1 },
    mBody: { padding: "20px 24px", overflowY: "auto", flex: 1, display: "flex", flexDirection: "column", gap: "16px" },
    section: { display: "flex", flexDirection: "column", gap: "8px" },
    sectionHead: { display: "flex", alignItems: "center", gap: "8px" },
    sectionDot: { width: "6px", height: "6px", borderRadius: "50%", background: "linear-gradient(135deg, #1a7a47, #27ae60)", flexShrink: 0 },
    sectionTitle: { fontSize: "11px", fontWeight: "800", color: "#1a7a47", textTransform: "uppercase", letterSpacing: ".6px" },
    sectionCard: { background: "linear-gradient(145deg, #f4fbf6, #ebf7ee)", borderRadius: "14px", padding: "14px 16px", display: "flex", flexDirection: "column", gap: "10px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(39,174,96,.15)" },
    row: { display: "flex", justifyContent: "space-between", alignItems: "center", gap: "12px" },
    rowLabel: { fontSize: "12.5px", color: "#3a8a58", fontWeight: "500", flexShrink: 0 },
    rowVal: { fontSize: "13px", color: "#0d2e18", fontWeight: "600", textAlign: "right" },
    divider: { height: "1px", background: "rgba(39,174,96,.13)" },
    modeBadge: { fontSize: "10.5px", fontWeight: "700", padding: "3px 10px", borderRadius: "999px", borderWidth: "1px", borderStyle: "solid" },
    cashNote: { display: "flex", alignItems: "center", gap: "6px", fontSize: "11px", color: "#0d9488", fontWeight: "600", background: "rgba(13,148,136,.07)", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(13,148,136,.18)", borderRadius: "8px", padding: "6px 10px" },
    netRow: { display: "flex", justifyContent: "space-between", alignItems: "center", paddingTop: "2px" },
    netLabel: { fontSize: "13.5px", fontWeight: "800", color: "#0d2e18" },
    netAmount: { fontSize: "22px", fontWeight: "800", color: "#1a7a47", letterSpacing: "-.5px", fontFamily: "'DM Sans', sans-serif" },
    senderCard: { background: "linear-gradient(135deg, #1a7a47 0%, #27ae60 100%)", borderRadius: "14px", padding: "14px 16px", display: "flex", alignItems: "center", gap: "12px", position: "relative", overflow: "hidden", boxShadow: "0 6px 20px rgba(26,122,71,.28)" },
    senderCardGlow: { position: "absolute", top: "-20px", right: "-20px", width: "80px", height: "80px", borderRadius: "50%", background: "radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%)", pointerEvents: "none" },
    senderName: { fontSize: "14px", fontWeight: "700", color: "#fff", display: "block" },
    senderBank: { fontSize: "11px", color: "rgba(255,255,255,.65)", display: "block", marginTop: "2px" },
    senderNum: { fontSize: "12px", color: "rgba(255,255,255,.85)", display: "block", fontFamily: "'IBM Plex Mono', monospace", letterSpacing: ".8px", marginTop: "3px" },
    senderSymbol: { fontSize: "10px", fontWeight: "800", color: "rgba(255,255,255,.6)", background: "rgba(255,255,255,.12)", padding: "3px 8px", borderRadius: "999px", flexShrink: 0 },
    notice: { display: "flex", alignItems: "flex-start", gap: "9px", background: "rgba(26,122,71,.05)", borderRadius: "10px", padding: "11px 13px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(26,122,71,.15)" },
    noticeText: { fontSize: "11.5px", color: "#1a7a47", lineHeight: "1.5", fontWeight: "500" },
    mFooter: { padding: "16px 24px 20px", display: "flex", gap: "10px", borderTopWidth: "1px", borderTopStyle: "solid", borderTopColor: "rgba(39,174,96,.10)", flexShrink: 0 },
    backBtn: { flex: 1, display: "flex", alignItems: "center", justifyContent: "center", gap: "7px", padding: "13px 20px", borderRadius: "12px", fontSize: "13.5px", fontWeight: "700", cursor: "pointer", background: "#f4fbf6", color: "#1a7a47", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(26,122,71,.2)", fontFamily: "'DM Sans', sans-serif", transition: "all .15s" },
    confirmBtn: { flex: 2, display: "flex", alignItems: "center", justifyContent: "center", gap: "8px", padding: "13px 20px", borderRadius: "12px", fontSize: "13.5px", fontWeight: "700", cursor: "pointer", background: "linear-gradient(135deg, #0d4a26 0%, #1a7a47 50%, #27ae60 100%)", color: "#fff", border: "none", fontFamily: "'DM Sans', sans-serif", boxShadow: "0 4px 18px rgba(26,122,71,.38)", transition: "all .15s" },
    spin: { width: "15px", height: "15px", borderTopWidth: "2px", borderTopStyle: "solid", borderTopColor: "#fff", borderRightWidth: "2px", borderRightStyle: "solid", borderRightColor: "rgba(255,255,255,.35)", borderBottomWidth: "2px", borderBottomStyle: "solid", borderBottomColor: "rgba(255,255,255,.35)", borderLeftWidth: "2px", borderLeftStyle: "solid", borderLeftColor: "rgba(255,255,255,.35)", borderRadius: "50%", display: "inline-block", animation: "spin .7s linear infinite" },
};

const S = {
    page: { minHeight: "100vh", background: "linear-gradient(160deg, #eaf5ee 0%, #dff0e6 40%, #e8f5ec 100%)", display: "flex", alignItems: "center", justifyContent: "center", padding: "40px 16px", fontFamily: "'DM Sans', 'Nunito', sans-serif", position: "relative", overflow: "hidden" },
    orb1: { position: "fixed", top: "-180px", right: "-120px", width: "500px", height: "500px", borderRadius: "50%", background: "radial-gradient(circle, rgba(39,174,96,.12) 0%, transparent 70%)", pointerEvents: "none" },
    orb2: { position: "fixed", bottom: "-150px", left: "-100px", width: "440px", height: "440px", borderRadius: "50%", background: "radial-gradient(circle, rgba(26,122,71,.10) 0%, transparent 70%)", pointerEvents: "none" },
    orb3: { position: "fixed", top: "40%", left: "60%", width: "300px", height: "300px", borderRadius: "50%", background: "radial-gradient(circle, rgba(34,197,94,.07) 0%, transparent 70%)", pointerEvents: "none" },
    card: { background: "#fff", borderRadius: "28px", boxShadow: "0 20px 60px rgba(26,122,71,.14), 0 4px 16px rgba(0,0,0,.06)", width: "100%", maxWidth: "660px", animation: "fadeUp .5s cubic-bezier(.22,.68,0,1.2) both", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(39,174,96,.10)", marginTop: "80px", overflow: "hidden" },
    header: { background: "linear-gradient(140deg, #0d4a26 0%, #1a7a47 40%, #27ae60 80%, #2ecc71 100%)", padding: "32px 36px 28px", textAlign: "center", position: "relative", overflow: "hidden" },
    headerGlow: { position: "absolute", inset: 0, background: "radial-gradient(ellipse at 50% 0%, rgba(150,255,180,.20) 0%, transparent 60%)", pointerEvents: "none" },
    hIconWrap: { position: "relative", zIndex: 1, marginBottom: "12px" },
    hIcon: { width: "52px", height: "52px", borderRadius: "16px", background: "rgba(255,255,255,.15)", display: "flex", alignItems: "center", justifyContent: "center", margin: "0 auto", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(255,255,255,.25)", boxShadow: "0 4px 20px rgba(0,0,0,.12)" },
    hTitle: { fontFamily: "'DM Sans', sans-serif", fontSize: "24px", fontWeight: "700", color: "#fff", letterSpacing: "2.5px", marginBottom: "4px", position: "relative", zIndex: 1 },
    hSub: { color: "rgba(255,255,255,.75)", fontSize: "12.5px", letterSpacing: ".5px", marginBottom: "12px", position: "relative", zIndex: 1 },
    hBadge: { display: "inline-flex", alignItems: "center", gap: "5px", background: "rgba(255,255,255,.18)", color: "#fff", borderRadius: "999px", padding: "4px 14px", fontSize: "11px", fontWeight: "700", letterSpacing: ".7px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(255,255,255,.3)", position: "relative", zIndex: 1 },
    form: { padding: "24px 26px 32px", display: "flex", flexDirection: "column", gap: "16px" },
    row: { display: "flex", gap: "12px" },
    fieldGroup: { display: "flex", flexDirection: "column", gap: "5px", position: "relative" },
    label: { fontSize: "12px", fontWeight: "700", color: "#0d4a26", letterSpacing: ".3px", display: "flex", alignItems: "center", gap: "5px" },
    req: { color: "#27ae60" },
    opt: { color: "#a8d5b5", fontWeight: "600", fontSize: "11px" },
    input: { borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "#c8e6d0", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#c8e6d0", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#c8e6d0", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#c8e6d0", borderTopLeftRadius: "11px", borderTopRightRadius: "11px", borderBottomLeftRadius: "11px", borderBottomRightRadius: "11px", padding: "11px 14px", fontSize: "13.5px", color: "#0d2e18", background: "#f8fdf9", transition: "border-top-color .2s, border-right-color .2s, border-bottom-color .2s, border-left-color .2s, box-shadow .2s", width: "100%", fontFamily: "'DM Sans', sans-serif" },
    inputErr: { borderTopColor: "#ef4444", borderRightColor: "#ef4444", borderBottomColor: "#ef4444", borderLeftColor: "#ef4444", background: "#fef2f2" },
    err: { fontSize: "11.5px", color: "#ef4444", fontWeight: "700", display: "flex", alignItems: "center", gap: "4px" },
    sectionLabel: { display: "flex", alignItems: "center", gap: "7px", fontSize: "11.5px", fontWeight: "700", color: "#1a7a47", letterSpacing: ".4px", textTransform: "uppercase", marginBottom: "-4px" },
    fromSection: { background: "linear-gradient(145deg, #f4fbf6 0%, #ebf7ee 100%)", borderRadius: "18px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(39,174,96,.16)", overflow: "hidden", boxShadow: "0 4px 20px rgba(26,122,71,.08), inset 0 1px 0 rgba(255,255,255,.8)" },
    fromHeader: { background: "linear-gradient(135deg, rgba(26,122,71,.06) 0%, rgba(39,174,96,.06) 100%)", padding: "14px 18px", display: "flex", alignItems: "center", justifyContent: "space-between", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(39,174,96,.12)" },
    fromHeaderLeft: { display: "flex", alignItems: "center", gap: "10px" },
    fromIconBox: { width: "32px", height: "32px", borderRadius: "10px", background: "linear-gradient(135deg, #1a7a47, #27ae60)", display: "flex", alignItems: "center", justifyContent: "center", boxShadow: "0 3px 10px rgba(26,122,71,.30)" },
    fromTitle: { display: "block", fontSize: "13.5px", fontWeight: "700", color: "#0d4a26" },
    fromSubTitle: { display: "block", fontSize: "10.5px", color: "#27ae60", marginTop: "1px" },
    fromSecureBadge: { display: "flex", alignItems: "center", gap: "4px", fontSize: "10px", fontWeight: "700", color: "#22c55e", background: "rgba(34,197,94,.08)", padding: "3px 9px", borderRadius: "999px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(34,197,94,.2)", letterSpacing: ".4px" },
    fromBody: { padding: "16px 18px", display: "flex", flexDirection: "column", gap: "13px" },
    fromFieldGroup: { display: "flex", flexDirection: "column", gap: "5px", position: "relative" },
    fromLabel: { fontSize: "11.5px", fontWeight: "700", color: "#1a7a47", display: "flex", alignItems: "center", gap: "5px", letterSpacing: ".2px" },
    fieldHint: { marginLeft: "auto", fontSize: "10px", color: "#a8d5b5", fontWeight: "600", letterSpacing: ".2px" },
    fromInput: { borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "#c8e6d0", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#c8e6d0", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#c8e6d0", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#c8e6d0", borderTopLeftRadius: "11px", borderTopRightRadius: "11px", borderBottomLeftRadius: "11px", borderBottomRightRadius: "11px", padding: "11px 14px", fontSize: "13.5px", color: "#0d2e18", background: "#fff", transition: "border-top-color .2s, border-right-color .2s, border-bottom-color .2s, border-left-color .2s, box-shadow .2s", width: "100%", fontFamily: "'DM Sans', sans-serif" },
    fromInputFocus: { borderTopColor: "#1a7a47", borderRightColor: "#1a7a47", borderBottomColor: "#1a7a47", borderLeftColor: "#1a7a47", boxShadow: "0 0 0 3px rgba(26,122,71,.12)" },
    fromInputErr: { borderTopColor: "#ef4444", borderRightColor: "#ef4444", borderBottomColor: "#ef4444", borderLeftColor: "#ef4444", background: "#fef2f2" },
    inputWithIcon: { paddingLeft: "40px" },
    accNumFont: { fontFamily: "'IBM Plex Mono', monospace", letterSpacing: "1px", fontSize: "14px" },
    inputIconLeft: { position: "absolute", left: "13px", top: "50%", transform: "translateY(-50%)", display: "flex", alignItems: "center", zIndex: 1, pointerEvents: "none" },
    inputIconRight: { position: "absolute", right: "12px", top: "50%", transform: "translateY(-50%)", display: "flex", alignItems: "center", zIndex: 1 },
    fromErr: { fontSize: "11.5px", color: "#ef4444", fontWeight: "700", display: "flex", alignItems: "center", gap: "4px" },
    fromDivider: { display: "flex", alignItems: "center", gap: "10px", padding: "0 4px" },
    fromDivLine: { flex: 1, height: "1px", background: "rgba(39,174,96,.15)" },
    fromDivIcon: { width: "24px", height: "24px", borderRadius: "50%", background: "rgba(39,174,96,.10)", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 },
    senderCard: { display: "flex", alignItems: "center", gap: "12px", background: "linear-gradient(135deg, #1a7a47 0%, #27ae60 100%)", borderRadius: "14px", padding: "14px 16px", position: "relative", overflow: "hidden", animation: "cardIn .3s cubic-bezier(.22,.68,0,1.2) both", boxShadow: "0 6px 24px rgba(26,122,71,.30)" },
    senderCardGlow: { position: "absolute", top: "-30px", right: "-30px", width: "100px", height: "100px", borderRadius: "50%", background: "radial-gradient(circle, rgba(255,255,255,.12) 0%, transparent 70%)", pointerEvents: "none" },
    senderInfo: { flex: 1, display: "flex", flexDirection: "column", gap: "2px" },
    senderName: { fontSize: "14px", fontWeight: "700", color: "#fff" },
    senderBank: { fontSize: "11px", color: "rgba(255,255,255,.65)" },
    senderNum: { fontSize: "12px", color: "rgba(255,255,255,.85)", fontFamily: "'IBM Plex Mono', monospace", letterSpacing: ".8px", marginTop: "2px" },
    senderCheck: { width: "28px", height: "28px", borderRadius: "50%", background: "rgba(255,255,255,.15)", display: "flex", alignItems: "center", justifyContent: "center", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(255,255,255,.4)", flexShrink: 0 },
    dropTrigger: { borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "#c8e6d0", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#c8e6d0", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#c8e6d0", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#c8e6d0", borderTopLeftRadius: "11px", borderTopRightRadius: "11px", borderBottomLeftRadius: "11px", borderBottomRightRadius: "11px", padding: "8px 12px", background: "#fff", cursor: "pointer", display: "flex", alignItems: "center", justifyContent: "space-between", transition: "border-top-color .2s, border-right-color .2s, border-bottom-color .2s, border-left-color .2s, box-shadow .2s", userSelect: "none", minHeight: "50px" },
    dropTriggerOpen: { borderTopColor: "#1a7a47", borderRightColor: "#1a7a47", borderBottomColor: "#1a7a47", borderLeftColor: "#1a7a47", boxShadow: "0 0 0 3px rgba(26,122,71,.12)", borderBottomLeftRadius: 0, borderBottomRightRadius: 0 },
    dropInner: { display: "flex", alignItems: "center", gap: "10px", flex: 1 },
    dropSelText: { fontSize: "13.5px", fontWeight: "700", color: "#0d2e18", flex: 1 },
    dropSelCode: { fontSize: "10.5px", color: "#27ae60", marginTop: "1px" },
    dropPlh: { color: "#a8d5b5", fontSize: "13px" },
    dropBadge: { fontSize: "10px", color: "#1a7a47", fontWeight: "700", background: "#d4f0de", padding: "2px 8px", borderRadius: "999px" },
    dummyCircle: { width: "36px", height: "36px", borderRadius: "50%", background: "#e8f8ee", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 },
    chevron: { color: "#27ae60", display: "flex", transition: "transform .2s ease" },
    dropPanel: { position: "absolute", top: "100%", left: 0, right: 0, background: "#fff", borderTopWidth: 0, borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "#1a7a47", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "#1a7a47", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "#1a7a47", borderBottomLeftRadius: "14px", borderBottomRightRadius: "14px", boxShadow: "0 20px 50px rgba(26,122,71,.18)", zIndex: 999, animation: "popDown .18s ease both", overflow: "hidden" },
    searchRow: { display: "flex", alignItems: "center", gap: "8px", padding: "10px 14px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "#e8f8ee", background: "#f4fbf6" },
    searchInput: { border: "none", outline: "none", background: "transparent", fontSize: "13px", color: "#0d2e18", flex: 1, fontFamily: "'DM Sans', sans-serif" },
    searchClear: { border: "none", background: "none", color: "#a8d5b5", cursor: "pointer", fontSize: "12px", padding: "0 2px", lineHeight: 1 },
    dropScroll: { maxHeight: "260px", overflowY: "auto" },
    dropItem: { display: "flex", alignItems: "center", gap: "12px", padding: "10px 16px", cursor: "pointer", transition: "background .12s", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "#f2fbf5" },
    dropItemActive: { background: "#edf8f1" },
    bankMeta: { display: "flex", flexDirection: "column", flex: 1 },
    bankNm: { fontSize: "13px", fontWeight: "700", color: "#0d2e18" },
    bankCd: { fontSize: "10.5px", color: "#27ae60", marginTop: "1px" },
    tickCircle: { width: "22px", height: "22px", borderRadius: "50%", background: "linear-gradient(135deg,#1a7a47,#27ae60)", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 },
    noResult: { padding: "24px 16px", textAlign: "center", color: "#a8d5b5", fontSize: "13px", display: "flex", flexDirection: "column", alignItems: "center", gap: "8px" },
    // ── Fee Tier Box ──
    feeTierBox: { background: "linear-gradient(145deg, #f0faf4 0%, #e6f5ec 100%)", borderRadius: "16px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(39,174,96,.16)", overflow: "hidden" },
    feeTierHeader: { display: "flex", alignItems: "center", gap: "7px", padding: "10px 16px 9px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(39,174,96,.10)", background: "rgba(26,122,71,.04)" },
    feeTierTitle: { fontSize: "11px", fontWeight: "800", color: "#1a7a47", textTransform: "uppercase", letterSpacing: ".5px" },
    feeTierGrid: { display: "flex", alignItems: "center", padding: "12px 16px", gap: "4px" },
    feeTierItem: { flex: 1, textAlign: "center", padding: "10px 8px", borderRadius: "10px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(39,174,96,.12)", background: "#fff", position: "relative", transition: "all .2s" },
    feeTierItemActive: { background: "rgba(220,38,38,.05)", borderColor: "rgba(220,38,38,.25)", boxShadow: "0 2px 10px rgba(220,38,38,.10)" },
    feeTierItemBigActive: { background: "rgba(13,148,136,.07)", borderColor: "rgba(13,148,136,.30)", boxShadow: "0 2px 10px rgba(13,148,136,.12)" },
    feeTierRange: { fontSize: "11px", fontWeight: "700", color: "#3a8a58", marginBottom: "4px" },
    feeTierPct: { fontSize: "22px", fontWeight: "800", color: "#1a7a47", fontFamily: "'DM Sans', sans-serif", lineHeight: 1.1 },
    feeTierLabel: { fontSize: "10px", color: "#a8d5b5", marginTop: "2px", fontWeight: "600" },
    feeTierActiveDot: { position: "absolute", top: "6px", right: "6px", width: "7px", height: "7px", borderRadius: "50%", background: "#dc2626" },
    feeTierDivider: { display: "flex", alignItems: "center", justifyContent: "center", padding: "0 4px", flexShrink: 0 },
    feeTierActive: { margin: "0 16px 12px", fontSize: "11.5px", fontWeight: "600", color: "#1a7a47", background: "rgba(26,122,71,.07)", borderRadius: "8px", padding: "7px 12px", display: "flex", alignItems: "center", gap: "6px", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(26,122,71,.15)" },
    // ──
    feeToggleBox: { background: "linear-gradient(145deg, #f4fbf6 0%, #ebf7ee 100%)", borderRadius: "16px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "rgba(39,174,96,.16)", overflow: "hidden", boxShadow: "0 2px 12px rgba(26,122,71,.07)" },
    feeToggleHeader: { display: "flex", alignItems: "center", gap: "7px", padding: "11px 16px 10px", borderBottomWidth: "1px", borderBottomStyle: "solid", borderBottomColor: "rgba(39,174,96,.12)", background: "linear-gradient(135deg, rgba(26,122,71,.05) 0%, rgba(39,174,96,.05) 100%)" },
    feeToggleTitle: { fontSize: "11.5px", fontWeight: "700", color: "#1a7a47", letterSpacing: ".3px", textTransform: "uppercase" },
    feeRadioRow: { display: "flex", flexDirection: "column" },
    feeRadioDivider: { height: "1px", background: "rgba(39,174,96,.10)", margin: "0 16px" },
    feeRadioLabel: { display: "flex", alignItems: "center", gap: "12px", padding: "12px 16px", cursor: "pointer", transition: "background .15s", userSelect: "none" },
    feeRadioLabelActive: { background: "rgba(26,122,71,.05)" },
    feeRadioLabelNoFeeActive: { background: "rgba(13,148,136,.04)" },
    radioHidden: { position: "absolute", opacity: 0, width: 0, height: 0, pointerEvents: "none" },
    radioCircle: { width: "18px", height: "18px", borderRadius: "50%", flexShrink: 0, borderWidth: "2px", borderStyle: "solid", borderColor: "#c8e6d0", display: "flex", alignItems: "center", justifyContent: "center", background: "#fff", transition: "border-color .15s" },
    radioCircleActive: { borderColor: "#1a7a47", background: "#fff" },
    radioCircleNoFeeActive: { borderColor: "#0d9488", background: "#fff" },
    radioDot: { width: "8px", height: "8px", borderRadius: "50%", background: "#1a7a47" },
    radioDotTeal: { width: "8px", height: "8px", borderRadius: "50%", background: "#0d9488" },
    feeRadioText: { display: "flex", flexDirection: "column", gap: "1px", flex: 1 },
    feeRadioMain: { fontSize: "13px", fontWeight: "700", color: "#0d2e18" },
    feeRadioSub: { fontSize: "11px", fontWeight: "500", transition: "color .15s" },
    feeBadge: { fontSize: "11px", fontWeight: "700", padding: "3px 10px", borderRadius: "999px", borderTopWidth: "1px", borderTopStyle: "solid", borderRightWidth: "1px", borderRightStyle: "solid", borderBottomWidth: "1px", borderBottomStyle: "solid", borderLeftWidth: "1px", borderLeftStyle: "solid", transition: "all .15s", flexShrink: 0 },
    amtWrap: { position: "relative" },
    amtPfx: { position: "absolute", left: "14px", top: "50%", transform: "translateY(-50%)", color: "#1a7a47", fontWeight: "800", fontSize: "16px", zIndex: 1 },
    amtInput: { paddingLeft: "30px" },
    pills: { display: "flex", flexWrap: "wrap", gap: "6px", marginTop: "6px" },
    pill: { padding: "5px 12px", borderRadius: "999px", borderWidth: "1.5px", borderStyle: "solid", borderColor: "#c8e6d0", background: "#f8fdf9", color: "#1a7a47", fontSize: "11.5px", fontWeight: "700", cursor: "pointer", transition: "all .15s", fontFamily: "'DM Sans', sans-serif" },
    pillOn: { background: "#1a7a47", color: "#fff", borderColor: "#1a7a47" },
    summary: { background: "linear-gradient(135deg, #f4fbf6 0%, #e2f5e9 100%)", borderTopLeftRadius: "14px", borderTopRightRadius: "14px", borderBottomLeftRadius: "14px", borderBottomRightRadius: "14px", padding: "16px 18px", borderTopWidth: "1.5px", borderTopStyle: "solid", borderTopColor: "rgba(39,174,96,.16)", borderRightWidth: "1.5px", borderRightStyle: "solid", borderRightColor: "rgba(39,174,96,.16)", borderBottomWidth: "1.5px", borderBottomStyle: "solid", borderBottomColor: "rgba(39,174,96,.16)", borderLeftWidth: "1.5px", borderLeftStyle: "solid", borderLeftColor: "rgba(39,174,96,.16)", animation: "fadeUp .3s ease both" },
    sumHead: { display: "flex", alignItems: "center", gap: "7px", marginBottom: "9px" },
    sumTitle: { fontWeight: "800", color: "#1a7a47", fontSize: "13px" },
    div: { height: "1px", background: "rgba(39,174,96,.15)", margin: "7px 0" },
    sRow: { display: "flex", justifyContent: "space-between", alignItems: "center", padding: "3px 0" },
    sLbl: { fontSize: "12.5px", color: "#3a8a58" },
    sVal: { fontSize: "12.5px", color: "#0d2e18", fontWeight: "600" },
    sumTotalLbl: { fontSize: "13.5px", fontWeight: "800", color: "#0d2e18" },
    sumTotal: { fontSize: "20px", fontWeight: "800", color: "#1a7a47", letterSpacing: "-.5px" },
    cashFeeNote: { display: "flex", alignItems: "center", gap: "6px", fontSize: "11px", color: "#0d9488", fontWeight: "600", background: "rgba(13,148,136,.07)", borderWidth: "1px", borderStyle: "solid", borderColor: "rgba(13,148,136,.18)", borderRadius: "8px", padding: "6px 10px", marginTop: "2px" },
    btn: { marginTop: "4px", background: "linear-gradient(135deg, #0d4a26 0%, #1a7a47 50%, #27ae60 100%)", color: "#fff", border: "none", borderRadius: "14px", padding: "15px 24px", fontSize: "14.5px", fontWeight: "700", cursor: "pointer", display: "flex", alignItems: "center", justifyContent: "center", gap: "9px", letterSpacing: ".3px", boxShadow: "0 6px 24px rgba(26,122,71,.35)", transition: "opacity .2s, box-shadow .2s", fontFamily: "'DM Sans', sans-serif" },
    btnOff: { opacity: .4, cursor: "not-allowed", boxShadow: "none" },
    spin: { width: "16px", height: "16px", borderTopWidth: "2.5px", borderTopStyle: "solid", borderTopColor: "#fff", borderRightWidth: "2.5px", borderRightStyle: "solid", borderRightColor: "rgba(255,255,255,.35)", borderBottomWidth: "2.5px", borderBottomStyle: "solid", borderBottomColor: "rgba(255,255,255,.35)", borderLeftWidth: "2.5px", borderLeftStyle: "solid", borderLeftColor: "rgba(255,255,255,.35)", borderRadius: "50%", display: "inline-block", animation: "spin .7s linear infinite" },
};