import { useState } from 'react';
import { useForm, Head, Link, usePage } from '@inertiajs/react';

/* ─── CSS ───────────────────────────────────────────────────────── */
const css = `
  @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap');
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary:   #5B2D8E;
    --secondary: #9B59B6;
    --dark:      #1A0A2E;
    --error:     #E74C3C;
    --success:   #27AE60;
    --border:    rgba(155,89,182,0.3);
    --glass:     rgba(255,255,255,0.06);
    --glow:      0 0 40px rgba(91,45,142,0.4);
  }
  body { background: var(--dark); font-family: 'DM Sans', sans-serif; }
  .auth-root {
    min-height: 100vh; display: flex; align-items: center; justify-content: center;
    background:
      radial-gradient(ellipse 80% 60% at 20% 0%, rgba(91,45,142,0.35) 0%, transparent 60%),
      radial-gradient(ellipse 60% 50% at 80% 100%, rgba(155,89,182,0.25) 0%, transparent 55%),
      var(--dark);
    padding: 2rem 1rem; position: relative; overflow: hidden;
  }
  .auth-root::before, .auth-root::after {
    content: ''; position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none;
  }
  .auth-root::before { width:500px;height:500px;background:rgba(91,45,142,0.18);top:-150px;left:-150px;animation:float1 12s ease-in-out infinite; }
  .auth-root::after  { width:380px;height:380px;background:rgba(155,89,182,0.12);bottom:-100px;right:-100px;animation:float2 15s ease-in-out infinite; }
  @keyframes float1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(30px,20px)} }
  @keyframes float2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-30px)} }
  .card {
    width:100%;max-width:480px;background:var(--glass);
    backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
    border:1px solid var(--border);border-radius:24px;padding:2.5rem 2.25rem;
    box-shadow:var(--glow),0 8px 60px rgba(0,0,0,0.5);position:relative;z-index:1;
    animation:slideUp 0.6s cubic-bezier(0.16,1,0.3,1) both;
    margin-top: 100px;
  }
  @keyframes slideUp { from{opacity:0;transform:translateY(32px)} to{opacity:1;transform:translateY(0)} }
  .card::before { content:'';position:absolute;top:0;left:10%;right:10%;height:2px;background:linear-gradient(90deg,transparent,var(--secondary),transparent);border-radius:2px; }
  .brand { text-align:center; }
  .brand-icon { width:56px;height:56px;margin:0 auto 0.75rem;background:linear-gradient(135deg,var(--primary),var(--secondary));border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;box-shadow:0 4px 20px rgba(91,45,142,0.5); }
  .brand h1 { font-family:'Cinzel',serif;font-size:1.5rem;color:#fff;letter-spacing:0.04em; }
  .brand p  { font-size:0.82rem;color:rgba(255,255,255,0.45);margin-top:0.3rem;font-weight:300;letter-spacing:0.08em;text-transform:uppercase; }
  .alert { padding:0.75rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.85rem;display:flex;align-items:center;gap:0.5rem;animation:fadeIn 0.3s ease; }
  @keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }
  .alert-success { background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.3);color:#6ddc9a; }
  .alert-error   { background:rgba(231,76,60,0.15);border:1px solid rgba(231,76,60,0.3);color:#ff8a7a; }
  .form-group { margin-bottom:1.1rem;position:relative; }
  .form-label { display:flex;align-items:center;gap:0.4rem;font-size:0.78rem;font-weight:600;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.45rem; }
  .form-label .req { color:var(--secondary); }
  .input-wrap { position:relative; }
  .input-icon { position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1rem;color:rgba(155,89,182,0.6);pointer-events:none;z-index:1; }
  .form-input { width:100%;padding:0.75rem 1rem 0.75rem 2.75rem;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:12px;color:#fff;font-family:'DM Sans',sans-serif;font-size:0.95rem;outline:none;transition:border-color 0.25s,background 0.25s,box-shadow 0.25s; }
  .form-input::placeholder { color:rgba(255,255,255,0.25); }
  .form-input:focus { border-color:var(--secondary);background:rgba(155,89,182,0.1);box-shadow:0 0 0 3px rgba(155,89,182,0.15); }
  .form-input.has-error { border-color:var(--error); }
  .form-input.is-valid  { border-color:var(--success); }
  .toggle-pw { position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.35);font-size:1rem;transition:color 0.2s;padding:0; }
  .toggle-pw:hover { color:var(--secondary); }
  .error-msg { font-size:0.75rem;color:var(--error);margin-top:0.35rem;display:flex;align-items:center;gap:0.3rem;animation:fadeIn 0.2s ease; }
  .pw-strength { margin-top:0.5rem; }
  .pw-bars { display:flex;gap:4px;margin-bottom:0.25rem; }
  .pw-bar { height:3px;flex:1;border-radius:2px;background:rgba(255,255,255,0.1);transition:background 0.3s; }
  .pw-bar.s1{background:#e74c3c} .pw-bar.s2{background:#e67e22} .pw-bar.s3{background:#f1c40f} .pw-bar.s4{background:#27ae60}
  .pw-strength-label { font-size:0.7rem;letter-spacing:0.05em; }
  .pw-rules { margin-top:0.5rem;display:grid;grid-template-columns:1fr 1fr;gap:0.25rem; }
  .pw-rule { font-size:0.72rem;color:rgba(255,255,255,0.3);display:flex;align-items:center;gap:0.3rem;transition:color 0.2s; }
  .pw-rule.ok { color:var(--success); }
  .pw-rule-dot { width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0; }
  .divider { border:none;border-top:1px solid rgba(255,255,255,0.08);margin:1.5rem 0 1.25rem; }
  .btn-submit { width:100%;padding:0.85rem;background:linear-gradient(135deg,var(--primary),var(--secondary));border:none;border-radius:12px;color:#fff;font-family:'DM Sans',sans-serif;font-size:0.95rem;font-weight:600;letter-spacing:0.03em;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.15s,box-shadow 0.25s,opacity 0.25s;box-shadow:0 4px 20px rgba(91,45,142,0.45); }
  .btn-submit:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 8px 28px rgba(91,45,142,0.6)} .btn-submit:active:not(:disabled){transform:translateY(0)} .btn-submit:disabled{opacity:0.7;cursor:not-allowed}
  .footer-link{text-align:center;margin-top:1.25rem;font-size:0.83rem;color:rgba(255,255,255,0.4);}
  .footer-link a{color:var(--secondary);text-decoration:none;font-weight:600;transition:color 0.2s;}
  .footer-link a:hover{color:#c084fc;}
  .security-badge{display:flex;align-items:center;justify-content:center;gap:0.4rem;margin-top:1rem;font-size:0.72rem;color:rgba(255,255,255,0.2);letter-spacing:0.05em;}
`;

/* ─── password strength helper ─────────────────────────────────── */
function pwStrength(pw) {
    const rules = {
        length:  pw.length >= 8,
        upper:   /[A-Z]/.test(pw),
        lower:   /[a-z]/.test(pw),
        number:  /[0-9]/.test(pw),
        special: /[^A-Za-z0-9]/.test(pw),
    };
    const score = Object.values(rules).filter(Boolean).length;
    return { score, rules };
}

/* ─── No layout — auth pages are standalone ─────────────────────── */
Register.layout = null;

export default function Register() {
    /* ── shared props from HandleInertiaRequests ── */
    const { props } = usePage();
    const translations = props.translations ?? {};
    const t = (key) => translations[key] ?? key;

    /* ── flash.greet (matches your middleware: flash.greet) ── */
    const greet = props.flash?.greet ?? null;

    const { data, setData, post, processing, errors } = useForm({
        name: '', phoneNumber: '', email: '', password: '', password_confirmation: '',
    });
    const [showPw, setShowPw]           = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);
    const [touched, setTouched]         = useState({});
    const pw                            = pwStrength(data.password);
    const touch = (f) => setTouched(p => ({ ...p, [f]: true }));

    /* client-side validation */
    const clientErrors = {
        name: !data.name
            ? t('Username is required.')
            : data.name.length < 3
                ? t('Minimum 3 characters.')
                : !/^[a-zA-Z0-9_\s]+$/.test(data.name)
                    ? t('Letters, numbers, underscores, spaces only.')
                    : '',
        phoneNumber: !data.phoneNumber
            ? t('Phone number is required.')
            : !/^\+?[0-9]{7,15}$/.test(data.phoneNumber)
                ? t('Enter a valid phone number (7-15 digits).')
                : '',
        email: !data.email
            ? t('Email is required.')
            : !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)
                ? t('Enter a valid email address.')
                : '',
        password: !data.password
            ? t('Password is required.')
            : pw.score < 4
                ? t('Password is too weak.')
                : '',
        password_confirmation: !data.password_confirmation
            ? t('Please confirm your password.')
            : data.password !== data.password_confirmation
                ? t('Passwords do not match.')
                : '',
    };

    const getErr = (f) => (touched[f] && clientErrors[f]) || errors[f] || '';
    const isOk   = (f) => touched[f] && !clientErrors[f] && !errors[f] && data[f];

    function submit(e) {
        e.preventDefault();
        setTouched({ name:true, phoneNumber:true, email:true, password:true, password_confirmation:true });
        if (Object.values(clientErrors).some(Boolean)) return;
        post(route('register'), { onError: () => {} });
    }

    const scoreColor = ['','#e74c3c','#e67e22','#f1c40f','#27ae60','#27ae60'];
    const scoreLabel = ['', t('Weak'), t('Fair'), t('Good'), t('Strong'), t('Very Strong')];

    return (
        <>
            <Head title={t('Create Account')} />
            <style dangerouslySetInnerHTML={{ __html: css }} />

            <div className="auth-root">
                <div className="card">

                    <div className="brand">
                        <div className="brand-icon">🔐</div>
                        <h1>{t('Create Account')}</h1>
                        <p>{t('Staff Registration Portal')}</p>
                    </div>

                    {/* flash.greet from session */}
                    {greet && (
                        <div className="alert alert-success">✅ {greet}</div>
                    )}

                    <form onSubmit={submit} noValidate autoComplete="off">

                        {/* Username */}
                        <div className="form-group">
                            <label className="form-label">
                                {t('Username')} <span className="req">*</span>
                            </label>
                            <div className="input-wrap">
                                <span className="input-icon">👤</span>
                                <input type="text"
                                    className={`form-input ${getErr('name') ? 'has-error' : ''} ${isOk('name') ? 'is-valid' : ''}`}
                                    placeholder={t('e.g. john_doe')}
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    onBlur={() => touch('name')}
                                    autoComplete="username"
                                />
                            </div>
                            {getErr('name') && <p className="error-msg">⚠ {getErr('name')}</p>}
                        </div>

                        {/* Phone */}
                        <div className="form-group">
                            <label className="form-label">
                                {t('Phone Number')} <span className="req">*</span>
                            </label>
                            <div className="input-wrap">
                                <span className="input-icon">📱</span>
                                <input type="tel"
                                    className={`form-input ${getErr('phoneNumber') ? 'has-error' : ''} ${isOk('phoneNumber') ? 'is-valid' : ''}`}
                                    placeholder={t('+855 12 345 678')}
                                    value={data.phoneNumber}
                                    onChange={e => setData('phoneNumber', e.target.value)}
                                    onBlur={() => touch('phoneNumber')}
                                />
                            </div>
                            {getErr('phoneNumber') && <p className="error-msg">⚠ {getErr('phoneNumber')}</p>}
                        </div>

                        {/* Email */}
                        <div className="form-group">
                            <label className="form-label">
                                {t('Email')} <span className="req">*</span>
                            </label>
                            <div className="input-wrap">
                                <span className="input-icon">✉️</span>
                                <input type="email"
                                    className={`form-input ${getErr('email') ? 'has-error' : ''} ${isOk('email') ? 'is-valid' : ''}`}
                                    placeholder={'you@example.com'}
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    onBlur={() => touch('email')}
                                    autoComplete="email"
                                />
                            </div>
                            {getErr('email') && <p className="error-msg">⚠ {getErr('email')}</p>}
                        </div>

                        {/* Password */}
                        <div className="form-group">
                            <label className="form-label">
                                {t('Password')} <span className="req">*</span>
                            </label>
                            <div className="input-wrap">
                                <span className="input-icon">🔑</span>
                                <input type={showPw ? 'text' : 'password'}
                                    className={`form-input ${getErr('password') ? 'has-error' : ''} ${isOk('password') && pw.score >= 4 ? 'is-valid' : ''}`}
                                    placeholder={t('Minimum 8 characters')}
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    onBlur={() => touch('password')}
                                    autoComplete="new-password"
                                />
                                <button type="button" className="toggle-pw"
                                    onClick={() => setShowPw(p => !p)}
                                    aria-label={showPw ? t('Hide password') : t('Show password')}>
                                    {showPw ? '🙈' : '👁️'}
                                </button>
                            </div>

                            {data.password && (
                                <div className="pw-strength">
                                    <div className="pw-bars">
                                        {[1,2,3,4].map(n => (
                                            <div key={n} className={`pw-bar ${pw.score >= n ? `s${Math.min(pw.score,4)}` : ''}`} />
                                        ))}
                                    </div>
                                    <span className="pw-strength-label" style={{ color: scoreColor[Math.min(pw.score,5)] }}>
                                        {scoreLabel[Math.min(pw.score,5)]}
                                    </span>
                                    <div className="pw-rules">
                                        {[
                                            ['length',  t('8+ chars')],
                                            ['upper',   t('Uppercase')],
                                            ['lower',   t('Lowercase')],
                                            ['number',  t('Number')],
                                            ['special', t('Symbol')],
                                        ].map(([k, label]) => (
                                            <span key={k} className={`pw-rule ${pw.rules[k] ? 'ok' : ''}`}>
                                                <span className="pw-rule-dot" />{label}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                            )}
                            {getErr('password') && <p className="error-msg">⚠ {getErr('password')}</p>}
                        </div>

                        {/* Confirm Password */}
                        <div className="form-group">
                            <label className="form-label">
                                {t('Confirm Password')} <span className="req">*</span>
                            </label>
                            <div className="input-wrap">
                                <span className="input-icon">🔒</span>
                                <input type={showConfirm ? 'text' : 'password'}
                                    className={`form-input ${getErr('password_confirmation') ? 'has-error' : ''} ${isOk('password_confirmation') && data.password === data.password_confirmation ? 'is-valid' : ''}`}
                                    placeholder={t('Re-enter password')}
                                    value={data.password_confirmation}
                                    onChange={e => setData('password_confirmation', e.target.value)}
                                    onBlur={() => touch('password_confirmation')}
                                    autoComplete="new-password"
                                />
                                <button type="button" className="toggle-pw"
                                    onClick={() => setShowConfirm(p => !p)}
                                    aria-label={showConfirm ? t('Hide password') : t('Show password')}>
                                    {showConfirm ? '🙈' : '👁️'}
                                </button>
                            </div>
                            {getErr('password_confirmation') && (
                                <p className="error-msg">⚠ {getErr('password_confirmation')}</p>
                            )}
                        </div>

                        <hr className="divider" />

                        <button type="submit" className="btn-submit" disabled={processing}>
                            {`🚀 ${t('Create Staff Account')}`}
                        </button>
                    </form>

                    <p className="footer-link">
                        {t('Already have an account?')}{' '}
                        <Link href={route('teller.login')}>{t('Sign In')}</Link>
                    </p>
                    <div className="security-badge">
                        🛡 {t('256-bit SSL encrypted · Secure registration')}
                    </div>
                </div>
            </div>
        </>
    );
}