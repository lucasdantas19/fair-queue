<?php

namespace App\Models;

use FairQueue\Persistence\EloquentScheduleJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleJob extends EloquentScheduleJob
{
    use HasFactory;
}
