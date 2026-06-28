<?php

namespace App\Http\Controllers;

use App\Http\Resources\FAQCategoryResource;
use App\Http\Resources\FAQResource;
use App\Models\FAQCategory;
use App\Models\FrequentlyAskedQuestions;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function getFrequentlyAskedQuestions(): \Illuminate\Http\JsonResponse
    {
        $faqs = FAQCategory::all();
        return response()->json(['success' => true, "faq_category" => FAQCategoryResource::collection($faqs)]);
    }

    public function searchFaq(Request $request){
        $query = $request->input('query');
        if ($query) {
            $searchResult = FrequentlyAskedQuestions::where('question', 'LIKE', '%' . $query . '%')->get();
            return $this->jsonSuccess(200, 'Questions Found', FAQResource::collection($searchResult), 'questions');
        } else {
            return $this->jsonSuccess(200, 'Questions Found', [], 'questions');
        }
    }
}
