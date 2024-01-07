<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{$company_name}</title>
    <style type="text/css">@media (max-width:440px) { .adrs{ float: left!important; } }</style>
  </head>
  <body style="font-family: 'Open Sans', sans-serif; font-weight: 400; font-size:0.8rem; color: #333;">
    <table class="table table-borderless" style="max-width:768px; margin:20px auto; box-shadow:0px 0px 5px rgba(0,0,0,0.2);width: 100%;box-sizing: border-box;">
  <thead>
    <tr>
      <td style="padding: 5px 25px 10px 25px;">
        <div style="float: left; margin-top:20px"><img src={$site_url}/uploads/images/logos/{$site_logo} style="width: auto; height: auto;max-width: 150px; max-height: 60px; " /></div>
        <div class="adrs" style="float: right; margin-top:20px;">
            <h4 title="company Name" style="font-size: 1.1em;box-sizing: border-box;font-weight:600;   font-size: 1.1em; line-height: 1.2;margin-bottom: .5rem;    margin-top: 0;">{$company_name}</h4>
            <p style="max-width: 230px;box-sizing: border-box; margin-top: 0;
    margin-bottom: 1rem;   line-height: 1.5;">{$company_address}</p>
        </div>
    </td>
    </tr>
  </thead>
  <tbody>
    <tr>     
      <td>
             <div style="max-width:600px;width: 100%;box-sizing: border-box; margin:0px auto 30px auto;">
                {$mail_content|default:''}
          </div>
    </td>
    </tr>
  </tbody>
  <tfoot style="background: #4f5c75; color: #fff">
      <tr>     
      <td style="padding: .75rem;"><p style="padding:1px 20px; text-align: center; margin: 0px auto; color: #d7e4ff;    line-height: 1.5;">Please do not reply to this message. If you have any questions, please contact us via </br>email at <a style="color: #fff;    line-height: 1.5;" href="{$company_email}">{$company_email}</a> or call us at <span style="color: #fff;    line-height: 1.5;">{$company_phone}</span></p></td>
    </tr>
  </tfoot>
</table>

  </body>
</html>