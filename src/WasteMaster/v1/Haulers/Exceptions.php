<?php

namespace WasteMaster\v1\Haulers;

class HaulerNotFound extends \Exception{}
class HaulerExists extends \Exception{}
class InvalidEmails extends \InvalidArgumentException{}
class MissingRequiredFields extends \InvalidArgumentException{}

class CityNotFound extends \InvalidArgumentException{}
