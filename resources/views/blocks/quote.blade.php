{!! collect(preg_split('/\r\n|\r|\n/', $content))->map(fn($line) => "> $line")->join(PHP_EOL) !!}
