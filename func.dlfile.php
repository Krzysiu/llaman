<?php
    function dlFile($url, &$code) {
        global $config;
        if (function_exists('curl_version')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // security risk, but...
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // needed for older PHP compatibility
            curl_setopt($ch, CURLOPT_USERAGENT, $config['ua']); // user agent. Try keeping it as it is, so devs can manage/block this tool
            
            $content = curl_exec($ch);
            
            if (curl_errno($ch) && $v) clog(['cURL error: %s', curl_error($ch)], 2);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get status code
            curl_close($ch);
            
            } else {
            $content = @file_get_contents($url);
            foreach ($http_response_header as $h) if(preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m)) $code = (int)$m[1];

        }
        return $content;
    }
