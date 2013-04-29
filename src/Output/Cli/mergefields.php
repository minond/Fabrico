<?php

namespace Fabrico\Output\Cli;

Output::mergefield('red',       `tput setaf 1`);
Output::mergefield('green',     `tput setaf 2`);
Output::mergefield('yellow',    `tput setaf 3`);
Output::mergefield('blue',      `tput setaf 4`);
Output::mergefield('purple',    `tput setaf 5`);
Output::mergefield('teal',      `tput setaf 6`);
Output::mergefield('white',     `tput setaf 7`);
Output::mergefield('error',     `tput bold && tput setaf 1`);
Output::mergefield('pass',      `tput bold && tput setaf 2`);
Output::mergefield('warn',      `tput bold && tput setaf 3`);
Output::mergefield('info',      `tput bold && tput setaf 7`);
Output::mergefield('notice',    `tput sgr 0 1 && tput bold`);
Output::mergefield('bold',      `tput bold`);
Output::mergefield('underline', `tput sgr 0 1`);
Output::mergefield('end',       `tput sgr0`);

Output::mergefield('eol', PHP_EOL);
Output::mergefield('backspace', chr(0x08));
Output::mergefield('section', "\n    - " . `tput sgr 0 1 && tput bold`);
Output::mergefield('space', '    ');
Output::mergefield('tab', "\t");
Output::mergefield('nl', "\n");

Output::mergefield('ok', '[' . `tput bold && tput setaf 2` . 'ok' . `tput sgr0` . ']');
Output::mergefield('fail', '[' . `tput bold && tput setaf 1` . 'fail' . `tput sgr0` . ']');

Output::mergefield('time', function() {
    return (string) time();
});

Output::mergefield('rand', function() {
    return (string) rand();
});

Output::mergefield('clear', function() {
    passthru('clear');
    return '';
});
