<?php

namespace App\Http\Controllers;

use App\Models\MvelExecution;
use App\Services\MvelLab\MvelExecutorService;
use Illuminate\Http\Request;

class MvelLabController extends Controller
{
    public function index(Request $request)
    {
        $history = auth()->user()->mvelExecutions()
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return view('labs.mvel.index', compact('history'));
    }

    public function execute(Request $request, MvelExecutorService $executor)
    {
        $validated = $request->validate([
            'expression' => 'required|string',
            'variables' => 'nullable|string', // JSON string
        ]);

        $variables = [];
        if (!empty($validated['variables'])) {
            $variables = json_decode($validated['variables'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Invalid JSON in variables: ' . json_last_error_msg()
                ], 422);
            }
        }

        $result = $executor->execute($validated['expression'], $variables);

        $execution = auth()->user()->mvelExecutions()->create([
            'expression' => $validated['expression'],
            'variables' => $variables,
            'result' => is_string($result['result']) ? $result['result'] : json_encode($result['result']),
            'result_type' => $result['result_type'],
            'error' => $result['error'],
            'execution_time_ms' => $result['execution_time'],
        ]);

        return response()->json([
            'execution_id' => $execution->id,
            'result' => $result
        ]);
    }

    public function destroy(MvelExecution $mvelExecution)
    {
        if ($mvelExecution->user_id !== auth()->id()) {
            abort(403);
        }
        
        $mvelExecution->delete();
        
        return redirect()->route('mvel-lab.index')->with('status', 'Execution deleted from history.');
    }
}

