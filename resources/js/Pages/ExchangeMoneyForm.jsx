import { useForm, Head, usePage } from '@inertiajs/react';
import { useEffect, useState, useRef } from "react";
import axios from "axios";

export default function ExchangeMoneyForm() {

    const { translations } = usePage().props;
    const t = (key) => translations[key] ?? key;

    const currencies = [
        { value: "Dollar", label: "US Dollar", code: "USD", flag: "/website/assets/flags/USD.png" },
        { value: "Baht",   label: "Thai Baht",  code: "THB", flag: "/website/assets/flags/THB.png" },
        { value: "Riel",   label: "Khmer Riel", code: "KHR", flag: "/website/assets/flags/KHR.png" },
    ];

    const getCurrencySymbol  = (val) => ({ Dollar: "$", Baht: "฿", Riel: "៛" }[val] || "");
    const getCurrencyByValue = (val) => currencies.find(c => c.value === val);

    const { data, setData, post, errors, processing } = useForm({
        customer_name:    "",
        phone:            "",
        from_currency:    "Dollar",
        to_currency:      "Baht",
        enter_amount:     "",
        exchange_type:    "Normal",
        where_to_send:    "TRF-IN",
        receive_type:     "Cash to Cash",
        exchange_rate_id: "",
        exchange_rate:    "",
        subtotal:         "",
        service_fee:      "",
        final_amount:     "",
    });

    // ── Single source of truth for both display values ──
    const [fromDisplayAmount, setFromDisplayAmount] = useState("");
    const [toDisplayAmount,   setToDisplayAmount]   = useState("");
    const [displayRate,       setDisplayRate]       = useState("");
    const [serviceFee,        setServiceFee]        = useState("");
    const [fromOpen,          setFromOpen]          = useState(false);
    const [toOpen,            setToOpen]            = useState(false);

    const fromTimer = useRef(null);
    const toTimer   = useRef(null);

    const closeAll = () => { setFromOpen(false); setToOpen(false); };

    // ── Fetch when FROM amount typed ──
    const fetchFromAmount = async (from_currency, to_currency, exchange_type, enter_amount) => {
        if (!enter_amount || Number(enter_amount) <= 0) {
            setToDisplayAmount("");
            setDisplayRate(""); setServiceFee("");
            setData(prev => ({ ...prev, subtotal: "", service_fee: "", final_amount: "", exchange_rate: "", exchange_rate_id: "" }));
            return;
        }
        try {
            const res       = await axios.post("/get-exchange-rate", { from_currency, to_currency, exchange_type, enter_amount });
            const rawRate   = res.data.exchange_rate;
            const buyOrSell = res.data.buy_or_sell;
            const total     = parseFloat(res.data.total).toFixed(2);
            const fee       = res.data.service_fee;
            const rid       = res.data.exchange_rate_id ?? "";
            const oneUnit   = buyOrSell === 'sell' ? parseFloat(rawRate) : parseFloat((1 / rawRate).toFixed(6));

            setDisplayRate(oneUnit);
            setToDisplayAmount(total);
            setServiceFee(fee);
            setData(prev => ({ ...prev, enter_amount: enter_amount, exchange_rate: rawRate, exchange_rate_id: rid, subtotal: total, service_fee: fee, final_amount: total }));
        } catch (e) { console.error(e); }
    };

    // ── Fetch when TO amount typed (reverse calc) ──
    const fetchToAmount = async (from_currency, to_currency, exchange_type, to_amount_val) => {
        if (!to_amount_val || Number(to_amount_val) <= 0) {
            setFromDisplayAmount("");
            setDisplayRate(""); setServiceFee("");
            setData(prev => ({ ...prev, enter_amount: "", subtotal: "", service_fee: "", final_amount: "", exchange_rate: "", exchange_rate_id: "" }));
            return;
        }
        try {
            const res       = await axios.post("/get-exchange-rate", { from_currency, to_currency, exchange_type, enter_amount: 1 });
            const rawRate   = res.data.exchange_rate;
            const buyOrSell = res.data.buy_or_sell;
            const fee       = res.data.service_fee;
            const rid       = res.data.exchange_rate_id ?? "";
            const oneUnit   = buyOrSell === 'sell' ? parseFloat(rawRate) : parseFloat((1 / rawRate).toFixed(6));
            const fromCalc  = (parseFloat(to_amount_val) / oneUnit).toFixed(2);

            setDisplayRate(oneUnit);
            setFromDisplayAmount(fromCalc);
            setServiceFee(fee);
            setData(prev => ({ ...prev, enter_amount: fromCalc, exchange_rate: rawRate, exchange_rate_id: rid, subtotal: to_amount_val, service_fee: fee, final_amount: to_amount_val }));
        } catch (e) { console.error(e); }
    };

    // ── FROM input: update state immediately, debounce API call ──
    const handleFromChange = (val) => {
        setFromDisplayAmount(val);
        clearTimeout(fromTimer.current);
        fromTimer.current = setTimeout(() => {
            fetchFromAmount(data.from_currency, data.to_currency, data.exchange_type, val);
        }, 400);
    };

    // ── TO input: update state immediately, debounce API call ──
    const handleToChange = (val) => {
        setToDisplayAmount(val);
        clearTimeout(toTimer.current);
        toTimer.current = setTimeout(() => {
            fetchToAmount(data.from_currency, data.to_currency, data.exchange_type, val);
        }, 400);
    };

    // ── Swap currencies ──
    const handleSwap = () => {
        const prevFrom    = data.from_currency;
        const prevTo      = data.to_currency;
        const prevFromAmt = fromDisplayAmount;
        const prevToAmt   = toDisplayAmount;
        setData(prev => ({ ...prev, from_currency: prevTo, to_currency: prevFrom }));
        setFromDisplayAmount(prevToAmt);
        setToDisplayAmount(prevFromAmt);
        setTimeout(() => fetchFromAmount(prevTo, prevFrom, data.exchange_type, prevToAmt), 0);
    };

    // ── Prevent same currency on both sides ──
    const handleFromCurrencyChange = (newVal) => {
        let newTo = data.to_currency;
        if (newVal === data.to_currency) newTo = currencies.find(c => c.value !== newVal)?.value || "";
        setFromOpen(false);
        setData(prev => ({ ...prev, from_currency: newVal, to_currency: newTo }));
        setTimeout(() => fetchFromAmount(newVal, newTo, data.exchange_type, fromDisplayAmount), 0);
    };

    const handleToCurrencyChange = (newVal) => {
        let newFrom = data.from_currency;
        if (newVal === data.from_currency) newFrom = currencies.find(c => c.value !== newVal)?.value || "";
        setToOpen(false);
        setData(prev => ({ ...prev, to_currency: newVal, from_currency: newFrom }));
        setTimeout(() => fetchFromAmount(newFrom, newVal, data.exchange_type, fromDisplayAmount), 0);
    };

    function submit(e) {
        e.preventDefault();
        post("/calculateMoney");
    }

    const fromCurrency = getCurrencyByValue(data.from_currency);
    const toCurrency   = getCurrencyByValue(data.to_currency);
    const hasResult    = Number(data.enter_amount) > 0 && toDisplayAmount;

    return (
        <>
            <Head title={t('International')} />

            <style>{`
                .xform { font-family: 'Segoe UI', system-ui, sans-serif; }

                .xcard {
                    display: grid;
                    grid-template-columns: 1fr 52px 1fr;
                    border: 1.5px solid #d4b3e8;
                    border-radius: 14px;
                    background: #fff;
                    box-shadow: 0 2px 16px rgba(91,45,142,.07);
                    position: relative;
                    overflow: visible;
                }
                .xside {
                    padding: 14px 18px 16px;
                    display: flex;
                    flex-direction: column;
                    min-width: 0;
                    position: relative;
                }
                .xside-from { border-right: 1.5px solid #d4b3e8; }

                .xside-label {
                    font-size: 11px; font-weight: 700;
                    letter-spacing: .7px; text-transform: uppercase;
                    color: #9aa8be; margin-top: 6px;
                }

                /* Both inputs are structurally identical */
                .xamount-row { display: flex; align-items: baseline; gap: 4px; margin-top: 10px; }
                .xsymbol     { font-size: 24px; font-weight: 700; color: #1a1f36; line-height: 1; flex-shrink: 0; }
                .xsymbol-to  { color: #5B2D8E; }
                .xamount-input {
                    border: none; outline: none;
                    font-size: 24px; font-weight: 700; color: #1a1f36;
                    width: 100%; background: transparent; min-width: 0;
                    caret-color: #1a1f36;
                }
                .xamount-input-to  { color: #5B2D8E; caret-color: #5B2D8E; }
                .xamount-input::placeholder { color: #d0d8e8; font-weight: 400; }
                .xamount-input::-webkit-inner-spin-button,
                .xamount-input::-webkit-outer-spin-button { -webkit-appearance: none; }

                .xcur-trigger {
                    display: inline-flex; align-items: center; gap: 7px;
                    cursor: pointer; user-select: none; width: fit-content;
                    padding: 5px 10px 5px 6px;
                    border: 1.5px solid #e8d5f5; border-radius: 999px;
                    background: #faf5ff; transition: all .15s;
                }
                .xcur-trigger:hover { background: #f0e8f8; border-color: #c9a5e0; }
                .xcur-trigger img { width: 24px; height: 16px; border-radius: 2px; object-fit: cover; flex-shrink: 0; }
                .xcur-code { font-size: 14px; font-weight: 700; color: #1a1f36; }
                .xcur-name { font-size: 12px; color: #888; }
                .xcur-chevron { color: #aaa; font-size: 10px; margin-left: 2px; }

                .xswap-col {
                    display: flex; align-items: center; justify-content: center;
                    border-right: 1.5px solid #d4b3e8;
                }
                .xswap-btn {
                    width: 36px; height: 36px; border-radius: 50%;
                    border: 1.5px solid #d4b3e8; background: #fff;
                    display: flex; align-items: center; justify-content: center;
                    cursor: pointer; font-size: 17px; color: #5B2D8E;
                    transition: all .18s; flex-shrink: 0; line-height: 1;
                }
                .xswap-btn:hover { background: #5B2D8E; color: #fff; border-color: #5B2D8E; }

                .xdropdown {
                    position: absolute; top: calc(100% + 6px); left: 0;
                    background: #fff; border: 1.5px solid #d4b3e8;
                    border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.11);
                    z-index: 200; min-width: 210px; overflow: hidden;
                }
                .xdropdown-right { left: auto; right: 0; }
                .xdrop-item {
                    display: flex; align-items: center; gap: 10px;
                    padding: 10px 14px; cursor: pointer; font-size: 14px;
                    transition: background .12s;
                }
                .xdrop-item:hover { background: #f0e8f8; }
                .xdrop-item.xdrop-active { background: #ede0f5; }
                .xdrop-item img { width: 24px; height: 16px; border-radius: 2px; object-fit: cover; }
                .xdrop-code { font-weight: 700; color: #1a1f36; }
                .xdrop-name { color: #999; font-size: 12px; margin-left: auto; }
                .xdrop-check { color: #5B2D8E; font-size: 13px; margin-left: 4px; }

                .xrate-line {
                    display: flex; align-items: center; flex-wrap: wrap;
                    gap: 8px; margin-top: 8px; padding: 0 2px;
                    font-size: 13px; color: #444;
                }
                .xrate-line b { color: #1a1f36; }
                .xrate-badge {
                    font-size: 11px; background: #f0e8f8; color: #5B2D8E;
                    padding: 2px 9px; border-radius: 20px; font-weight: 600;
                }
                .xtrack-btn {
                    margin-left: auto; font-size: 12px; font-weight: 600; color: #5B2D8E;
                    background: #f0e8f8; border: 1.5px solid #d4b3e8;
                    border-radius: 8px; padding: 4px 14px; cursor: pointer;
                    text-decoration: none; white-space: nowrap; transition: all .15s;
                }
                .xtrack-btn:hover { background: #5B2D8E; color: #fff; border-color: #5B2D8E; }

                .xsummary {
                    background: linear-gradient(135deg,#f5f0fa,#ede0f5);
                    border: 1.5px solid #d4b3e8; border-radius: 12px; padding: 16px 20px;
                }
                .xsummary h6 { font-weight: 700; color: #1a1f36; margin-bottom: 12px; font-size: 15px; }
                .xsum-row { display: flex; justify-content: space-between; font-size: 14px; color: #555; margin-bottom: 5px; }
                .xsum-total {
                    font-weight: 700; font-size: 15px; color: #1a1f36;
                    border-top: 1px solid #d4b3e8; padding-top: 9px; margin-top: 5px;
                }
                .xerr { color: #e53e3e; font-size: 12px; margin-top: 3px; }

                .xpopular { font-size: 11px; color: #aaa; margin-top: 6px; }
                .xpopular span {
                    display: inline-flex; align-items: center; gap: 3px;
                    background: #f5f0fa; border: 1px solid #e8d5f5;
                    border-radius: 6px; padding: 2px 8px; cursor: pointer;
                    transition: all .12s; margin-right: 4px;
                }
                .xpopular span:hover { background: #ede0f5; }

                @media (max-width: 540px) {
                    .xcard { grid-template-columns: 1fr; }
                    .xside-from { border-right: none; border-bottom: 1.5px solid #d4b3e8; }
                    .xswap-col { border-right: none; border-bottom: 1.5px solid #d4b3e8; padding: 10px 18px; justify-content: flex-start; }
                    .xtrack-btn { margin-left: 0; }
                }
            `}</style>

            <div className="container-fluid py-5 xform" onClick={closeAll}>
                <br /><br /><br />
                 <div className=" px-lg-5">    {/*  container */} 
                    <div className="row justify-content-center">
                        <div className="col-lg-7">

                            <div className="section-title text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                                <h6 className="d-inline text-primary ps-4">
                                    {t('Exchange your money with confidence. Our rates are updated daily based on the international market to ensure fair and transparent transactions')}
                                </h6>
                            </div>

                            <div className="wow fadeInUp" data-wow-delay="0.3s">
                                <h4 className="text-center mb-4">{t('Live Exchange Rates – USD | THB | KHR')}</h4>

                                <form onSubmit={submit}>
                                    <div className="row g-3">

                                        <div className="col-md-6">
                                            <div className="form-floating">
                                                <input type="text"
                                                    className={`form-control ${errors.customer_name ? 'is-invalid' : ''}`}
                                                    value={data.customer_name}
                                                    onChange={e => setData('customer_name', e.target.value)}
                                                    placeholder=" " />
                                                <label>{t('Customer name')} ({t('optional')})</label>
                                                {errors.customer_name && <div className="xerr">{errors.customer_name}</div>}
                                            </div>
                                        </div>

                                        <div className="col-md-6">
                                            <div className="form-floating">
                                                <input type="text"
                                                    className={`form-control ${errors.phone ? 'is-invalid' : ''}`}
                                                    value={data.phone}
                                                    onChange={e => setData('phone', e.target.value)}
                                                    placeholder=" " />
                                                <label>{t('Phone Number')} ({t('optional')})</label>
                                                {errors.phone && <div className="xerr">{errors.phone}</div>}
                                            </div>
                                        </div>

                                        {/* ── Exchanger Card ── */}
                                        <div className="col-12">
                                            <div className="xcard" onClick={e => e.stopPropagation()}>

                                                {/* FROM side */}
                                                <div className="xside xside-from">
                                                    <div className="xcur-trigger"
                                                        onClick={() => { setFromOpen(v => !v); setToOpen(false); }}>
                                                        <img src={fromCurrency?.flag} alt={fromCurrency?.code} />
                                                        <span className="xcur-code">{fromCurrency?.code}</span>
                                                        <span className="xcur-name">- {fromCurrency?.label}</span>
                                                        <span className="xcur-chevron">▼</span>
                                                    </div>
                                                    {fromOpen && (
                                                        <div className="xdropdown">
                                                            {currencies.map(c => (
                                                                <div key={c.value}
                                                                    className={`xdrop-item ${c.value === data.from_currency ? 'xdrop-active' : ''}`}
                                                                    onClick={() => handleFromCurrencyChange(c.value)}>
                                                                    <img src={c.flag} alt={c.code} />
                                                                    <span className="xdrop-code">{c.code}</span>
                                                                    <span className="xdrop-name">{c.label}</span>
                                                                    {c.value === data.from_currency && <span className="xdrop-check">✓</span>}
                                                                </div>
                                                            ))}
                                                        </div>
                                                    )}
                                                    <div className="xamount-row">
                                                        <span className="xsymbol">{getCurrencySymbol(data.from_currency)}</span>
                                                        <input
                                                            type="number"
                                                            className="xamount-input"
                                                            placeholder="0"
                                                            value={fromDisplayAmount}
                                                            onChange={e => handleFromChange(e.target.value)}
                                                        />
                                                    </div>
                                                    {errors.enter_amount && <div className="xerr">{errors.enter_amount}</div>}
                                                    <div className="xside-label">{t('From')}</div>
                                                </div>

                                                {/* SWAP */}
                                                <div className="xswap-col">
                                                    <button type="button" className="xswap-btn" onClick={handleSwap} title="Swap">⇄</button>
                                                </div>

                                                {/* TO side — identical structure to FROM */}
                                                <div className="xside">
                                                    <div className="xcur-trigger"
                                                        onClick={() => { setToOpen(v => !v); setFromOpen(false); }}>
                                                        <img src={toCurrency?.flag} alt={toCurrency?.code} />
                                                        <span className="xcur-code">{toCurrency?.code}</span>
                                                        <span className="xcur-name">- {toCurrency?.label}</span>
                                                        <span className="xcur-chevron">▼</span>
                                                    </div>
                                                    {toOpen && (
                                                        <div className="xdropdown xdropdown-right">
                                                            {currencies.map(c => (
                                                                <div key={c.value}
                                                                    className={`xdrop-item ${c.value === data.to_currency ? 'xdrop-active' : ''}`}
                                                                    onClick={() => handleToCurrencyChange(c.value)}>
                                                                    <img src={c.flag} alt={c.code} />
                                                                    <span className="xdrop-code">{c.code}</span>
                                                                    <span className="xdrop-name">{c.label}</span>
                                                                    {c.value === data.to_currency && <span className="xdrop-check">✓</span>}
                                                                </div>
                                                            ))}
                                                        </div>
                                                    )}
                                                    <div className="xamount-row">
                                                        <span className="xsymbol xsymbol-to">{getCurrencySymbol(data.to_currency)}</span>
                                                        <input
                                                            type="number"
                                                            className="xamount-input xamount-input-to"
                                                            placeholder="0"
                                                            value={toDisplayAmount}
                                                            onChange={e => handleToChange(e.target.value)}
                                                        />
                                                    </div>
                                                    <div className="xside-label">{t('To')}</div>
                                                </div>
                                            </div>

                                            {/* Rate line */}
                                            {displayRate && (
                                                <div className="xrate-line">
                                                    <b>1.00 {fromCurrency?.code} = {displayRate} {toCurrency?.code}</b>
                                                    <span className="xrate-badge">{t('Real time update')}</span>
                                                    <a href={route('showExchangeRate')} target="_blank" className="xtrack-btn">{t('Track exchange rates')}</a>
                                                </div>
                                            )}

                                        </div>

                                        {/* Exchange Summary */}
                                        {hasResult && (
                                            <div className="col-12">
                                                <div className="xsummary wow zoomIn">
                                                    <h6>{t('Exchange Summary')}</h6>
                                                    <div className="xsum-row">
                                                        <span>{t('Amount')}</span>
                                                        <span>{getCurrencySymbol(data.from_currency)}{data.enter_amount} {fromCurrency?.code}</span>
                                                    </div>
                                                    <div className="xsum-row">
                                                        <span>{t('Exchange Rate')}</span>
                                                        <span>1 {fromCurrency?.code} = {displayRate} {toCurrency?.code}</span>
                                                    </div>
                                                    <div className="xsum-row">
                                                        <span>{t('Service Fee')}</span>
                                                        <span>{serviceFee}</span>
                                                    </div>
                                                    <div className="xsum-row xsum-total">
                                                        <span>{t('Total Receive amount')}</span>
                                                        <span>{getCurrencySymbol(data.to_currency)}{toDisplayAmount} {toCurrency?.code}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        )}

                                        {/* Exchange Type */}
                                        <div className="col-12">
                                            <div className="form-floating">
                                                <select className="form-control" value={data.exchange_type}
                                                    onChange={e => {
                                                        setData("exchange_type", e.target.value);
                                                        fetchFromAmount(data.from_currency, data.to_currency, e.target.value, fromDisplayAmount);
                                                    }}>
                                                    <option value="Normal">{t('Normal')}</option>
                                                    <option value="Standard">{t('Standard')}</option>
                                                </select>
                                                <label>{t('Exchange Type')}</label>
                                            </div>
                                        </div>

                                        {/* Submit */}
                                        <div className="col-12">
                                            <button
                                                className="btn btn-primary w-100 py-3 fw-bold"
                                                type="submit"
                                                disabled={processing || !hasResult}
                                            >
                                                 {processing ? (
                                                                    <> {t('Processing')}…</>
                                                                ) : (
                                                                    <>
                                                                        {t('Confirm')} / {t('Print')} ⟶
                                                                    </>
                                                                )}
                                            </button>
                                        </div>

                                        <p className="text-center text-muted mb-4" style={{ fontSize: 13 }}>
                                            {t('Secure and fast currency exchange with the best daily rates')}.
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </>
    );
}