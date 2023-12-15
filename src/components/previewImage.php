<?php

use Kirby\Cms\App as Kirby;
use Kirby\Cms\File;

return function (Kirby $kirby, File $file, array $options = [])
{
  static $original;

  if ($file->template() === 'virtual-file') {
    return $file;
  }

  // if static $original is null, get the original component
  if ($original === null) {
    $original = $kirby->nativeComponent('file::version');
  }

  // and return it with the given options
  return $original($kirby, $file, $options);
};