<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Banner;
use App\Models\Survey;
use App\Models\User;
use App\Support\ApiData;
use App\Support\Market;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * GET /home : app ka poora Home ek call me (website HomeController jaisa, same limits).
 */
class HomeController extends ApiController
{
    public function index()
    {
        return $this->ok([
            'banners'       => $this->banners(),
            'survey'        => $this->survey(),
            'want_products' => $this->products('want'),
            'have_products' => $this->products('have'),
            'profiles'      => $this->profiles(),
            'blogs'         => BlogController::cards(4),
        ]);
    }

    private function banners(int $limit = 6)
    {
        return Banner::where('status', 'Active')
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'title', 'description', 'image'])
            ->map(fn ($banner) => [
                'id'          => $banner->id,
                'title'       => $banner->title,
                'description' => $banner->description,
                'image'       => ApiData::url($banner->image),
            ]);
    }

    /** Active survey ho to chhota preview (website hero jaisa), warna null */
    private function survey(): ?array
    {
        if (! class_exists(Survey::class) || ! Schema::hasTable('surveys')) {
            return null;
        }

        $survey = Survey::current();
        if (! $survey) {
            return null;
        }

        $questions = $survey->visibleQuestions()->get();
        if ($questions->isEmpty()) {
            return null;
        }

        return [
            'id'                => $survey->id,
            'name'              => $survey->name,
            'description'       => $survey->description,
            'total_questions'   => $questions->count(),
            'questions_preview' => $questions->take(3)->map(fn ($q) => $q->question_text)->values(),
        ];
    }

    private function products(string $type, int $limit = 12)
    {
        return Market::productQuery()
            ->with(ApiData::PRODUCT_WITH)
            ->where('listing_type', $type)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn ($product) => ApiData::productCard($product));
    }

    private function profiles(int $limit = 16)
    {
        return ProfileController::approvedMembers()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn ($user) => ApiData::memberCard($user));
    }
}
