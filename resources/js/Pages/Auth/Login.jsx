import { useState } from 'react';
import { useForm, Head, Link, usePage } from '@inertiajs/react';
const appUrl = window.location.origin;
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
    min-height:100vh;display:flex;align-items:center;justify-content:center;
    background:
      radial-gradient(ellipse 70% 55% at 80% 10%, rgba(91,45,142,0.4) 0%, transparent 55%),
      radial-gradient(ellipse 55% 50% at 10% 90%, rgba(155,89,182,0.22) 0%, transparent 55%),
      var(--dark);
    padding:2rem 1rem;position:relative;overflow:hidden;
  }
  .auth-root::before,.auth-root::after{content:'';position:absolute;border-radius:50%;filter:blur(90px);pointer-events:none;}
  .auth-root::before{width:420px;height:420px;background:rgba(91,45,142,0.2);top:-120px;right:-100px;animation:float1 14s ease-in-out infinite;}
  .auth-root::after {width:300px;height:300px;background:rgba(155,89,182,0.14);bottom:-80px;left:-80px;animation:float2 18s ease-in-out infinite;}
  @keyframes float1{0%,100%{transform:translate(0,0)}50%{transform:translate(-25px,20px)}}
  @keyframes float2{0%,100%{transform:translate(0,0)}50%{transform:translate(20px,-25px)}}
  .card{width:100%;max-width:420px;background:var(--glass);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid var(--border);border-radius:24px;padding:2.5rem 2.25rem;box-shadow:var(--glow),0 12px 60px rgba(0,0,0,0.55);position:relative;z-index:1;animation:slideUp 0.6s cubic-bezier(0.16,1,0.3,1) both;}
  @keyframes slideUp{from{opacity:0;transform:translateY(28px) scale(0.98)}to{opacity:1;transform:translateY(0) scale(1)}}
  .card::before{content:'';position:absolute;top:0;left:15%;right:15%;height:2px;background:linear-gradient(90deg,transparent,var(--secondary),transparent);border-radius:2px;}
  .brand{text-align:center;margin-bottom:2rem;}
  .brand-icon{width:64px;height:64px;margin:0 auto 1rem;background:linear-gradient(135deg,var(--primary),var(--secondary));border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;box-shadow:0 6px 24px rgba(91,45,142,0.55);position:relative;}
  .brand-icon::after{content:'';position:absolute;inset:-4px;border-radius:24px;border:1px solid rgba(155,89,182,0.4);animation:pulse-ring 2.5s ease-in-out infinite;}
  @keyframes pulse-ring{0%,100%{opacity:0.6;transform:scale(1)}50%{opacity:0;transform:scale(1.12)}}
  .brand h1{font-family:'Cinzel',serif;font-size:1.65rem;color:#fff;letter-spacing:0.05em;}
  .brand p{font-size:0.8rem;color:rgba(255,255,255,0.4);margin-top:0.35rem;font-weight:300;letter-spacing:0.1em;text-transform:uppercase;}
  .alert{padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.4rem;font-size:0.85rem;display:flex;align-items:center;gap:0.5rem;animation:fadeIn 0.4s ease;}
  @keyframes fadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
  .alert-success{background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.35);color:#6ddc9a;}
  .alert-error  {background:rgba(231,76,60,0.15);border:1px solid rgba(231,76,60,0.35);color:#ff8a7a;}
  .form-group{margin-bottom:1.2rem;}
  .form-label{display:block;font-size:0.77rem;font-weight:600;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.09em;margin-bottom:0.45rem;}
  .form-label .req{color:var(--secondary);}
  .input-wrap{position:relative;}
  .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1rem;color:rgba(155,89,182,0.6);pointer-events:none;}
  .form-input{width:100%;padding:0.78rem 1rem 0.78rem 2.8rem;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:12px;color:#fff;font-family:'DM Sans',sans-serif;font-size:0.95rem;outline:none;transition:border-color 0.25s,background 0.25s,box-shadow 0.25s;}
  .form-input::placeholder{color:rgba(255,255,255,0.22);}
  .form-input:focus{border-color:var(--secondary);background:rgba(155,89,182,0.1);box-shadow:0 0 0 3px rgba(155,89,182,0.15);}
  .form-input.has-error{border-color:var(--error);}
  .toggle-pw{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.3);font-size:1rem;transition:color 0.2s;padding:0;}
  .toggle-pw:hover{color:var(--secondary);}
  .error-msg{font-size:0.75rem;color:var(--error);margin-top:0.35rem;display:flex;align-items:center;gap:0.3rem;}
  .remember-row{display:flex;align-items:center;gap:0.5rem;margin-bottom:1.4rem;font-size:0.83rem;color:rgba(255,255,255,0.45);}
  .remember-row input[type="checkbox"]{accent-color:var(--secondary);width:15px;height:15px;cursor:pointer;}
  .remember-row label{cursor:pointer;}
  .btn-submit{width:100%;padding:0.9rem;background:linear-gradient(135deg,var(--primary),var(--secondary));border:none;border-radius:12px;color:#fff;font-family:'DM Sans',sans-serif;font-size:0.95rem;font-weight:600;letter-spacing:0.04em;cursor:pointer;position:relative;overflow:hidden;transition:transform 0.15s,box-shadow 0.25s,opacity 0.25s;box-shadow:0 5px 24px rgba(91,45,142,0.5);}
  .btn-submit:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 10px 32px rgba(91,45,142,0.65);}
  .btn-submit:active:not(:disabled){transform:translateY(0);}
  .btn-submit:disabled{opacity:0.65;cursor:not-allowed;}
  .footer-link{text-align:center;margin-top:1.3rem;font-size:0.83rem;color:rgba(255,255,255,0.38);}
  .footer-link a{color:var(--secondary);text-decoration:none;font-weight:600;transition:color 0.2s;}
  .footer-link a:hover{color:#c084fc;}
  .security-badge{display:flex;align-items:center;justify-content:center;gap:0.4rem;margin-top:1.1rem;font-size:0.7rem;color:rgba(255,255,255,0.18);letter-spacing:0.05em;}
  .attempts-warn{font-size:0.73rem;color:rgba(231,76,60,0.7);text-align:center;margin-bottom:0.8rem;}
`;

/* ─── No layout — auth pages are standalone ─────────────────────── */
Login.layout = null;

export default function Login() {
    /* ── shared props from HandleInertiaRequests ── */
    const { props } = usePage();
    const translations = props.translations ?? {};
    const t = (key) => translations[key] ?? key;

    /* ── flash.greet (matches your middleware: flash.greet) ── */
    const greet = props.flash?.greet ?? null;

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '', password: '', remember: false,
    });
    const [showPw, setShowPw]     = useState(false);
    const [touched, setTouched]   = useState({});
    const [attempts, setAttempts] = useState(0);
    const touch = (f) => setTouched(p => ({ ...p, [f]: true }));

    const clientErrors = {
        name:     !data.name     ? t('Username is required.')  : '',
        password: !data.password ? t('Password is required.')  : '',
    };
    const getErr = (f) => (touched[f] && clientErrors[f]) || errors[f] || '';

    function submit(e) {
        e.preventDefault();
        setTouched({ name: true, password: true });
        if (Object.values(clientErrors).some(Boolean)) return;
        post(route('login'), {
            onError: () => { setAttempts(p => p + 1); reset('password'); },
        });
    }

    return (
        <>
            <Head title={t('Staff Login')} />
            <style dangerouslySetInnerHTML={{ __html: css }} />

            <div className="auth-root">
                <div className="card">

                    <div className="brand">
                        <div className="brand-icon"> <img src={`${appUrl}/website/assets/logo/logo2.png`} style={{ borderRadius: '10px', height: '50px', width: '50px' }} /></div>
                        <h1>G+ Services</h1>
                        <p>{t('Secure Sign In')}</p>
                    </div>

                    {/* flash.greet — shown after successful registration redirect */}
                    {greet && (
                        <div className="alert alert-success">✅ {greet}</div>
                    )}

                    {/* server-side credential error */}
                    {errors.name && !clientErrors.name && (
                        <div className="alert alert-error">🚫 {errors.name}</div>
                    )}

                    {attempts >= 3 && (
                        <p className="attempts-warn">
                            ⚠ {attempts} {t('failed attempt(s).')} {t('Account may be locked after 5 tries.')}
                        </p>
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
                                    className={`form-input ${getErr('name') ? 'has-error' : ''}`}
                                    placeholder={t('Enter Username')}
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    onBlur={() => touch('name')}
                                    autoComplete="username"
                                />
                            </div>
                            {getErr('name') && <p className="error-msg">⚠ {getErr('name')}</p>}
                        </div>

                        {/* Password */}
                        <div className="form-group">
                            <label className="form-label">
                                {t('Password')} <span className="req">*</span>
                            </label>
                            <div className="input-wrap">
                                <span className="input-icon">🔒</span>
                                <input type={showPw ? 'text' : 'password'}
                                    className={`form-input ${getErr('password') ? 'has-error' : ''}`}
                                    placeholder={t('Enter password')}
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    onBlur={() => touch('password')}
                                    autoComplete="current-password"
                                />
                                <button type="button" className="toggle-pw"
                                    onClick={() => setShowPw(p => !p)}
                                    aria-label={showPw ? t('Hide password') : t('Show password')}>
                                    {showPw ? '🙈' : '👁️'}
                                </button>
                            </div>
                            {getErr('password') && <p className="error-msg">⚠ {getErr('password')}</p>}
                        </div>

                        {/* Remember me */}
                        <div className="remember-row">
                            <input id="remember" type="checkbox"
                                checked={data.remember}
                                onChange={e => setData('remember', e.target.checked)}
                            />
                            <label htmlFor="remember">{t('Remember me for 30 days')}</label>
                        </div>

                        <button type="submit" className="btn-submit" disabled={processing}>
                            {`🔓 ${t('Sign In to Portal')}`}
                        </button>
                    </form>

                    <p className="footer-link">
                        {t('No account yet?')}{' '}
                        <Link href={route('register')}>{t('Register')}</Link>
                    </p>
                    <div className="security-badge">
                        🛡 {t('Protected by rate-limiting · Sessions encrypted')}
                    </div>
                </div>
            </div>
        </>
    );
}