<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

/**
 * Admin: Survey master (SmartHR theme)
 * Ek time par sirf ek survey Active rehta hai. Active survey ke question front par dikhte hain.
 */
class SurveyAdminController extends Controller
{
    public function index(Request $request)
    {
        $surveys = Survey::query()
            ->withCount(['questions', 'responses'])
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderByDesc('status')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.survey.index', compact('surveys'));
    }

    public function create()
    {
        $survey = new Survey(['status' => Survey::INACTIVE]);

        return view('admin.survey.form', compact('survey'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $survey = Survey::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => Survey::INACTIVE,
        ]);

        if ((int) $data['status'] === Survey::ACTIVE) {
            $survey->activate();
        }

        return redirect()->route('admin.survey.questions.index', $survey)
            ->with('success', 'Survey created successfully. Now add questions to it.');
    }

    public function edit(Survey $survey)
    {
        return view('admin.survey.form', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $data = $this->validated($request);

        $survey->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if ((int) $data['status'] === Survey::ACTIVE) {
            $survey->activate();
        } else {
            $survey->update(['status' => Survey::INACTIVE]);
        }

        return redirect()->route('admin.survey.index')->with('success', 'Survey updated successfully.');
    }

    /**
     * Active / Inactive switch (list page se)
     */
    public function status(Survey $survey)
    {
        if ($survey->is_active) {
            $survey->update(['status' => Survey::INACTIVE]);

            return back()->with('success', 'Survey "' . $survey->name . '" is now inactive. The website will show the default banners.');
        }

        if (! $survey->visibleQuestions()->exists()) {
            return back()->with('error', 'Please add at least one question before activating this survey.');
        }

        $survey->activate();

        return back()->with('success', 'Survey "' . $survey->name . '" is now active. All other surveys have been set to inactive.');
    }

    public function destroy(Survey $survey)
    {
        // Questions, responses aur answers foreign key cascade se apne aap delete ho jaate hain
        $survey->delete();

        return redirect()->route('admin.survey.index')->with('success', 'Survey deleted successfully.');
    }

    /**
     * Result: har question ke har option par kitne logon ne click kiya + kisne survey diya
     */
    public function results(Survey $survey)
    {
        $questions = $survey->questions()->get();

        // [question_id][option_no] => total
        $counts = SurveyAnswer::query()
            ->whereIn('survey_question_id', $questions->pluck('id'))
            ->selectRaw('survey_question_id, option_no, COUNT(*) as total')
            ->groupBy('survey_question_id', 'option_no')
            ->get()
            ->groupBy('survey_question_id')
            ->map(fn ($rows) => $rows->pluck('total', 'option_no'));

        $responses = SurveyResponse::with('user')
            ->where('survey_id', $survey->id)
            ->latest('submitted_at')
            ->paginate(20);

        return view('admin.survey.results', compact('survey', 'questions', 'counts', 'responses'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:0,1'],
        ], [
            'name.required' => 'Please enter the survey name.',
            'status.required' => 'Please select the status.',
        ]);
    }
}
