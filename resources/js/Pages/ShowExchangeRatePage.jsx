import { useEffect, useState } from "react";
import { Head, Link, usePage } from '@inertiajs/react';
export default function ShowExchangeRatePage({LeftRecords, RightRecords}) {
    // console.log(Leftrecords);
    const { translations, locale, ziggy } = usePage().props;
    const appUrl = window.location.origin;
    const t = (key) => translations[key] ?? key;

    // For Change Background color code Start
  const colors = [
    "#ffffff",
    "#f8f9fa",
    "#fff3cd",
    "#e8f5e9",
    "#e3f2fd",
    "#f3e5f5"
  ];

  const [bg, setBg] = useState(colors[0]);

  // change board background every 5 minutes START
    useEffect(() => {
        const changeColor = () => {
            const random = colors[Math.floor(Math.random() * colors.length)];
            setBg(random);
        };
        changeColor();
        const interval = setInterval(changeColor, 300000); // 5 minutes
        return () => clearInterval(interval);
    }, []);
  // change board background every 5 minutes END

    // for dateTime format code START
        const now = new Date().toLocaleString('en-GB', {
            timeZone: 'Asia/Bangkok',
            day: '2-digit',
            month: '2-digit',
            year: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
            });

        const formatted = now.replace(/\//g, '-').replace(',', '');
        
    // for dateTime format code END

     const capitalizeFirst = (text) => {
    if (!text) return ''
        return text.charAt(0).toUpperCase() + text.slice(1)
    }

  return (
    <>
    <Head title={t('Today Exchange Rate')}/><br/><br/><br/><br/><br/>
    <div className="board-wrapper wow zoomIn">

      <div className="exchange-board" style={{ background: bg }}>

        <h2 className="title">{t('House 40 exchange rate')}</h2>
        <div className="city">{t('Poipet City')}</div>

        <div className="top-info">
          <span className="time-css"><b>{formatted}</b></span>
          <span>"{t('Exchange rates fluctuate')}"</span>
        </div>

        {/* MAIN TWO SECTIONS */}
        <div className="rate-container">

          {/* LEFT SIDE */}
          <div className="left-section">
            <div className="header-row">
              <div>{t('For sale')}</div><div>{t('Buy')}</div>
            </div>


          <div className="rate-row">
                {LeftRecords.map(row => (

                    <div key={row.id}>
                        <div>{t(row.from_currency)} - {t(row.to_currency)}</div>    {/*  for Sale */}
                        <b>{row.normal_rate}</b>
                    </div>

                ))}
          </div>

            <div className="bottom-label">
              <span className="black-color">&nbsp;&nbsp;{t('Rate in hours')}</span>
            </div>

          </div>

          {/* RIGHT SIDE */}
          <div className="right-section">

            <div className="right-title">{t('Thai Baht')}</div>
            <div className="right-note">{t('Please contact in advance')}</div>
            <div className="header-row">
              <div>{t('For sale')}</div><div>{t('Buy')}</div>
            </div>

            <div className="rate-row">
                  {RightRecords.map(row => (
                    
                      <div key={row.id}>
                          <div>{t(row.from_currency)} - {t(row.to_currency)}</div>    
                          <b>{row.standard_rate}</b>
                      </div>  

                    ))}
            </div>

            <div className="bottom-label">
              <span className="time-css"><b>
                                            {capitalizeFirst(
                                            new Date().toLocaleTimeString('en-GB', {
                                                timeZone: 'Asia/Bangkok',
                                                hour: 'numeric',
                                                minute: '2-digit',
                                                hour12: true
                                            }).toUpperCase()
                                            )}
                    </b></span>
            </div>

          </div>

        </div>

        <div className="footer">
          <div><h6>{t('Note: Exchange rates vary')}.</h6></div>
          <div className="tel">TEL: 012-580487, 099-996000</div>
          <div><h6>{t('Working hours')}: 6.30 {t('am')} {t('to')} 5.30 {t('pm')}</h6></div>
        </div>

      </div>
   
    </div>
                                               
    <br/><br/>
      <Link href={route('open.moneyexchange.form')}>
          <div className="text-center">
              <button className="mb-3 btn btn-primary py-3 px-5" type="button">
                  {t('Money Exchange')} ⟶
              </button>
          </div>
      </Link>
    <br/>
    </>
  );
}