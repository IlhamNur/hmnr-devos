<?php

namespace App\Http\Controllers;

use App\Models\ApiRequest;
use App\Services\ApiLab\ApiExecutorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiLabController extends Controller
{
    public function index(Request $request)
    {
        $history = auth()->user()->apiRequests()
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return view('labs.api.index', compact('history'));
    }

    public function execute(Request $request, ApiExecutorService $executor)
    {
        $validated = $request->validate([
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'url' => 'required|url',
            'headers' => 'nullable|array',
            'headers.*.key' => 'nullable|string',
            'headers.*.value' => 'nullable|string',
            'query_parameters' => 'nullable|array',
            'query_parameters.*.key' => 'nullable|string',
            'query_parameters.*.value' => 'nullable|string',
            'body_type' => 'required|in:none,json,raw,form',
            'body' => 'nullable|string',
        ]);

        // Construct final URL with query parameters
        $url = $validated['url'];
        if (!empty($validated['query_parameters'])) {
            $queryParams = [];
            foreach ($validated['query_parameters'] as $param) {
                if (!empty($param['key'])) {
                    $queryParams[$param['key']] = $param['value'] ?? '';
                }
            }
            if (!empty($queryParams)) {
                $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . http_build_query($queryParams);
            }
        }

        // Execute request
        $result = $executor->execute(
            $validated['method'],
            $url,
            $validated['headers'] ?? [],
            $validated['body'] ?? null
        );

        $headersToSave = $validated['headers'] ?? [];
        foreach ($headersToSave as &$header) {
            if (strtolower($header['key'] ?? '') === 'authorization') {
                $header['value'] = '******** (redacted)';
            }
        }

        // Save to history
        $apiRequest = auth()->user()->apiRequests()->create([
            'method' => $validated['method'],
            'url' => $validated['url'],
            'headers' => $headersToSave,
            'query_parameters' => $validated['query_parameters'] ?? [],
            'body_type' => $validated['body_type'],
            'body' => $validated['body'] ?? null,
            'last_status' => $result['status'],
            'last_duration_ms' => $result['duration'],
            'last_response_headers' => $result['headers'],
            'last_response_body' => $result['body'],
            'last_error' => $result['error'],
        ]);

        return response()->json([
            'request_id' => $apiRequest->id,
            'result' => $result
        ]);
    }

    public function destroy(ApiRequest $apiRequest)
    {
        if ($apiRequest->user_id !== auth()->id()) {
            abort(403);
        }
        
        $apiRequest->delete();
        
        return redirect()->route('api-lab.index')->with('status', 'Request deleted from history.');
    }
}
