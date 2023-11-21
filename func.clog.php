<?php
    // defines for pretty logging/output function
    define('CL_CRIT', 0); 
    define('CL_INFO', 1);
    define('CL_WARN', 2);
    define('CL_DBUG', 3);
    define('CL_OKAY', 4);
    $dbg = false; // verbose mode; not used in this tool
    
/**
 * Console logging function with Win 10 (and older with ansicon extenstion) ANSI color support: clog
 * 
 * @param mixed $msg     The message to be logged. If it's an array, it is treated as arguments for sprintf.
 * @param int   $status  The status of the log message (0-4). CL_CRIT additionally interrupts the program.
 * @param bool  $stdlog  Whether to trigger a PHP error based on the log status.
 *
 * @return mixed The logged message.
 */
function clog($msg, $status = 1, $stdlog = false) {
        global $dbg;
        if ($status === 3 && !$dbg) return;
        $colors = [ // array of statuses - BG, FG (if 0 then use bg as fg color), message
        0 => [41, 0, 'CRIT'], 
        1 => [46, 0, 'INFO'], 
        2 => [43, 0, 'WARN'],
        3 => [100, 0,'DBUG'],
        4 => [42, 0, 'OKAY']
        ];
        $codes = [CL_CRIT => E_USER_ERROR, CL_WARN => E_USER_WARNING, CL_DBUG => E_USER_NOTICE]; // translation table for stdlog 
        
        if ($colors[$status][1] === 0) $colors[$status][1] = $colors[$status][0] - 10; // bg color to fg
        $esc = chr(27);
        $end = $esc . '[0m';
        if (is_array($msg)) $msg = call_user_func_array('sprintf', $msg);
        echo "{$esc}[{$colors[$status][0]}m[{$colors[$status][2]}]{$end} {$esc}[{$colors[$status][1]}m{$msg}{$end}" . PHP_EOL;
        if ($stdlog && array_key_exists($status, $codes)) trigger_error($msg, $codes[$status]);
        if ($status === 0) die(1);
        return $msg;
    } 