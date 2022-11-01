<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;600&display=swap" rel="stylesheet">
    <style>
              /* Resets */
         @font-face {
         font-family: 'prozak-bold';
         src: url('https://dev.artfora.net/Email/fonts/prozak-bold-webfont.woff2') format('woff2'),
         url('https://dev.artfora.net/Email/fonts/prozak-bold-webfont.woff') format('woff'),
         url('https://dev.artfora.net/Email/fonts/prozak-bold.ttf') format('ttf');
         font-weight: normal;
         font-style: normal;
         }
         @font-face {
         font-family: 'prozak-regular';
         src: url('https://dev.artfora.net/Email/fonts/prozak-regular-webfont.woff2') format('woff2'),
         url('https://dev.artfora.net/Email/fonts/prozak-regular-webfont.woff') format('woff'),
         url('https://dev.artfora.net/Email/fonts/prozak-regular.ttf') format('ttf');
         font-weight: normal;
         font-style: normal;
         }
      body {
        background-color: #393939;
      }
      @media only screen and (max-width: 620px) {
        table {
          width: 100%;
        }
      }

      p {
        margin-block-start: 1em;
        margin-block-end: 1em;
        min-height: 1px;
        color: rgb(255, 255, 255);
      }
    </style>
  </head>

  <body>
    <table
      width="500"
      cellpadding="0"
      cellspacing="0"
      align="center"
      bgcolor="#393939"
      style="
        font: 16px/1.4 'Jost', Arial, sans-serif;
        color: rgb(255, 255, 255);
        padding: 25px;
      "
    >

    @foreach ($data['shop_files_data'] as $file)
      <tr>
        <td align="center">
          <img width="100%" src="https://artfora.net/images/post/new_images/thumb/{{ $file['thumb'] }}" alt="" />
        </td>
      </tr>
    @endforeach
      
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td colspan="2" height="20"></td>
            </tr>
            <tr>
              <td width="100">
                <img
                  src="{{$data['user_image']}}"
                  width="74"
                  alt=""
                  style="border-radius: 50%; border: 2px solid #fff"
                />
              </td>
              <td align="left">
                <h3 style="margin-bottom: 5px; margin-top: 0">{{$data['user_name']}}</h3>
                <p style="opacity: 0.5; margin: 0">{{$data['user_tagname']}}</p>
              </td>
            </tr>
            <tr>
              <td colspan="2" height="20"></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td height="40">Posted:</td>
              <td align="right" style="opacity: 0.5">{{$data['datetime_submitted']}}</td>
            </tr>
            <tr>
              <td height="40">Product name:</td>
              <td align="right" style="opacity: 0.5">{{$data['shop_product']['name']}}</td>
            </tr>
            <tr>
              <td height="40">Price:</td>
              <td align="right" style="opacity: 0.5">{{$data['shop_product']['currency']}} {{$data['shop_product']['price']}}</td>
            </tr>
            <tr>
              <td height="40">Shipping:</td>
              <td align="right" style="opacity: 0.5">
                {{$data['shop_product']['shipping_included']}}
              </td>
            </tr>
            <tr>
              <td colspan="2" height="30" valign="bottom">Description:</td>
            </tr>
            <tr>
              <td colspan="2" height="30" valign="top"  align="left" style="opacity: 0.5">
                {{$data['shop_product']['description']}}
              </td>
            </tr>

            <tr>
              <td height="40">W/H/D:</td>
              <td align="right" style="opacity: 0.5">{{$data['shop_product']['package_size']}}</td>
            </tr>
            <tr>
              <td height="40">Weight:</td>
              <td align="right" style="opacity: 0.5">{{$data['shop_product']['package_weight']}} kg</td>
            </tr>
            <tr>
              <td height="40">Quantity for sale:</td>
              <td align="right" style="opacity: 0.5">{{$data['shop_product']['quantity']}}</td>
            </tr>
            <tr>
              <td colspan="2" height="30" valign="bottom">Categories:</td>
            </tr>
            <tr>
              <td colspan="2" align="left" valign="top" height="30" style="opacity: 0.5">
                {{$data['shop_product']['type']}}
              </td>
            </tr>
            <tr>
              <td height="30" colspan="2" valign="bottom">Tags:</td>
            </tr>
            <tr>
              <td colspan="2" align="left" valign="top" style="color: #94babb">
                <span>{{str_replace(',', ' ', $data['shop_product']['tags'])}}</span>
              </td>
            </tr>
            <tr>
              <td height="30" colspan="2" valign="bottom">Filters:</td>
            </tr>
            <tr>
              <td colspan="2" align="left" valign="top" style="color: #94babb">
                <span> {{$data['filter_text']}}</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
<tr>
    <td height="40"></td>
</tr>

      <tr>
       <td>
         <table width="100%">
            <tr>
          <td style="font-size: 22px;font-family: 'prozak-bold',sans-serif;text-align:center;color: #494949;font-weight:normal;background-color:#E5E4E9;border-radius: 8px; box-sizing: border-box;padding: 5px 15px 15px 12px;text-transform: uppercase;letter-spacing: 2px;box-shadow: inset 0px -3px 4px rgba(0,0,0,0.4); text-decoration:none; line-height: normal;">
            APPROVE

        </td>
      </tr>
         </table>
       </td>
      </tr>
    </table>
  </body>
</html>
