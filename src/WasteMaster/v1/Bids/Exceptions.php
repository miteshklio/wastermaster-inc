<?php

namespace WasteMaster\v1\Bids;

class BidNotFound extends \Exception{}
class BidExists extends \Exception{}
class InvalidEmail extends \InvalidArgumentException{}
class MissingRequiredFields extends \InvalidArgumentException{}
class NothingToUpdate extends \BadMethodCallException{}
class InvalidStatus extends \Exception{}
