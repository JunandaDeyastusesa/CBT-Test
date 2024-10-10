<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAnswer;
use App\Models\CourseQuestion;
use App\Models\StudentAnswer;
// use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $my_courses = $user->courses()->with('category')->orderBy('id', 'DESC')->get();

        foreach ($my_courses as $course) {
            $totalQuestionsCount = $course->questions()->count();

            $answeredQuestionsCount = StudentAnswer::where('user_id', $user->id)
                ->whereHas('question', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->distinct()->count('course_question_id');

            if ($answeredQuestionsCount < $totalQuestionsCount) {
                $firstUnansweredQuestion = CourseQuestion::where('course_id', $course->id)
                    ->whereNotIn('id', function ($query) use ($user) {
                        $query->select('course_question_id')->from('student_answers')
                            ->where('user_id', $user->id);
                    })->orderBy('id', 'asc')->first();

                $course->nextQuestionId = $firstUnansweredQuestion ? $firstUnansweredQuestion->id : null;
            } else {
                $course->nextQuestionId = null;
            }
        }

        return view('student.courses.learning', [
            'my_courses' => $my_courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course, $question)
    {
        DB::beginTransaction();

        try {
            // Validate the request
            $validate = $request->validate([
                'answer_id' => 'required|exists:course_answers,id'
            ]);

            // Fetch the selected answer and question
            $selectedAnswer = CourseAnswer::findOrFail($validate['answer_id']);
            $question_details = CourseQuestion::findOrFail($question);

            // Check if the answer belongs to the question
            if ($selectedAnswer->course_question_id != $question) {
                return response()->json(['success' => false, 'message' => 'Jawaban tidak tersedia pada pertanyaan!'], 400);
            }

            // Find existing answer for the user
            StudentAnswer::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'course_question_id' => $question
                ],
                [
                    'answer' => $selectedAnswer->score
                ]
            );

            DB::commit();

            // Fetch the next question
            $nextQuestion = CourseQuestion::where('course_id', $course->id)
                ->where('id', '>', $question)
                ->orderBy('id', 'asc')
                ->first();

            if ($nextQuestion) {
                return redirect()->route('dashboard.learning.course', ['course' => $course->id, 'question' => $nextQuestion->id]);
            } else {
                return redirect()->route('dashboard.learning.finished.course', $course->id);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'System error! ' . $e->getMessage()], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAnswer $studentAnswer)
    {
        //
    }
}
