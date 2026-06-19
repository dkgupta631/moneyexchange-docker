import { Link, Head, usePage, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function WebLayout({ children }) {

    // ── Shared props from HandleInertiaRequests ─────────────────────────
    const { translations, locale, ziggy, auth, flash } = usePage().props;
    const appUrl = window.location.origin;
    const t = (key) => translations[key] ?? key;
    const isActive = (name) => route().current(name);

    // ── Auth user (from your middleware: auth.user) ─────────────────────
    const user = auth?.user ?? null;

    // ── Global flash toast (flash.greet from any controller redirect) ───
    // Auto-dismisses after 4 seconds. Re-triggers on every new greet message.
    const greet = flash?.greet ?? null;
    const [flashMsg, setFlashMsg] = useState(greet);

    useEffect(() => {
        if (!greet) return;
        setFlashMsg(greet);
        const timer = setTimeout(() => setFlashMsg(null), 6000);
        return () => clearTimeout(timer);
    }, [greet]);

    // ── Logout form ─────────────────────────────────────────────────────
    const { get: logoutGet, processing: loggingOut } = useForm({});
    const handleLogout = (e) => {
        e.preventDefault();
        logoutGet(route('logout'));
    };

    // ── Language map ────────────────────────────────────────────────────
    const languages = {
        en: {
            name: "English",
            flag: "/website/assets/flags/en.png"
        },
        "th-TH": {
            name: "Thai",
            flag: "/website/assets/flags/th.png"
        },
        km: {
            name: "Khmer",
            flag: "/website/assets/flags/kh.png"
        }
    };
    const currentLang = languages[locale] ?? languages['en'];

    const capitalizeFirst = (text) => {
    if (!text) return ''
        return text.charAt(0).toUpperCase() + text.slice(1)
    }

    return (
        <>
            <Head>
                <meta head-key="description" name="description" content={t('Exchange currency rates help customers convert money correctly, make secure payments, and ensure transparency and trust in international transactions')} />
                <link rel="icon" type="image/svg+xml" href={`${appUrl}/website/assets/logo/logo2.png`} />
            </Head>

            {/* <!-- Navbar & Hero Start --> */}
            <div className="container-fluid position-relative p-0">
                <nav className={`navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0 ${isActive('home') ? '' : 'bg-primary'}`}>
                    <Link href="/" className="navbar-brand p-0">
                        <img src={`${appUrl}/website/assets/logo/logo2.png`} style={{ borderRadius: '10px' }} /><span class="text-white fw-bold"> G+ Services</span>
                    </Link>
                    <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span className="fa fa-bars"></span>
                    </button>
                    <div className="collapse navbar-collapse" id="navbarCollapse">
                        <div className="navbar-nav ms-auto py-0">

                            <Link href={route('home')} className={`nav-item nav-link ${isActive('home') ? 'active' : ''}`}>
                                {t('Home')}
                            </Link>

                            {/* ── Auth-aware: show Staff Login OR user + Logout ── */}
                            {user ? (
                                <>
                                    {/* Logged-in: show username badge + Logout */}
                                    <span className="nav-item nav-link d-flex align-items-center gap-2 pe-0">
                                        <span className="badge bg-light text-primary fw-semibold">
                                            👤 { capitalizeFirst(user.name) }
                                        </span>
                                        <span className="badge bg-secondary text-white text-uppercase" style={{ fontSize: '0.62rem', lineHeight: '13px' }}>
                                            {user.role}
                                        </span>
                                    </span>
                                    <button
                                        type="button"
                                        className="nav-item nav-link btn btn-link text-danger fw-semibold"
                                        style={{ cursor: 'pointer', border: 'none', background: 'none' }}
                                        onClick={handleLogout}
                                        disabled={loggingOut}
                                    >
                                        {loggingOut ? '…' : `⬡ ${t('Logout')}`}
                                    </button>
                                </>
                            ) : (
                                /* Guest: show Staff Login */
                                <Link
                                    href={route('teller.login')}
                                    className={`nav-item nav-link ${isActive('teller.login') ? 'active' : ''}`}
                                >
                                    {t('Staff Login')}
                                </Link>
                            )}

                            <Link href={route('showExchangeRate')} className={`nav-item nav-link ${isActive('showExchangeRate') ? 'active' : ''}`}>
                                {t('Today Exchange Rate')}
                            </Link>
                            <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${isActive('open.moneyexchange.*') ? 'active' : ''}`}>
                                {t('Money Exchange')}
                            </Link>

                            {/* Language dropdown */}
                            <div className="nav-item dropdown">
                                <a href="#" className="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    <img src={currentLang.flag} width="20" style={{ marginRight: '6px' }} />
                                    {t(currentLang.name)}
                                </a>
                                <div className="dropdown-menu m-0">
                                    <a href="/lang/en" className="dropdown-item">
                                        <img src="/website/assets/flags/en.png" width="20" className="me-2" />{t('English')}
                                    </a>
                                    <a href="/lang/th-TH" className="dropdown-item">
                                        <img src="/website/assets/flags/th.png" width="20" className="me-2" />{t('Thai')}
                                    </a>
                                    <a href="/lang/km" className="dropdown-item">
                                        <img src="/website/assets/flags/kh.png" width="20" className="me-2" />{t('Khmer')}
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </nav>
            </div>
            {/* <!-- Navbar & Hero End --> */}

            <main>
                {children}
            </main>

            {/* <!-- Footer Start --> */}
            <div className="container-fluid py-3 bg-primary text-light footer wow fadeIn" data-wow-delay="0.1s">
                <div className="container py-1 px-lg-5">
                    <div className="row g-5">
                        <div className="col-md-6 col-lg-3">
                            <h5 className="text-white mb-4">{t('Quick Contacts')}</h5>
                            <p><i className="fa fa-map-marker-alt me-3"></i>Beer City Poipet Zone 3</p>
                            <p><i className="fa fa-phone-alt me-3"></i>(+855) 973294524</p>
                            <p><i className="fa fa-envelope me-3"></i>beercity@gmail.com</p>
                            <div className="d-flex">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-facebook-f"></i></a>
                                <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-youtube"></i></a>
                                <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-instagram"></i></a>
                                <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-telegram"></i></a>
                            </div>
                        </div>
                        <div className="col-md-6 col-lg-3">
                            <h5 className="text-white mb-4">{t('Market Fluctuations')}</h5>
                            <Link href={route('showExchangeRate')} className="btn btn-link">{t('Today Exchange Rate')}</Link>
                            <Link href={route('open.moneyexchange.form')} className="btn btn-link">{t('Money Transfer')}</Link>
                            <Link href={route('open.moneyexchange.form')} className="btn btn-link">{t('Administrator Login')}</Link>
                            <a className="btn btn-link" href="#">{t('Contact with Grand Diamond City')}</a>
                        </div>
                        <div className="col-md-6 col-lg-3">
                            <h5 className="text-white mb-4">{t('Faster International Payments')}</h5>
                            <div className="row g-2">
                                <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-1.jpg`} alt="" /></div>
                                <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-2.jpg`} alt="" /></div>
                                <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-3.jpg`} alt="" /></div>
                                <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-4.jpg`} alt="" /></div>
                                <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-5.jpg`} alt="" /></div>
                                <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-6.jpg`} alt="" /></div>
                            </div>
                        </div>
                        <div className="col-md-6 col-lg-3">
                            <h5 className="text-white mb-4">{t('Why_People_Like_us')}</h5>
                            <p>{t('Exchange currency rates help customers convert money correctly, make secure payments, and ensure transparency and trust in international transactions')}</p>
                            <div className="position-relative w-100 mt-3">
                                <input className="form-control border-0 rounded-pill w-100 ps-4 pe-5" type="text" placeholder={t('Enter email')} style={{ height: '48px' }} />
                                <button type="button" className="btn shadow-none position-absolute top-0 end-0 mt-1 me-2">
                                    <i className="fa fa-paper-plane text-primary fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="container px-lg-5">
                    <div className="copyright">
                        <div className="row">
                            <div className="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                {t('Copyright')} &copy; 2025-2026 {t('by')} <a className="border-bottom" href="https://zaffrantech.com/">Zaffran Tech</a> {t('All_Rights_Reserved')}
                            </div>
                            <div className="col-md-6 text-center text-md-end">
                                <div className="footer-menu">
                                    <Link href={route('home')}>{t('Home')}</Link>
                                    <a href="#">{t('Cookies')}</a>
                                    <a href="#">{t('Help')}</a>
                                    <a href="#">{t('FQAs')}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* <!-- Footer End --> */}

            {/* <!-- Back to Top --> */}
            <a href="#" className="btn btn-lg btn-primary btn-lg-square back-to-top pt-2">
                <i className="bi bi-arrow-up"></i>
            </a>

            {/* ── Global Flash Toast ── */}
            {/* Shown on every page — driven by flash.greet from any controller */}
            <style>{`
                @keyframes wl-toast-in  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
                @keyframes wl-toast-out { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(40px); } }
                .wl-toast {
                    position: fixed;
                    bottom: 28px;
                    right: 24px;
                    z-index: 99999;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    background: rgba(26, 10, 46, 0.92);
                    border: 1px solid #7EECC4;
                    border-radius: 10px;
                    padding: 13px 20px;
                    color: #7EECC4;
                    font-size: 14px;
                    font-weight: 500;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.35), 0 0 0 1px rgba(126,236,196,0.15);
                    min-width: 260px;
                    max-width: 380px;
                    animation: wl-toast-in 0.35s cubic-bezier(0.16,1,0.3,1) both;
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                }
                .wl-toast-icon { font-size: 18px; flex-shrink: 0; }
                .wl-toast-close {
                    margin-left: auto;
                    background: none;
                    border: none;
                    color: rgba(126,236,196,0.5);
                    cursor: pointer;
                    font-size: 16px;
                    line-height: 1;
                    padding: 0 0 0 8px;
                    transition: color 0.2s;
                    flex-shrink: 0;
                }
                .wl-toast-close:hover { color: #7EECC4; }
            `}</style>

            {flashMsg && (
                <div className="wl-toast" role="alert">
                    <span className="wl-toast-icon">✓</span>
                    <span>{flashMsg}</span>
                    <button
                        className="wl-toast-close"
                        onClick={() => setFlashMsg(null)}
                        aria-label="Dismiss"
                    >✕</button>
                </div>
            )}
        </>
    );
}