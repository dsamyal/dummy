<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Email template</title>
        <link rel="stylesheet" href="css/screen.css">
    </head>

    <body height="100%">

        <table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> 
            <tbody>
                <tr>
                    <td>
                        <p style="text-align: center; padding-bottom:40px;"><img src="http://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" /></p> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="text-align: center; color:#666;"><span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span></p>
                    </td>
                </tr>
                <tr style="text-align: center; color:#666;">
                    <td><span style="font-size: 14px; font-family: AvenirNextCondensed, sans-serif; letter-spacing: 2px;">Please verify your email address by clicking the link below.</span></td>
                </tr>
                <tr style="text-align: center;">
                    <td style="padding-top:50px;">
                        <?php $ssUsrl = url('email-veryfied/' . base64_encode($id)); ?>
                        <p>
                            <span>
                                <a style="font-size: 14px; font-family: AvenirNextCondensed, sans-serif; letter-spacing: 2px; color: #fff; background-color:#666; padding:20px; border-radius:10px; text-decoration: none;" href="{{$ssUsrl }}">{{$ssUsrl}} 
                                </a>
                            </span>
                        </p> 
                    </td>
                </tr>
            </tbody>
        </table>

    </body>
</html>



