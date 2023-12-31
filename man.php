<?php
    chdir(dirname(__FILE__));
    require 'config.php';
    require 'vendor/autoload.php';
    require 'func.clog.php';
    require 'func.dlfile.php';
    
    use GetOptionKit\OptionCollection;
    use GetOptionKit\OptionParser;
    use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
    
    $specs = new OptionCollection;
    $specs->add('s|style?', 'changes print style')->isa('String')->defaultValue('default');
    $specs->add('S|list-styles', 'list available styles');    
    $specs->add('l|local', 'prefer local help');
    $specs->add('n|nocache', 'don\'t cache results');
    $specs->add('p|purge', 'purge cache for particular command');
    $specs->add('b|browser', 'open ss64.com result in browser, if available');
    $specs->add('u|update', 'check for updates');
    $specs->add('v|verbose', 'verbose mode');
    $specs->add('h|help', 'shows this screen');
    
    $version = file_get_contents('version');
    
    
    
    $parser = new OptionParser($specs);
    
    try {
        $result = $parser->parse($argv);
        $args = $result->getArguments();
        $str = $result->keys['style']->value; // return the option value  
        
        } catch( Exception $e ) {
        echo $e->getMessage();
        
    }
    $v = $result->has('verbose');
    
    function verStringToInt($ver) {
        $ver = explode('.',$ver);
        $ver = array_reverse($ver);
        array_walk($ver, function (&$seg, $key) { $seg = pow(10, $key) * $seg;});
        return array_sum($ver);
    }
    if ($result->has('update')) {
        $ghVer = @file_get_contents('https://raw.githubusercontent.com/Krzysiu/llaman/main/version');
        if (!$ghVer) { clog('Can\'t access GihHub repository. Update check failed.', CL_WARN); die(1); }
        
        if (verStringToInt($ghVer) > verStringToInt($version)) clog(['Your version (%s) has update available (%s). Check https://github.com/Krzysiu/llaman', $version, $ghVer], CL_OKAY);
        else clog(['No update available for your version (%s).', $version, $ghVer], CL_INFO);
        
        die(0);
    }
    
    
    // defines for styling system
    // See https://ss64.com/nt/syntax-ansi.html for preview and info 
    // If you have older Windows, try https://github.com/adoxa/ansicon
    
    
    // 1) foreground 
    define('S_FG_BLACK', "\033[30m");
    define('S_FG_RED', "\033[31m");
    define('S_FG_GREEN', "\033[32m");
    define('S_FG_YELLOW', "\033[33m");
    define('S_FG_BLUE', "\033[34m");
    define('S_FG_MAGENTA', "\033[35m");
    define('S_FG_CYAN', "\033[36m");
    define('S_FG_WHITE', "\033[37m");
    define('S_FG_BRIGHT_BLACK', "\033[90m");
    define('S_FG_BRIGHT_RED', "\033[91m");
    define('S_FG_BRIGHT_GREEN', "\033[92m");
    define('S_FG_BRIGHT_YELLOW', "\033[93m");
    define('S_FG_BRIGHT_BLUE', "\033[94m");
    define('S_FG_BRIGHT_MAGENTA', "\033[95m");
    define('S_FG_BRIGHT_CYAN', "\033[96m");
    define('S_FG_BRIGHT_WHITE', "\033[97m");
    
    // 2) background 
    define('S_BG_BLACK', "\033[40m");
    define('S_BG_RED', "\033[41m");
    define('S_BG_GREEN', "\033[42m");
    define('S_BG_YELLOW', "\033[43m");
    define('S_BG_BLUE', "\033[44m");
    define('S_BG_MAGENTA', "\033[45m");
    define('S_BG_CYAN', "\033[46m");
    define('S_BG_WHITE', "\033[47m");    
    define('S_BG_BRIGHT_BLACK', "\033[100m");
    define('S_BG_BRIGHT_RED', "\033[101m");
    define('S_BG_BRIGHT_GREEN', "\033[102m");
    define('S_BG_BRIGHT_YELLOW', "\033[103m");
    define('S_BG_BRIGHT_BLUE', "\033[104m");
    define('S_BG_BRIGHT_MAGENTA', "\033[105m");
    define('S_BG_BRIGHT_CYAN', "\033[106m");
    define('S_BG_BRIGHT_WHITE', "\033[107m");
    
    // 3) special styles
    
    // switches fg with bg. Works one time, so using two S_REVERSE won't get to 
    // the starting point. You have to use alternating S_REVERSE and S_UNREVERSE
    define('S_REVERSE', "\033[7m"); 
    define('S_UNREVERSE', "\033[27m"); // switches fg with bg (works one time)
    define('S_UNDERLINE', "\033[4m");
    define('S_NOUNDERLINE', "\033[24m");
    
    // for some reason it won't make font bold, it just "boosts" color from 
    // normal version to bright
    define('S_BOLD', "\033[1m"); 
    define('S_END', "\033[0m");
    
    if ($result->has('help')) showHelp();
    /*
        Styles
        
        to edit it, just concat (dot operator) above constants (like S_FOO . S_BAR).
        The order shouldn't matter. 
        
        You can change fg, bg colors and add underline. For bold see note above 
        S_BOLD definition.
        
        S_END is used internally, so probably you won't have to bother with that, 
        but if you would need it, it just removes all previous styles.
    */
    $style = [];
    // style template 
    $style['none'] = [
    'header' => '', // headers (h3)
    'italics' => '' , // <i>
    'href' => '', // links with href (i.e. except <a id="foo">)
    'code' => '', // code blocks
    'blockquote' => '', // seldom used, I'm not sure what's the purpose
    'em' => '',
    'b' => '', // seldom used in code blocks
    'listBullet' => '' // ok, here's only S_END in styling
    ]; // output style for ss64 (and eventually for other)
    foreach (glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'style.*') as $file) include_once($file);
    foreach ($style as $name => &$s) { $s = array_merge($style['none'], $s); } // merge with empty array to avoid notices
    $styleName = strtolower($result->keys['style']->value);
    
    $style = array_combine(array_map('strtolower', array_keys($style)), array_values($style)); // lowercase style names
    
    if ($result->has('list-styles')) { // list styles
        clog(['Available styles: %s', implode(', ', array_keys($style))]); 
        die(0);
        }
        
    if (!array_key_exists($styleName, $style)) clog(['Style "%s" not found. Available styles: %s', $styleName, implode(', ', array_keys($style))], 0);
    $curStyle = $style[$styleName];
    
    @$item = $args[0];
    if (!$item) showHelp();
    
    $cacheDir = $config['cacheDir'] === null ? getenv('LOCALAPPDATA') . DIRECTORY_SEPARATOR . 'llaMan' : $config['cache'];
    if (!(file_exists($cacheDir) && is_dir($cacheDir))) {
        mkdir($cacheDir); // create cache director if it's not there yet
        if ($v) clog(['Creating cache directory: %s', $cacheDir]);
    } else if ($v) clog(['Using cache directory: %s', $cacheDir]);
    
    
    // command parameters to check - all are checked, even if there's some 
    // output. /? is Windows style, -h and --help is for *nix tools, empty 
    // string for some weird commands that prints help when no params are given
    
    $helpParameters = ['/?', '-h', '--help', '']; 
    $ss = "https://ss64.com/nt/%s.html";
    
    
    $help = ['cmd' => []]; // init main array
    foreach ($helpParameters as $param) {
        // log all outputs, including stderr, so failed commands won't appear on the
        // screen (like "curl /?")
        $cmd = $item . ' '. $param . ' 2>&1';
        exec($cmd, $out, $errlevel); 
        
        $out = implode(PHP_EOL, $out);
        if ($errlevel === 0) { // let's hope help page won't trigger non-zero exit code
            if ($v) clog(['Pontential help page available from command line parameter: %s (%d characters)', $param, iconv_strlen($out)]);
            $help['cmd'][] = $out;
        }
        
        $shellRes = shell_exec($cmd);
        
        if (trim($shellRes) != '') {
            
            if ($v) clog(['Pontential help page available from shell parameter: %s (%d characters)', $param, iconv_strlen($out)]);
            $help['cmd'][] = $shellRes;
        }
        
    }
    if (count($help['cmd']) < 1) unset($help['cmd']);
    else {
        $help['cmd'] = array_unique($help['cmd']); // remove duplicate entries
        $help['cmd'] = array_filter($help['cmd']); // remove empty entries
        
        usort($help['cmd'],function($a, $b) {
            if($a == $b) return 0;
            return (strlen($a) > strlen($b) ? -1 : 1);
            
        }); // sort help pages from shell by lenght - we assume the longest is the best
    }
    
    // ss64
    
    if ($result->has('purge')) @unlink(getCacheFileName($item, 'ss64'));
    $url = sprintf($ss, $item);
    if (!$result->has('local')) {
        
        if (checkCache($item, 'ss64') && !$result->has('nocache')) {
            $help['ext'] = file_get_contents(getCacheFileName($item, 'ss64'));
            
            if (strpos($help['ext'], 'HTTP ') !== 0) {
                
                if ($v) clog(['Pontential help page available from cache: %s' . PHP_EOL, getCacheFileName($item, 'ss64')]);
                if ($result->has('browser')) {
                    if ($v) clog(['Opening ss64.com page (%s) in default browser' . PHP_EOL, $url]);
                    shell_exec('start ' . $url);
                    die(0);
                }
                } else {
                if ($v) clog(['ss64.com cached page exists, but it returned error (%s). Consider checking manually (%s) or clear the cache (-p flag)', $help['ext'], $url], 2);
                if ($result->has('browser')) die(1);
                unset($help['ext']);
            }

            } else {

            $httpCode = 0;
            $dlData = dlFile($url, $httpCode);
            
            // Output the downloaded content
            var_dump($dlData);
            if ($httpCode === 200) { // proceed if HTTP 200 (ok)
                if ($result->has('browser')) {
                    if ($v) clog(['Opening ss64.com page (%s) in default browser' . PHP_EOL, $url]);
                    shell_exec('start ' . $url);
                    die(0);
                }
                
                $help['ext'] = $dlData;
                if ($v) clog(['Pontential help page available from online source: %s (%d characters)' . PHP_EOL, $item, iconv_strlen($dlData)]);
                
                if (!$result->has('nocache')) file_put_contents(getCacheFileName($item, 'ss64'), $help['ext']);
                } else {
                if ($v) clog(['ss64 error: HTTP %d', $httpCode], 2);
                if ($v && $result->has('browser')) { clog(['ss64.com resource unavailable (HTTP %d)' . PHP_EOL, $httpCode], 2); die(1); }
                if (!$result->has('nocache')) file_put_contents(getCacheFileName($item, 'ss64'), 'HTTP ' . $httpCode); // cache non HTTP 200 pages as well
            }
            
            
        }
        
    }
    if (array_key_exists('ext', $help)) {
        $help['ext'] = substr($help['ext'], strpos($help['ext'], '</h1>') + 5); // trim header
        $help['ext'] = trim(substr($help['ext'], 0, strpos($help['ext'], '<!-- #BeginLibraryItem "/Library/foot_nt.lbi" -->'))); // trim footer
        $help['ext'] = convertHTML($help['ext']); // magic happens here
        echo $help['ext'] . PHP_EOL . PHP_EOL . 'Source: ' . $url;
    } else if (array_key_exists('cmd', $help)) echo $help['cmd'][0]; else if ($v) clog(['Can\'t find help for %s', $item], 2);
    echo S_END . S_NOUNDERLINE; // just in case of wrong style
    function convertHTML($string) {
        global $curStyle;
        $remove = [
        '<br>',
        '<pre>',
        '</pre>',
        '<ul>',
        '</ul>',
        '</li>'
        ];
        $translation = // tags to replace
        [
        '<i>' => $curStyle['italics'] ,
        '</i>' => S_END,        
        '<em>' => $curStyle['em'] , 
        '</em>' => S_END,        
        '<span class="code">' => $curStyle['code'] ,
        '</span>' => S_END,
        '<li>' => $curStyle['listBullet']
        ];
        
        
        // format and sanitize ss64.com HTML
        // it's done also for cached files, so style changes won't have to be redownloaded
        $out = str_replace($remove, '', $string); // remove tags        
        $out = str_replace(array_keys($translation), array_values($translation), $out); // simple tag replacement
        $out = HTMLTagReplace('span', '','', $out); // remove rest of <span>
        $out = HTMLTagReplace('p', '',PHP_EOL, $out); // remove rest of <p> (not in removal routine because "<p class=...>"
        $out = preg_replace('/<a id=.+?><\/a>/', '', $out); // remove "positional" link (no href, just id)
        $out = HTMLTagReplace('a',  $curStyle['href'], S_END, $out); // set style for links
        $out = HTMLTagReplace('b',  $curStyle['b'], '', $out); // seldom used in code blocks. No S_END, as it would unstyle many code blocks
        $out = HTMLTagReplace('blockquote',  $curStyle['blockquote'], S_END, $out);
        $out = HTMLTagReplace('h3', PHP_EOL . PHP_EOL, PHP_EOL, $out, 'emulateHeader'); // set style for h3
        $out = HTMLTagReplace('h2', PHP_EOL . PHP_EOL, PHP_EOL, $out, 'emulateHeader2'); // set style for h2
        $out = html_entity_decode($out); // convert HTML entites
        $out = trimLines($out); // rtrim every line
        
        return $out;
    }
    
    
    function checkCache($entry, $suffix) {
        /*
            Check if cache file is non-zero and cached
        */
        $file = getCacheFileName($entry, $suffix);
        return (file_exists($file) && filesize($file));
    }
    
    function getCacheFileName($entry, $suffix) {
        /*
            Get path of cache file for given $entry and $suffix (for now only ss64)
        */
        global $cacheDir;
        return $cacheDir . DIRECTORY_SEPARATOR . $entry . '.' . $suffix;        
    }
    
    function trimLines($string) {
        /*
            Right trim lines. Shouldn't be THAT needed, but it's light anyways, 
            so just in case
        */
        $string = explode("\n", str_replace("\r\n", "\n", $string)); // ensure Linux style EOL
        $string = array_map('rtrim', $string);
        return implode(PHP_EOL, $string);
    }
    
    function HTMLTagReplace($tag, $start, $end, $subject, $cb = '') {
        /*
            Replace HTML tag with name $tag (without "<" and ">") with $start for 
            the opening tag and $end for closing in $subject.
            Optionally use callback $cb for content between tags
        */ 
        return preg_replace_callback('/<' . $tag . '.*?>(.*?)<\/' . $tag . '>/s', function ($m) use ($start, $end, $cb) {
            
            if (isset($m[1])) {
                
                if ($cb !== '') {return call_user_func($cb, $m[1], $start, $end);}
                return "{$start}{$m[1]}{$end}";
            }}, $subject);
            
    }
    
    function emulateHeader($str, $start, $end) {
        /* 
            Special function/callback for making headers more "headery". 
            It adds ..[ and ].., capitalizes characters and puts space between 
            them.
        */
        global $curStyle;
        $str = str_split($str);
        $str = implode(' ', $str);
        return $start . $curStyle['header'] . '..[ '   . strtoupper($str)  . ' ]..'. S_END . $end;
    }
    
    function emulateHeader2($str, $start, $end) {
        /* 
            Same as emulateHeader, but for seldom used h2
        */
        global $curStyle;
        $str = str_split($str);
        $str = implode(' ', $str);
        return $start . $curStyle['header'] . '.[ '   . strtoupper($str)  . ' ].'. S_END . $end;
    }                        
    
    function showHelp() {
        global $specs, $version;
        
        echo S_BG_BRIGHT_BLUE . S_FG_WHITE . "Llaman v{$version} - colorful console online and local documentation parser"  . S_END . PHP_EOL;
        echo S_UNDERLINE . S_FG_BRIGHT_BLUE . "https://github.com/Krzysiu/llaman" . S_END . PHP_EOL . PHP_EOL ;
        echo "Usage:" . PHP_EOL . 'php ' .  __FILE__ . ' [options] command'. PHP_EOL .  PHP_EOL . 'Options:' . PHP_EOL; 
        
        $parser = new OptionParser($specs);
        $printer = new ConsoleOptionPrinter();
        echo $printer->render($specs);
        exit(0);
        return;
    }                                        