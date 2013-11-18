<?php

////////////////////////////////////////////////////
// SMTP - PHP SMTP class
//
// Version 1.02
//
// Define an SMTP class that can be used to connect
// and communicate with any SMTP server. It implements
// all the SMTP functions defined in RFC821 except TURN.
//
// Author: Chris Ryan
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * SMTP is rfc 821 compliant and implements all the rfc 821 SMTP
 * commands except TURN which will always return a not implemented
 * error. SMTP also provides some utility methods for sending mail
 * to an SMTP server.
 * @package PHPMailer
 * @author Chris Ryan
 */
class SMTP {

    var $SMTP_SERVER_PORT = 25;
    var $CRLFReplyLine = "\r\n";
    var $do_debug;       # the level of debug to perform
    var $smtp_conn;      # the socket to the server
    var $error;          # error if any on the last call
    var $helo_rply;      # the reply the server sent to us for HELO

    function SMTP() {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;

        $this->do_debug = 0;
    }

    //                    CONNECTION FUNCTIONS                  *

    function Connect($host, $port = 0, $tval = 30) {
        assert($host =! NULL);
        assert($port =! Null);
        assert($tval =! Null);
        
        assert(is_int($host));
        assert(is_int($port));
        assert(is_int($tval));
        
        # set the error val to null so there is no confusion
        $this->error = null;

        # make sure we are __not__ connected
        if ($this->connected()) {
            # ok we are connected! what should we do?
            # for now we will just give an error saying we
            # are already connected
            $this->error =
                    array("error" => "Already connected to a server");
            return false;
        }

        if (empty($port)) {
            $port = $this->SMTP_SERVER_PORT;
        }

        #connect to the smtp server
        $this->smtp_conn = fsockopen($host, # the host of the server
                $port, # the port to use
                $errno = 0, # error number if any
                $errstr = 0, //error message if any
                $tval);   # give up after ? secs
        # verify we connected properly
        if (empty($this->smtp_conn)) {
            $this->error = array("error" => "Failed to connect to server",
                "errno" => $errno,
                "errstr" => $errstr);
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": $errstr ($errno)" . $this->CRLFReplyLine;
            }
            else
                return false;
        }

        # sometimes the SMTP server takes a little longer to respond
        # so we will give it a longer timeout for the first read
        // Windows still does not have support for this timeout function
        if (substr(PHP_OS, 0, 3) != "WIN")
            socket_set_timeout($this->smtp_conn, $tval, 0);

        # get any announcement stuff
        $announce = $this->get_lines();

        # set the timeout  of any socket functions at 1/10 of a second
        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->smtp_conn, 0, 100000);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $announce;
        }

        return true;
    }

    function Authenticate($username, $password) {
        assert($username =! Null);
        assert($password =! Null);
        
        assert(is_string($username));
        assert(is_string($password));
        
        // Start authentication
        fputs($this->smtp_conn, "AUTH LOGIN" . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($code != 334) {
            $this->error =
                    array("error" => "AUTH not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }

        // Send encoded username
        fputs($this->smtp_conn, base64_encode($username) . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($code != 334) {
            $this->error =
                    array("error" => "Username not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }

        // Send encoded password
        fputs($this->smtp_conn, base64_encode($password) . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($code != 235) {
            $this->error =
                    array("error" => "Password not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function Connected() {
        if (!empty($this->smtp_conn)) {
            $sock_status = socket_get_status($this->smtp_conn);
            if ($sock_status["eof"]) {
                # hmm this is an odd situation... the socket is
                # valid but we aren't connected anymore
                if ($this->do_debug >= 1) {
                    echo "SMTP -> NOTICE:" . $this->CRLFReplyLine .
                    "EOF caught while checking if connected";
                }
                $this->Close();
                return false;
            }
            else
                return true;# everything looks good
        }
        else
            return false;
    }

    function Close() {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if (!empty($this->smtp_conn)) {
            # close the connection and cleanup
            fclose($this->smtp_conn);
            $this->smtp_conn = 0;
        }
    }

    //                        SMTP COMMANDS                       *


    function Data($msg_data) {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Data() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "DATA" . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 354) {
            $this->error =
                    array("error" => "DATA command not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }

        # the server is ready to accept data!
        # according to rfc 821 we should not send more than 1000
        # including the CRLF
        # characters on a single line so we will break the data up
        # into lines by \r and/or \n then if needed we will break
        # each of those into smaller lines to fit within the limit.
        # in addition we will be looking for lines that start with
        # a period '.' and append and additional period '.' to that
        # line. NOTE: this does not count towards are limit.
        # normalize the line breaks so we know the explode works
        $msg_data = str_replace("\r\n", "\n", $msg_data);
        $lines = explode("\n", $msg_data);

        # we need to find a good way to determine is headers are
        # in the msg_data or if it is a straight msg body
        # currently I'm assuming rfc 822 definitions of msg headers
        # and if the first field of the first line (':' sperated)
        # does not contain a space then it _should_ be a header
        # and we can process all lines before a blank "" line as
        # headers.
        $field = substr($lines[0], 0, strpos($lines[0], ":"));
        $in_headers = false;
        if (!empty($field) && !strstr($field, " ")) {
            $in_headers = true;
        }

        $max_line_length = 998; # used below; set here for ease in change

        while (list(, $line) = @each($lines)) {
            $lines_out = null;
            if ($line == "" && $in_headers) {
                $in_headers = false;
            }
            # ok we need to break this line up into several
            # smaller lines
            while (strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line, 0, $max_line_length), " ");

                # Patch to fix DOS attack
                if (!$pos) {
                    $pos = $max_line_length - 1;
                }

                $lines_out[] = substr($line, 0, $pos);
                $line = substr($line, $pos + 1);
                # if we are processing headers we need to
                # add a LWSP-char to the front of the new line
                # rfc 822 on long msg headers
                if ($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;

            # now send the lines to the server
            while (list(, $line_out) = @each($lines_out)) {
                if (strlen($line_out) > 0) {
                    if (substr($line_out, 0, 1) == ".") {
                        $line_out = "." . $line_out;
                    }
                }
                fputs($this->smtp_conn, $line_out . $this->CRLFReplyLine);
            }
        }

        # ok all the message data has been sent so lets get this
        # over with aleady
        fputs($this->smtp_conn, $this->CRLFReplyLine . "." . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "DATA not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function Expand($name) {
        
        assert($name =! Null);
        assert(is_string($name));
        
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Expand() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "EXPN " . $name . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "EXPN not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }

        # parse the reply and place in our array to return to user
        $entries = explode($this->CRLFReplyLine, $rply);
        while (list(, $l) = @each($entries)) {
            $list[] = substr($l, 4);
        }

        return $list;
    }

    function Hello($host = "") {
        assert($host =! Null);
        assert(is_int($host));
        
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Hello() without being connected");
            return false;
        }

        # if a hostname for the HELO wasn't specified determine
        # a suitable one to send
        if (empty($host)) {
            # we need to determine some sort of appopiate default
            # to send to the server
            $host = "localhost";
        }

        // Send extended hello first (RFC 2821)
        if (!$this->SendHello("EHLO", $host)) {
            if (!$this->SendHello("HELO", $host))
                return false;
        }
        else
            return true;
    }

    function SendHello($hello, $host) {
        fputs($this->smtp_conn, $hello . " " . $host . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER: " . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => $hello . " not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }

        $this->helo_rply = $rply;

        return true;
    }

    function Help($keyword = "") {
        $this->error = null; # to avoid confusion

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Help() without being connected");
            return false;
        }

        $extra = "";
        if (!empty($keyword)) {
            $extra = " " . $keyword;
        }

        fputs($this->smtp_conn, "HELP" . $extra . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 211 && $code != 214) {
            $this->error =
                    array("error" => "HELP not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return $rply;
    }

    function Mail($from) {
        assert($from =! Null);
        assert(is_string($from));
        
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Mail() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "MAIL FROM:<" . $from . ">" . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "MAIL not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    /**
     * Sends the command NOOP to the SMTP server
     */
    function Noop() {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Noop() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "NOOP" . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "NOOP not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function Quit($close_on_error = true) {
        $this->error = null; # so there is no confusion

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Quit() without being connected");
            return false;
        }

        # send the quit command to the server
        fputs($this->smtp_conn, "quit" . $this->CRLFReplyLine);

        # get any good-bye messages
        $byemsg = $this->get_lines();

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $byemsg;
        }

        $rval = true;
        $e = null;

        $code = substr($byemsg, 0, 3);
        if ($code != 221) {
            # use e as a tmp var cause Close will overwrite $this->error
            $e = array("error" => "SMTP server rejected quit command",
                "smtp_code" => $code,
                "smtp_rply" => substr($byemsg, 4));
            $rval = false;
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $e["error"] . ": " .
                $byemsg . $this->CRLFReplyLine;
            }
        }

        if (empty($e) || $close_on_error) {
            $this->Close();
        }
        else
            return $rval;
    }

    function Recipient($to) {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Recipient() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "RCPT TO:<" . $to . ">" . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250 && $code != 251) {
            $this->error =
                    array("error" => "RCPT not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function Reset() {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Reset() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "RSET" . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "RSET failed",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function Send($from) {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Send() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "SEND FROM:" . $from . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "SEND not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function SendAndMail($from) {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called SendAndMail() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "SAML FROM:" . $from . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "SAML not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    function SendOrMail($from) {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called SendOrMail() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "SOML FROM:" . $from . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250) {
            $this->error =
                    array("error" => "SOML not accepted from server",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return true;
    }

    /**
     * This is an optional command for SMTP that this class does not
     * support. This method is here to make the RFC821 Definition
     * complete for this class and __may__ be implimented in the future
     */
    function Turn() {
        $this->error = array("error" => "This method, TURN, of the SMTP " .
            "is not implemented");
        if ($this->do_debug >= 1) {
            echo "SMTP -> NOTICE: " . $this->error["error"] . $this->CRLFReplyLine;
        }
        return false;
    }

    function VerifyName($name) {
        $this->error = null; # so no confusion is caused

        if (!$this->connected()) {
            $this->error = array(
                "error" => "Called Verify() without being connected");
            return false;
        }

        fputs($this->smtp_conn, "VRFY " . $name . $this->CRLFReplyLine);

        $rply = $this->get_lines();
        $code = substr($rply, 0, 3);

        if ($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLFReplyLine . $rply;
        }

        if ($code != 250 && $code != 251) {
            $this->error =
                    array("error" => "VRFY failed on name '$name'",
                        "smtp_code" => $code,
                        "smtp_msg" => substr($rply, 4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                ": " . $rply . $this->CRLFReplyLine;
            }
            else
                return false;
        }
        else
            return $rply;
    }

//                       INTERNAL FUNCTIONS                       *

    function get_lines() {
        $data = "";
        while ($str = fgets($this->smtp_conn, 515)) {
            if ($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data was \"$data\"" .
                $this->CRLFReplyLine;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                $this->CRLFReplyLine;
            }
            $data .= $str;
            if ($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->CRLFReplyLine;
            }
            # if the 4th character is a space then we are done reading
            # so just break the loop
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $data;
    }

}

?>