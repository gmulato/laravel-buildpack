<?php

namespace App\Models\Traits;

trait HidesTimestamps
{
    public function initializeHidesTimestamps()
    {
        $this->hidden = array_merge($this->hidden ?? [], ['created_at', 'updated_at']);
    }
}