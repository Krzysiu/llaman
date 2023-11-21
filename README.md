
<img align="left" src="https://user-images.githubusercontent.com/2560298/284696687-88ec3ba5-b1ec-409a-80ae-626ccdbb0f60.png">
 **LlaMan** - man wannabe for windows - find and show in console documentation for command or application - no more "was&nbsp;it&nbsp;/?&nbsp;or&nbsp;-h?", no more poor documented Windows commands and functions.<br clear="both"/>



## Examples

`man break` (in this case fetches and formats entry from ss64.com)
![break1](https://github.com/Krzysiu/llaman/assets/2560298/e0d445b3-6e87-4d42-b63f-321ab783d869)

--- 
`man break -l` (use local help only, in this case it executes shell command break /?)
![breaklocal](https://github.com/Krzysiu/llaman/assets/2560298/92c38833-e341-4fbc-90b5-97f299dc085b)

## Purpose

LlaMan serves as something like man pages in Linux. It either tries to get info from command line parameters (like --help or /?) or from ss64.com. The latter gets printed in Windows console with as much formatting as possible - and it's easily configurable!

## How does it work?


For local help pages it simply tries to run command with following parameters: `-h`, `--help`, `/?` and without parameters - both as executable (`exec` method) and shell commands (`shell_exec` method). Then it assumes that the longest output is correct one. If there's page about particular command on ss64.com, it fetches the page, caches it, formats and displays it.

## Requirements

* PHP (tested on 7.4) with cURL module

* WIndows 10 or older with [ANSICon](https://github.com/adoxa/ansicon)

## Installation

1) Get the repository

2) Set path to `man.php` in `man.bat`

3) Move `man.bat` to any directory in your PATH enviroment variable

4) Start using!

## Usage

This instruction assumes script already "installed" (points 2-3 in Installation chapter). If not, just change `man` to `php c:\some_path\man.php`.

**Basic usage:**

`man **command**` where command* is a name of command you want to see help for.

**Additional parameters**

Options:
        -s, --style[=default]
                changes print style

        -l, --local
                prefer local help

        -n, --nocache
                don't cache results

        -p, --purge
                purge cache for particular command

        -b, --browser
                open ss64.com result in browser, if available

        -u, --update
                check for updates

        -v, --verbose
                verbose mode

        -h, --help
                shows help screen

## Styling

### Color styles

[to add later/for now consult `man.php` and `style.*.php` files.

### Mutators

[to describe laterl; for now check out `emulateHeader()` callback in `man.php`]

## Todo

* max cache time

* clean code

* display time of ss64 cache

* allow loading local files, just like manpages

* remove leftover tags

* add non-cURL method of retriving data

* ability to ignore non zero errorcodes

* allow choice of non-longest help

* display raw HTML for ss64

* scroll up/down ability
