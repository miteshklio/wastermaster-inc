<?php

use Mockery as m;

class HistoryManagerTest extends UnitTestCase
{
    /**
     * @var \App\History
     */
    protected $history;

    /**
     * @var \WasteMaster\v1\Leads\LeadManager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->history = m::mock('\App\History');
        $this->manager = new \WasteMaster\v1\History\HistoryManager($this->history);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testFindForLead()
    {
        $this->history->shouldReceive('where')
            ->with('lead_id', 1)
            ->andReturn($this->history);
        $this->history->shouldReceive('where')
            ->with('type', 'foo')
            ->andReturn($this->history);
        $this->history->shouldReceive('with->get')
            ->once()
            ->andReturn((object)[
                'id' => 1
            ]);

        $this->assertEquals(1, $this->manager->findForLead(1, 'foo')->id);
    }

    public function testDeleteForLead()
    {
        $this->history->shouldReceive('where')
            ->with('lead_id', 1)
            ->andReturn($this->history);
        $this->history->shouldReceive('delete')
            ->once();

        $this->manager->deleteForLead(1);
    }

    public function testCreateSuccess()
    {
        $expects = [
            'lead_id' => 2,
            'hauler_id' => 3,
            'type' => 'foo'
        ];

        $this->history->shouldReceive('create')
            ->with(Mockery::subset($expects))
            ->once()
            ->andReturn((object)[
                'id' => 4
            ]);

        $item = $this->manager
                    ->setLeadID(2)
                    ->setHaulerID(3)
                    ->setType('foo')
                    ->create();

        $this->assertEquals(4, $item->id);
    }

}
