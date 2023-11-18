<?php
    $style['print'] = [
    'italics' => S_UNDERLINE , // <i>
    'href' => S_UNDERLINE, // links with href (i.e. except <a id="foo">)
    'b' => S_UNDERLINE, // seldom used in code blocks
    'listBullet' => '  • ' // ok, here's only S_END in styling
    ]; // output style for ss64 (and eventually for other)