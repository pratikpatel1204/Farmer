<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Support\Notify;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Front: Survey page (home ke "View all" button se naye tab me khulta hai)
 *  - Sirf Active survey dikhta hai
 *  - Login (web guard) member hi answer de sakta hai, guest ko "Please log in" message
 *  - Ek member ek survey sirf ek baar. Pehle submit ke 24 ghante tak answer badal sakta hai, phir lock
 *  - Submit ke baad sirf Thank you message, koi score / result nahi
 */
class SurveyController extends Controller
{
    public function show()
    {
        $survey = Survey::current();
        $questions = $survey ? $survey->visibleQuestions()->get() : collect();

        $user = auth('web')->user();
        $response = null;
        $answers = [];

        if ($survey && $user) {
            $response = SurveyResponse::with('answers')
                ->where('survey_id', $survey->id)
                ->where('user_id', $user->id)
                ->first();

            $answers = $response ? $response->answers->pluck('option_no', 'survey_question_id')->all() : [];
        }

        return view('front.survey.show', compact('survey', 'questions', 'response', 'answers'));
    }

    public function submit(Request $request)
    {
        $survey = Survey::current();

        if (! $survey || (int) $request->input('survey_id') !== $survey->id) {
            return redirect()->route('survey.show')->with('survey_error', 'This survey is closed now. Please check the latest survey.');
        }

        $questions = $survey->visibleQuestions()->get();
        $userId = auth('web')->id();

        $response = SurveyResponse::where('survey_id', $survey->id)->where('user_id', $userId)->first();

        if ($response && ! $response->canEdit()) {
            return redirect()->route('survey.show')->with('survey_error', 'Your answers are locked. Answers can be changed only within 24 hours of your first submission.');
        }

        // Har question ka ek option zaroori, aur option usi question ka hona chahiye
        $input = (array) $request->input('answers', []);
        $errors = [];
        foreach ($questions as $index => $question) {
            $selected = (int) ($input[$question->id] ?? 0);
            if (! array_key_exists($selected, $question->options)) {
                $errors['answers.' . $question->id] = 'Please select an answer for question ' . ($index + 1) . '.';
            }
        }

        if ($errors) {
            return back()->withInput()->withErrors($errors)
                ->with('survey_error', 'Please answer all the questions before submitting.');
        }

        $isUpdate = (bool) $response;

        try {
            DB::transaction(function () use ($survey, $questions, $input, $userId, &$response) {
                if (! $response) {
                    $now = now();
                    $response = SurveyResponse::create([
                        'survey_id' => $survey->id,
                        'user_id' => $userId,
                        'submitted_at' => $now,
                        'locked_at' => $now->copy()->addHours(SurveyResponse::EDIT_HOURS),
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
            // Do baar jaldi-jaldi submit dabane par unique index rok deta hai
            return redirect()->route('survey.show')->with('survey_error', 'You have already submitted this survey.');
        }

        // Admin ko "New survey response", sirf pehli baar submit par (answer badalne par nahi)
        if (! $isUpdate) {
            Notify::surveySubmitted(auth('web')->user(), $survey->title ?? null);
        }

        return redirect()->route('survey.show')->with(
            'survey_success',
            $isUpdate ? 'Thank you! Your answers have been updated.' : 'Thank you! Your survey has been submitted.'
        );
    }
}