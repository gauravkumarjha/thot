const mix = require('laravel-mix');
const partytown = require('@builder.io/partytown/utils');

mix.copy(partytown.libDirPath(), 'public/~partytown');
