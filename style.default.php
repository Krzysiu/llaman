<?php
    $style['default'] = [
    'header' => S_BG_GREEN . S_FG_BRIGHT_WHITE, // headers (h3)
    'italics' => S_FG_YELLOW , // <i>
    'href' => S_FG_CYAN . S_UNDERLINE, // links with href (i.e. except <a id="foo">)
    'code' => S_BG_WHITE . S_FG_RED, // code blocks
    'blockquote' => S_FG_BRIGHT_WHITE, // seldom used, I'm not sure what's the purpose
    'em' => S_BG_YELLOW . S_FG_BLACK,
    'b' => S_BOLD, // seldom used in code blocks
    'listBullet' => S_FG_RED . '  • ' . S_END // ok, here's only S_END in styling
    ]; // output style for ss64 (and eventually for other)