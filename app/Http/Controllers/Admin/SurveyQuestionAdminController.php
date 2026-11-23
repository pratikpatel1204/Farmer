<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Admin: Survey ke questions (CKEditor) + option_1..option_6
 * Khali option save nahi hota (null), front par wo skip ho jata hai.
 * Sahi answer ka koi option nahi hai, ye exam nahi survey hai.
 */
class SurveyQuestionAdminController extends Controller
{
    public function index(Survey $survey)
    {
        $questions = $survey->questions()->withCount('answers')->get();

        return view('admin.survey.questions.index', compact('survey', 'questions'));
    }

    public function create(Survey $survey)
    {
        $question = new SurveyQuestion([
            'status' => 1,
            'sort_order' => (int) $survey->questions()->max('sort_order') + 1,
        ]);

        return view('admin.survey.questions.form', compact('survey', 'question'));
    }

    public function store(Request $request, Survey $survey)
    {
        $survey->questions()->create($this->validated($request));

        $message = 'Question added successfully.';

        if ($request->input('save_action') === 'save_new') {
            return redirect()->route('admin.survey.questions.create', $survey)->with('success', $message);
        }

        return redirect()->route('admin.survey.questions.index', $survey)->with('success', $message);
    }

    public function edit(Survey $survey, SurveyQuestion $question)
    {
        $this->belongsTo($survey, $question);

        return view('admin.survey.questions.form', compact('survey', 'question'));
    }

    public function update(Request $request, Survey $survey, SurveyQuestion $question)
    {
        $this->belongsTo($survey, $question);

        $question->update($this->validated($request));

        return redirect()->route('admin.survey.questions.index', $survey)->with('success', 'Question updated successfully.');
    }

    public function destroy(Survey $survey, SurveyQuestion $question)
    {
        $this->belongsTo($survey, $question);

        $question->delete();

        return redirect()->route('admin.survey.questions.index', $survey)->with('success', 'Question deleted successfully.');
    }

    private function belongsTo(Survey $survey, SurveyQuestion $question): void
    {
        abort_unless((int) $question->survey_id === (int) $survey->id, 404);
    }

    private function validated(Request $request): array
    {
        $rules = [
            'question' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:0,1'],
        ];
        for ($i = 1; $i <= SurveyQuestion::MAX_OPTIONS; $i++) {
            $rules['option_' . $i] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules, [
            'question.required' => 'Please enter the question.',
            'option_*.max' => 'An option can have a maximum of 255 characters.',
        ]);

        // CKEditor khali hone par bhi "<p>&nbsp;</p>" bhejta hai
        $plain = trim(str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags($data['question']), ENT_QUOTES, 'UTF-8')));
        if ($plain === '') {
            throw ValidationException::withMessages(['question' => 'Please enter the question.']);
        }

        // Khali option -> null (front par skip). Option ka number nahi badalta,
        // taaki purane answers (option_no) galat option par na chale jaayein.
        $filled = [];
        for ($i = 1; $i <= SurveyQuestion::MAX_OPTIONS; $i++) {
            $text = trim((string) ($data['option_' . $i] ?? ''));
            $data['option_' . $i] = $text === '' ? null : $text;
            if ($text !== '') {
                $filled[] = mb_strtolower($text);
            }
        }

        if (count($filled) < 2) {
            throw ValidationException::withMessages(['options' => 'Please add at least 2 options.']);
        }

        if (count($filled) !== count(array_unique($filled))) {
            throw ValidationException::withMessages(['options' => 'Two options cannot be the same.']);
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
