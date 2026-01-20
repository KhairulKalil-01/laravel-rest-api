<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePromptRequest;
use App\Http\Resources\PromptGenerationResource;
use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenAI\Exceptions\RateLimitException;
use OpenAI\Exceptions\TransporterException;
use Illuminate\Http\JsonResponse;

class PromptGenerationController extends Controller
{
    public function __construct(private OpenAiService $openAiService)
    {
        
    }


    public function index()
    {
        $user = request()->user();
        $promptGenerations = $user->imageGenerations()->latest()->paginate(10);
        return PromptGenerationResource::collection($promptGenerations);
    }

    public function store(GeneratePromptRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $image = $request->file('image');

            $originalName = $image->getClientOriginalName();
            $sanitizeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $image->getClientOriginalExtension();
            $safeFilename = $sanitizeName . '_' . Str::random(32) . '.' . $extension;

            $imagePath = $image->storeAs('uploads/images', $safeFilename, 'public');

            $generatedPrompt = $this->openAiService->generatePromptFromImage($image);

            $imageGeneration = $user->imageGenerations()->create([
                'image_path' => $imagePath,
                'generated_prompt' => $generatedPrompt,
                'original_filename' => $originalName,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
            ]);

            return (new PromptGenerationResource($imageGeneration))->response();
        } catch (RateLimitException $e) {
            return response()->json([
                'message' => 'OpenAI API rate limit exceeded. Please try again later.',
                'error' => 'rate_limit_exceeded'
            ], 429);
        } catch (TransporterException $e) {
            return response()->json([
                'message' => 'Failed to connect to OpenAI API. Please check your network connection and try again.',
                'error' => 'api_connection_error'
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => 'processing_error'
            ], 500);
        }
    }
}
