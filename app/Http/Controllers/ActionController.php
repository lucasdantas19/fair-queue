<?php

namespace App\Http\Controllers;

use App\Actions\FairQueue\ScheduleJobAction;
use App\Jobs\ActionJob;
use App\Jobs\ActionTenSecondsJob;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function requestJob(Request $request, ScheduleJobAction $scheduleJobAction)
    {
        $data = $request->validate([
            'duration' => ['required', 'integer'],
        ]);

        $scheduleJobAction->execute(
            new ActionJob($request->user(), $data['duration']),
            'user',
            $request->user()->id
        );

        return redirect()
            ->route('home')
            ->with('status', 'Job solicitado com sucesso.');
    }

    public function requestTenSecondsJob(Request $request)
    {
        // Handle the job request logic here
        // For example, you can validate the request and dispatch a job

        // $request->validate([
        //     'job_type' => 'required|string',
        //     'payload' => 'required|array',
        // ]);

        dispatch(new ActionTenSecondsJob);

        // Dispatch the job (assuming you have a Job class)
        // Example: dispatch(new \App\Jobs\YourJob($request->input('payload')));

        return response()->json(['message' => 'Ten seconds job requested successfully.']);
    }
}
