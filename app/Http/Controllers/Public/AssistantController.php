<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Services\AssistantService;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function ask(Request $request, AssistantService $assistant)
    {
        $data = $request->validate([
            'question' => 'required|string|max:300',
        ]);

        $identifier = 'assistant:' . $request->session()->getId();

        // Batasi pertanyaan yang butuh AI (bukan FAQ) — 15x/hari per sesi browser
        $result = $assistant->answer($data['question']);

        if ($result['source'] === 'ai') {
            if (!AiUsageLog::attempt($identifier, 'assistant', 15)) {
                return response()->json([
                    'answer' => 'Batas tanya-jawab hari ini sudah tercapai. Coba lagi besok, atau cek menu FAQ di bawah.',
                ]);
            }
        }

        return response()->json(['answer' => $result['answer']]);
    }
}