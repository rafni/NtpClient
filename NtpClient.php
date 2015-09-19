<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Rafni <alberto.rcdb@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Client to query NTP server from PHP
 *
 * @author Rafni <alberto.rcdb@gmail.com>
 * @category ntp client, real time
 */
class NtpClient {
    
    /**
     * Make a new query to the NTP server
     * @param string $timeserver
     * @param integer $port
     * @param integer $timeout
     * @return object
     */
    public static function query($timeserver, $port, $timeout = 2) {
        $err = null; $errstr = null;
        $fp = @fsockopen($timeserver, $port, $err, $errstr, $timeout);
        if ($fp) {
            fputs($fp, "\n");
            $timeread = fread($fp, 49);
            $timevalue = bin2hex($timeread);
            $timevalue = abs(HexDec('7fffffff') - HexDec($timevalue) - HexDec('7fffffff'));
            $timestamp = $timevalue - 2208988800; # convert to UNIX epoch timestamp
            fclose($fp);
            $result = (object)array(
                'timezone' => date_default_timezone_get(),
                'timestamp' => $timestamp,
                'datetime' => date('Y-m-d H:i:s', $timestamp)
            );
        } else {
            $result = null;
        }
        return (object)array(
            'result' => $result,
            'error-code' => $err,
            'error-text' => $errstr
        );
    }
    
}