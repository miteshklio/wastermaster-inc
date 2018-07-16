<?php namespace WasteMaster\v1\History;

use App\History;

class HistoryManager
{
    /**
     * @var History
     */
    protected $histories;

    protected $lead_id;
    protected $hauler_id;
    protected $type;

    public function __construct(History $history)
    {
        $this->histories = $history;
    }

    /**
     * Finds all history entries for a lead of a certain type.
     *
     * @param int    $leadID
     * @param string $type
     */
    public function findForLead(int $leadID, string $type)
    {
        return $this->histories
            ->where('lead_id', $leadID)
            ->where('type', strtolower($type))
            ->with('hauler')
            ->get();
    }

    /**
     * Deletes all histories items for a single lead.
     *
     * @param int    $leadID
     * @param string $type
     *
     * @return mixed
     */
    public function deleteForLead(int $leadID)
    {
        return $this->histories
            ->where('lead_id', $leadID)
            ->delete();
    }

    /**
     * Sets the ID of the Lead for a new history entry.
     *
     * @param int $leadID
     *
     * @return $this
     */
    public function setLeadID(int $leadID)
    {
        $this->lead_id = $leadID;

        return $this;
    }

    /**
     * Sets the ID of the Hauler for a new history entry.
     *
     * @param int $haulerID
     *
     * @return $this
     */
    public function setHaulerID(int $haulerID)
    {
        $this->hauler_id = $haulerID;

        return $this;
    }

    /**
     * Sets the type of a new history entry, like post_bid_match, pre_bid_match, etc.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = strtolower($type);

        return $this;
    }

    public function create()
    {
        $this->checkRequired();

        $history = $this->histories->create([
            'lead_id' => $this->lead_id,
            'hauler_id' => $this->hauler_id,
            'type' => $this->type
        ]);

        $this->reset();

        return $history;
    }

    public function reset()
    {
        $this->lead_id = null;
        $this->hauler_id = null;
        $this->type = null;
    }

    protected function checkRequired()
    {
        $requiredFields = [
            'lead_id', 'hauler_id', 'type'
        ];

        $errorFields = [];

        foreach ($requiredFields as $field)
        {
            if (empty($this->$field))
            {
                $errorFields[] = $field;
            }
        }

        if (count($errorFields))
        {
            throw new MissingRequiredFields(trans('messages.clientValidationErrors', ['fields' => implode(', ', $errorFields)]));
        }
    }

    public function listNames($haulers=null)
    {
        $names = [];
        foreach ($haulers as $record)
        {
            if (empty($record))
            {
                continue;
            }

            $hauler = $record->hauler;

            if (empty($hauler))
            {
                continue;
            }

            $names[] = $hauler->name;
        }

        return implode("\n", $names);
    }
}
