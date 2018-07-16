<?php

namespace WasteMaster\v1\Clients;

class ClientNotFound extends \Exception{}
class ClientExists extends \Exception{}
class InvalidEmail extends \InvalidArgumentException{}
class MissingRequiredFields extends \InvalidArgumentException{}
class NothingToUpdate extends \BadMethodCallException{}

class CityNotFound extends \InvalidArgumentException{}
