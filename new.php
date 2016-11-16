<?php

        include ("PHPTelnet.php");
        $server = '192.168.7.229';
        $user ='admin';
        $pass = 'ATI#123';

        $telnet = new PHPCiscoTelnet();
        $result = $telnet->Connect($server, $user, $pass);
        switch ($result) {
        case 0:
                

                $telnet->enable('ATI#123');
                
                $telnet->DoCommand("configure terminal");
                $telnet->DoCommand("int FastEthernet0/4");
                $telnet->DoCommand("no shut");
                $telnet->Disconnect();
                
                print "SHOW status\n-----------------------------\n";
                $telnet->display();
				
				
				
                print "shutdown\n-----------------------------\n";
                $telnet->display();
					
				
        break;
        case 1:
                echo '[PHP Telnet] Connect failed: Unable to open network connection';
        break;
        case 2:
                echo '[PHP Telnet] Connect failed: Unknown host';
        break;
        case 3:
                echo '[PHP Telnet] Connect failed: Login failed';
        break;
        case 4:
                echo '[PHP Telnet] Connect failed: Your PHP version does not support PHP Telnet';
        break;
}
?>