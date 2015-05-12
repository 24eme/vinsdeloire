<?php

function sprintFloat($float, $format = "%01.02f") 
{
	if (is_null($float))
		return null;
  return sprintf($format, $float);
}

function sprintFloatFr($float, $format = "%01.02f")
{

  return preg_replace('/\./', ',', sprintFloat($float, $format));
}

function echoFloat($float) 
{
  echo sprintFloat($float);
}

function echoLongFloat($float) 
{
  echo sprintFloat($float, "%01.04f");
}

function echoLongFloatFr($float)
{
  echo sprintFloatFr($float, "%01.04f");
}

function echoFloatFr($float)
{
  echo sprintFloatFr($float);
}

function echoSignedFloat($float) 
{
  echo ($float>0)? '+'.sprintFloat($float) : sprintFloat($float);
}

function echoArialFloat($float) {
    echo number_format($float, 2, '.', ' ');
}

function getArialFloat($float) {
    return number_format($float, 2, '.', ' ');
}