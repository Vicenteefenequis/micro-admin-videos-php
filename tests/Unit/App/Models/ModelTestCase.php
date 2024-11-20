<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

abstract class ModelTestCase extends TestCase
{
    abstract protected function model(): Model;
    abstract protected function traits(): array;
    abstract protected function fillables(): array;
    abstract protected function casts(): array;

    public function testIfUseTraits()
    {
        $traitsUsed = array_keys(class_uses($this->model()));
        $this->assertEquals($this->traits(), $traitsUsed);
    }

    public function testIncrementingIsFalse()
    {
        $model = $this->model();
        $this->assertFalse($model->getIncrementing());
    }

    public function testHasCasts()
    {
        $casts = $this->model()->getCasts();
        $this->assertEquals($this->casts(), $casts);
    }

    public function testFillables()
    {
        $this->assertEquals($this->fillables(), $this->model()->getFillable());
    }
}
