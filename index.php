<?php
include_once('krumo/class.krumo.php');
include_once('config.php');

/**
 * Convert drupal_debug.txt into an array of array strings.
 *
 * Allows us to view multiple dd() outputs at once.
 */
function find_arrays($string) {
  $strings = array();
  $in_group = FALSE;
  $current = -1;
  $lines = explode("\n", trim($string));
  foreach ($lines as $line) {
    if (trim($line) == 'Array' || trim($line) == 'stdClass Object') {
      $in_group = TRUE;
      $current += 1;
      $strings[$current] = array('lines' => array($line));
    }
    elseif ($in_group) {
      $strings[$current]['lines'][] = $line;
    }
    else {
      $in_group = FALSE;
    }
  }
  $out = array();
  foreach ($strings as $key => $values) {
    $out[] = implode("\n", $values['lines']);
  }
  return $out;
}

/**
 * Convert print_r() strings back to arrays.
 */
function print_r_reverse($in) {
  $lines = explode("\n", trim($in));
  if (trim($lines[0]) != 'Array' && trim($lines[0]) != 'stdClass Object') {
    // bottomed out to something that isn't an array
    return $in;
  } else {
    // this is an array, lets parse it
    if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
      // this is a tested array/recursive call to this function
      // take a set of spaces off the beginning
      $spaces = $match[1];
      $spaces_length = strlen($spaces);
      $lines_total = count($lines);
      for ($i = 0; $i < $lines_total; $i++) {
        if (substr($lines[$i], 0, $spaces_length) == $spaces) {
          $lines[$i] = substr($lines[$i], $spaces_length);
        }
      }
    }
    array_shift($lines); // Array
    array_shift($lines); // (
    array_pop($lines); // )
    $in = implode("\n", $lines);
    // make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
    preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
    $pos = array();
    $previous_key = '';
    $in_length = strlen($in);
    // store the following in $pos:
    // array with key = key of the parsed array's item
    // value = array(start position in $in, $end position in $in)
    foreach ($matches as $match) {
      $key = $match[1][0];
      $start = $match[0][1] + strlen($match[0][0]);
      $pos[$key] = array($start, $in_length);
      if ($previous_key != '') $pos[$previous_key][1] = $match[0][1] - 1;
      $previous_key = $key;
    }
    $ret = array();
    foreach ($pos as $key => $where) {
      // recursively see if the parsed out value is an array too
      $ret[$key] = print_r_reverse(substr($in, $where[0], $where[1] - $where[0]));
    }
    return $ret;
  }
}

// This is really delicate and prone to PHP out of memory errors.
// Clean out drupal_debug.txt frequently to avoid them.
$debug = file_get_contents(DEBUG_FILE_PATH);
$array_strings = find_arrays($debug);
foreach ($array_strings as $array_string) {
  krumo(print_r_reverse($array_string));
}

?>