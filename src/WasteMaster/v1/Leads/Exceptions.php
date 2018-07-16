<?php

namespace WasteMaster\v1\Leads;

class LeadNotFound extends \Exception{}
class LeadExists extends \Exception{}
class InvalidEmail extends \InvalidArgumentException{}
class MissingRequiredFields extends \InvalidArgumentException{}
class NothingToUpdate extends \BadMethodCallException{}

class CityNotFound extends \InvalidArgumentException{}
