<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700;800;900&display=swap");

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Barlow", sans-serif;
            color: #000000;
            font-weight: 400;
        }

        body {
            font-family: "Barlow", sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: rgba(0, 0, 0, 0.4);
        }

        span {
            font-size: 12px;
            color: #000000;
        }

        a {
            text-decoration: none;
        }
    </style>
    <title>Reset Password</title>
</head>

<body>
    <center style="width: 100%">
        <div style="display: none; font-size: 1px;  max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;  font-family: sans-serif; "></div>
        <div style="max-width: 600px; margin: 0 auto; background-color: #eaf0f3">
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto">
                <tbody>
                    <tr>
                        <td valign="top" class="bg_white" style="padding: 2.5em">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody> 
                                    <tr style=" display: flex; justify-content: center; align-items: center;">
                                        <td style="margin-top: 35px; text-align: center">
                                            <h1 style="text-align: center; font-size: 20px">
                                                Hi {{$user->name}}, Berikut Kode untuk melanjutkan Reset Password
                                            </h1>
                                        </td>
                                    </tr>
                                    <tr style="display: flex; justify-content: center; align-items: center;">
                                        <td style="margin-top: 35px; margin-bottom: 30px; background-color: #fff; border-radius: 6px;">
                                            <div style="border-top-left-radius: 6px; border-top-right-radius: 6px; border: 0;">
                                                <div style="height: 10px; border-top-left-radius: 6px; border-top-right-radius: 6px;  background: linear-gradient(90deg, #5b7cfd 24.03%, #ff8057 89.2%);"></div>
                                                <div style="padding: 0 1.5em">
                                                    <div style="background-color: #eaf0f3; padding: 5px; font-size: 30px;  color: #000000;  display: flex; justify-content: center; align-items: center; margin: auto; max-width: 100px; margin-top: 2.5rem; border: 0;">
                                                        {{$user->two_factor_code}}
                                                    </div>
                                                    <h6 style="margin: auto; margin-top: 1.25rem">
                                                        Kode akan kadaluarsa dalam 60 menit. Jika Kamu
                                                        tidak merasa memerlukan kode verifikasi abaikan
                                                        pesan ini
                                                    </h6>
                                                    <h5 style="margin: auto;  margin-top: 2.5rem; text-align: center;">
                                                        Aplikasi Fakturco.co.id
                                                    </h5>
                                                    <div style="margin: auto; margin-bottom: 2.5rem; margin-top: 0.5rem; text-align: center; "> </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    
                                    <tr>
                                        <td style="text-align: center">
                                            <span style="font-size: 12px; color: #000000">Copyright &copy; {{date('Y')}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; margin-top: 0.5rem">
                                            <span style="font-size: 12px; color: #000000"><strong>Copyright © POSHUB ACCOUNTING <?=date('Y');?></strong></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </center>
</body>

</html>