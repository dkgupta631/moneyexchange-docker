import { useState } from 'react';
import { usePage } from '@inertiajs/react';
export default function ShowInvoices({records}) {

    console.log(usePage());
    const { flash } = usePage().props;
    const [flashMsg, setflashMsg] = useState(flash.greet);
    setTimeout(() => {
        setflashMsg(null)
    }, 20000);

  return (
    <>
        <br /><br /><br />
        <div className="container-fluid py-5">
            <div className="container px-lg-5">
                <div className="row justify-content-center">
                    <div className="col-lg-7">
                        <div className="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                            <h6 className="position-relative d-inline text-primary ps-4">Contact Us</h6>
                            <h2 className="mt-2">{flashMsg && <div style={{ color: 'green' }}>{flashMsg}</div>}</h2>
                        </div>
                        <div className="wow fadeInUp" data-wow-delay="0.3s">
                            <h4 className="text-center mb-4">Receive messages instantly with our PHP and Ajax contact form - available in the <a href="">Pro Version</a> only.</h4>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

      <h1>This is Show Invoices page</h1>
       <div>
      {records.invoice_number}
        {/* {records.map(row => (

            <div key={row.id}>
              <p>{row.from_currency} - {row.to_currency}</p>
              <p></p>
            </div>

        ))} */}
      </div>
      
    </>
      
   
  );
}