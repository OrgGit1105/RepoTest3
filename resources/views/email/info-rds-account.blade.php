<?php
?>
<html lang="en">
<body >
<div style="padding: 0 120px; margin-top: 90px">
    <div style="border-top: 5px solid #289FE1; border-bottom: 5px solid #289FE1; padding-top: 20px; padding-bottom: 20px">
        <h3 style='font-weight: 600;'>
            V-face Veho
        </h3>
        <div style="padding-left: 60px">
            <p>
                Your RDS account has been created with the following information:
            </p>
            <ul>
                <li>Server IP Address: {{$ec2_ip_address}}</li>
                <li>Username: {{$name}}</li>
                <li>Password: {{$password}}</li>
            </ul>
            The above account can be used to access databases belonging to the company's RDS (PhpMyAdmin) with the form <a href="{{$phpmyadmin_url}}">{{$phpmyadmin_url}}</a>.
        </div>
        <div style="margin-top: 20px;">
            <ul> <b>Note:</b>
                <li>
                    The username and password will be the same on other RDS (PhpMyAdmin).
                        If you cannot access other RDS (PhpMyAdmin) using the above login information,
                        please contact V-face admin to learn more about your access rights.
                </li>
                <li>This account will be revoked when you leave your job. So please do not use it to configure projects</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
