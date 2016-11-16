<?php
/*
PHPCiscoTelnet 1.0 (http://linbox.free.fr/PHPCiscoTelnet.php)
adapted by Cyriac REMY (05/07/18)
adapted from code PHPTelnet 1.0 by Antone Roundy (http://www.geckotribe.com/php-telnet/)
originally adapted from code found on the PHP website
public domain
*/

/*
        Differences with original code :
                . new GetResponseUntilPrompt function which bufferize all input in
                an array of strings until Cisco prompt is returned (regexpr [>#$])

                . array of strings which contains the last "DoCommand" result

                . no time sleep

                . a dump variable if you want to echo login and enable session

                . an "enable" function with password argument

        I did'nt spend a lot of time to adapt the original code for Cisco Equipement
        so try it at your own risk... ;)
*/

class PHPCiscoTelnet {
        var $fp=NULL;
        var $loginprompt;

        var $buffer = array();
        var $dump = 0;
        var $endPrompt = ">";

        /*
        0 = success
        1 = couldn't open network connection
        2 = unknown host
        3 = login failed
        4 = PHP version too low
        */
        function Connect($server,$user,$pass) {
                $rv=0;
                $vers=explode('.',PHP_VERSION);
                $needvers=array(4,3,0);
                $j=count($vers);
                $k=count($needvers);
                if ($k<$j) $j=$k;
                for ($i=0;$i<$j;$i++) {
                        if (($vers[$i]+0)>$needvers[$i]) break;
                        if (($vers[$i]+0)<$needvers[$i]) return 4;
                }

                $this->Disconnect();

                if (strlen($server)) {
                        if (preg_match('/[^0-9.]/',$server)) {
                                $ip=gethostbyname($server);
                                if ($ip==$server) {
                                        $ip='';
                                        $rv=2;
                                }
                        } else $ip=$server;
                } else $ip='127.0.0.1';

                if (strlen($ip)) {
                        if ($this->fp=fsockopen($ip,23)) {
                                fputs($this->fp,chr(0xFF).chr(0xFB).chr(0x1F).chr(0xFF).chr(0xFB).
                                chr(0x20).chr(0xFF).chr(0xFB).chr(0x18).chr(0xFF).chr(0xFB).
                                chr(0x27).chr(0xFF).chr(0xFD).chr(0x01).chr(0xFF).chr(0xFB).
                                chr(0x03).chr(0xFF).chr(0xFD).chr(0x03).chr(0xFF).chr(0xFC).
                                chr(0x23).chr(0xFF).chr(0xFC).chr(0x24).chr(0xFF).chr(0xFA).
                                chr(0x1F).chr(0x00).chr(0x50).chr(0x00).chr(0x18).chr(0xFF).
                                chr(0xF0).chr(0xFF).chr(0xFA).chr(0x20).chr(0x00).chr(0x33).
                                chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0x2C).chr(0x33).
                                chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0xFF).chr(0xF0).
                                chr(0xFF).chr(0xFA).chr(0x27).chr(0x00).chr(0xFF).chr(0xF0).
                                chr(0xFF).chr(0xFA).chr(0x18).chr(0x00).chr(0x58).chr(0x54).
                                chr(0x45).chr(0x52).chr(0x4D).chr(0xFF).chr(0xF0));

                                fputs($this->fp,chr(0xFF).chr(0xFC).chr(0x01).chr(0xFF).chr(0xFC).
                                chr(0x22).chr(0xFF).chr(0xFE).chr(0x05).chr(0xFF).chr(0xFC).chr(0x21));
                                $this->GetResponse($r);
                                $r=explode("\n",$r);
                                $this->loginprompt=$r[count($r)-1];

                                fputs($this->fp,"$user\r");

                                fputs($this->fp,"$pass\r");
                                $this->GetResponse($r);
                                $r=explode("\n",$r);
                                if (($r[count($r)-1]=='')||($this->loginprompt==$r[count($r)-1])) {
                                        $rv=3;
                                        $this->Disconnect();
                                }
                        } else $rv=1;
                }

                $this->GetResponseUntilPrompt($tmp);
                return $rv;
        }

        function Disconnect($exit=1) {
                if ($this->fp) {
                        if ($exit) fputs($this->fp, "\nexit");
                        fclose($this->fp);
                        $this->fp=NULL;
                }
        }

        function DoCommand($c) {
            if ($this->fp) 
            {
                    fputs($this->fp,"$c\n");
                    $this->GetResponseUntilPrompt($r);
                    $r=preg_replace("~\r~", "", $r);
                    //print $c;
                    $r=preg_replace("~".$c."~", "", $r);
                    //print $r;
                    $tab = explode("\n", $r);
                    $this->buffer = array_slice($tab, 1, count($tab) - 2);
                    return 1;
            }
            return 0;
        }
    function enable($pwd) {
                fputs($this->fp, "enable\n");
        fputs($this->fp, $pwd . "\n");
                $this->endPrompt="#";
                $this->GetResponseUntilPrompt($tmp);
        }

        function GetResponse(&$r) {
                $r='';
                do {
                        $r.=fread($this->fp,1000);
                        $s=socket_get_status($this->fp);
                } while ($s['unread_bytes']) ;
           if ($this->dump)
            print $r."\n";

        }

    function GetResponseUntilPrompt(&$r) {
        $r='';
        do {
            $r.=fread($this->fp,1000);
            $s=socket_get_status($this->fp);
            if (preg_match("/ --More-- /", $r)) {
                $r = preg_replace("/ --More-- /", "MORE", $r);
                fputs($this->fp, " ");
            }
        } while (! preg_match("/".$this->endPrompt."$/", $r));

                $r=preg_replace("/".chr(8)."/", "", $r);
                $r=preg_replace("/MORE        /", "", $r);
                if ($this->dump)
                        print $r."\n";
    }

        function display() {
             foreach ($this->buffer as $line) {
                                 print $line."\n";
            }
        }
}
?>