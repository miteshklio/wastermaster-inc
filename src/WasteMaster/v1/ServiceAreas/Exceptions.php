<?php

namespace WasteMaster\v1\ServiceAreas;

class ServiceAreaNotFound extends \Exception{}
class ServiceAreaExists extends \Exception{}
class MissingRequiredFields extends \InvalidArgumentException{}
class NothingToUpdate extends \BadMethodCallException{}
