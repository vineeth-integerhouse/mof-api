<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Email Title</title>
 <style type="text/css">
           body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100%!important;
            height: 100%;
            font-family: sans-serif;
            color: #383D39;
            font-style: normal;
            letter-spacing: -0.02em;
            text-align: left;
        }
        .btn-primary {
            text-decoration: none;
            color: #ffffff !important;
            background-color: #3E4A6B;
            line-height: 2;
            font-weight: bold;
            margin-right: 10px;
            text-align: center;
            cursor: pointer;
            display: inline-block;
            border-radius: 5px;
            padding-top: 10px;
            padding-left: 20px;
            padding-right: 20px;
            padding-bottom: 10px;
            font-size: 16px;
        }
       
        h1, 
        h2, 
        h3 {
            font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            line-height: 1.1;
            margin-bottom: 15px;
            color: #000;
            margin: 40px 0 10px;
            line-height: 1.2;
            font-weight: 200;
        }
        h1 {
            font-size: 36px;
        }
        h2 {
            font-size: 28px;
        }
        h3 {
            font-size: 22px;
        }
        p {
            margin-bottom: 10px;
            font-weight: normal;
            font-size: 14px;
        }

        .container {
            display: block!important;
            max-width: 600px!important;
            /* makes it centered */
        }
        .body-wrap .container {
            padding: 20px;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            display: block;
        }

        .content table {
            width: 100%;
        }

        .logo-img {
            background-image: url("{{ $message->embed(resource_path() . '/email/logo-email.png') }}");
            width: 230px;
            height: 113px;
            margin-left: auto;
            margin-right: auto;
        }

        .button-center {
            margin-left: auto;
            margin-right: auto;
            width: fit-content;
            height: 50px;
        }

        .text-align-center {
            text-align: center; 
        }
    </style>
    <body>
    <div class="container">
        <div class="logo-img"></div>
        <h2 class="text-align-center"> Hello !</h2>
         <h3>
         <h3> Welcome to Music Only Fans!<h3>
<h3>Your account has been created. Please login with below credentials </h3>
         <p> Email  - {{ $details['email'] }}</p>
         <p> Password  - {{ $details['password'] }} </p>
<h3>Sincerley</h3>
<h3> Music Only Fans!</h3>
</div>
 </body>
</body>
</html>