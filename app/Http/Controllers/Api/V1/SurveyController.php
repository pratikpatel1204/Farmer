<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Support\Notify;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Survey (website /survey jaisa):
 *  - Sirf Active survey
 *  - Member ek survey ek baar, pehle submit ke 24 ghante tak answer badal sakta hai, phir lock
 */
class SurveyController extends ApiController
{
    /** GET /survey  (token ho to my_response bhi) */
    public function show()
    {
        $survey = Survey::current();

        if (! $survey) {
            return $this->ok(['survey' => null, 'questions' => [], 'my_response' => null], 'No active survey right now.');
        }

        $questions = $survey->visibleQuestions()->get();
        $user      = auth('sanctum')->user();
        $mine      = null;

        if ($user) {
            $response = SurveyResponse::with('answers')
                ->where('survey_id', $survey->id)
                ->where('user_id', $user->id)
                ->first();

            if ($response) {
                $mine = [
                    'submitted_at' => optional($response->submitted_at)->toIso8601String(),
                    'locked_at'    => optional($response->locked_at)->toIso8601String(),
                    'can_edit'     => $response->canEdit(),
                    'answers'      => (object) $response->answers->pluck('option_no', 'survey_question_id')->all(),
                ];
            }
        }

        return $this->ok([
            'survey'      => ['id' => $survey->id, 'name' => $survey->name, 'description' => $survey->description],
            'questions'   => $questions->map(fn ($q) => [
                'id'            => $q->id,
                'question_html' => $q->question_html,
                'question_text' => $q->question_text,
                'options'       => (object) $q->options,
            ])->values(),
            'my_response' => $mine,
        ]);
    }

    /** POST /survey  { survey_id, answers: { question_id: option_no } } */
    public function submit(Request $request)
    {
        $survey = Survey::current();

        if (! $survey || (int) $request->input('survey_id') !== $survey->id) {
            return $this->fail('This survey is closed now. Please check the latest survey.', 422);
        }

        $questions = $survey->visibleQuestions()->get();
        $user      = $request->user();

        $response = SurveyResponse::where('survey_id', $survey->id)->where('user_id', $user->id)->first();

        if ($response && ! $response->canEdit()) {
            return $this->fail('Your answers are locked. Answers can be changed only within 24 hours of your first submission.', 422);
        }

        $input  = (array) $request->input('answers', []);
        $errors = [];
        foreach ($questions as $index => $question) {
            $selected = (int) ($input[$question->id] ?? 0);
            if (! array_key_exists($selected, $question->options)) {
                $errors['answers.' . $question->id] = ['Please select an answer for question ' . ($index + 1) . '.'];
            }
        }

        if ($errors) {
            return $this->fail('Please answer all the questions before submitting.', 422, $errors);
        }

        $isUpdate = (bool) $response;

        try {
            DB::transaction(function () use ($survey, $questions, $input, $user, &$response) {
                if (! $response) {
                    $now = now();
                    $response = SurveyResponse::create([
                        'survey_id'    => $survey->id,
                        'user_id'      => $user->id,
                        'submitted_at' => $now,
                        'locked_at'    => $now->copy()->addHours(SurveyResponse::EDIT_HOURS),
                    ]);
                }

                foreach ($questions as $question) {
                    $optionNo = (int) $input[$question->id];
                    SurveyAnswer::updateOrCreate(
                        ['survey_response_id' => $response->id, 'survey_question_id' => $question->id],
                        ['option_no' => $optionNo, 'option_text' => $question->options[$optionNo]]
                    );
                }

                $response->touch();
            });
        } catch (QueryException $e) {
            return $this->fail('You have already submitted this survey.', 422);
        }

        if (! $isUpdate) {
            Notify::surveySubmitted($user, $survey->name);
        }

        return $this->ok(
            ['locked_at' => optional($response->locked_at)->toIso8601String()],
            $isUpdate ? 'Thank you! Your answers have been updated.' : 'Thank you! Your survey has been submitted.'
        );
    }
}
