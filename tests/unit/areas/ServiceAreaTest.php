<?php

use Mockery as m;
use WasteMaster\v1\ServiceAreas\ServiceAreaManager;

class ServiceAreaTest extends UnitTestCase
{
    /**
     * @var \WasteMaster\v1\ServiceAreas\ServiceAreaManager
     */
    protected $manager;
    protected $areas;

    public function setUp()
    {
        parent::setUp();

        $this->areas = m::mock('App\ServiceArea');
        $this->manager = new ServiceAreaManager($this->areas);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testFindSuccess()
    {
        $this->areas->shouldReceive('find')
            ->once()
            ->andReturn((object)[
                'id' => 2
            ]);

        $area = $this->manager->find(2);

        $this->assertEquals(2, $area->id);
    }

    public function testGetThrowsOnFailure()
    {
        $this->setExpectedException('WasteMaster\v1\ServiceAreas\ServiceAreaNotFound');

        $this->areas->shouldReceive('find')
                   ->once()
                   ->andReturn();

        $this->manager->find(1);
    }

    public function testCreateThrowsOnNoData()
    {
        $this->setExpectedException('WasteMaster\v1\ServiceAreas\MissingRequiredFields');

        $this->manager->create();
    }

    public function testCreateThrowsWhenAreaExists()
    {
        $this->setExpectedException('WasteMaster\v1\ServiceAreas\ServiceAreaExists');

        $this->areas->shouldReceive('where->count')
                   ->once()
                   ->andReturn(1);

        $area = $this->manager
            ->setName('ChicagoLand')
            ->create();
    }

    public function testCreateSuccess()
    {
        $data = [
            'name' => 'ChicagoLand'
        ];

        $this->areas->shouldReceive('where->count')
                    ->once()
                    ->andReturn(0);
        $this->areas->shouldReceive('create')
            ->once()
            ->with(m::subset($data))
            ->andReturn((object)$data);

        $area = $this->manager
            ->setName('ChicagoLand')
            ->create();

        $this->assertTrue(is_object($area));
        $this->assertEquals('ChicagoLand', $area->name);
    }

    public function testDeleteThrowsOnNotFound()
    {
        $this->setExpectedException('WasteMaster\v1\ServiceAreas\ServiceAreaNotFound');

        $this->areas->shouldReceive('find')
                   ->once()
                   ->with(12)
                   ->andReturn();

        $this->manager->delete(12);
    }

    public function testDeleteSuccess()
    {
        $area = m::mock('App\ServiceArea');

        $this->areas->shouldReceive('find')
                    ->once()
                    ->with(12)
                    ->andReturn($area);

        $area->shouldReceive('delete')
            ->once()
            ->andReturn();

        $this->manager->delete(12);
    }

}
