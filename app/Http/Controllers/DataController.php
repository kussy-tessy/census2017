<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Selection;
use App\Question;
use App\QuestionGroup;
use App\Answer;
use App\Hobby;

class DataController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function answers(Request $req)
    {
        $firstQuestionId = $req->input('first');
        $secondQuestionId = $req->input('second');

        if($secondQuestionId === null){
            return $this->getSingleAnswers($firstQuestionId, $req);
        } else {
            return $this->getCorrelationAnswers($firstQuestionId, $secondQuestionId, $req);
        }
    }

    private function getAnswerBuilder($req)
    {
        $query = Answer::query();

        $query->when($req->input('onlyWorker') === 'true', fn() => $query->onlyWorker());
        $query->when($req->input('onlyActive') === 'true', fn() => $query->onlyActive());
        $query->when($req->input('onlyActor') === 'true', fn() => $query->onlyActor());

        return $query;
    }

    private function getSingleAnswers($questionId, $req)
    {
        if($questionId == 90){ // 趣味
            $hobbies = Hobby::all();
            $response['xlabel'] = $hobbies->pluck('description');
            $response['ylabel'] = [null];
            $response['value'] = $hobbies->pluck('count')->map(fn($v) => [$v]);
            $response['repr'] = [null];
            return $response;
        } 

        $question = Question::find($questionId);
        $questionSelections = Selection::where('question_id', $questionId)->get()->sortBy('selection_cd');
        $values = [];
        $answer = $this->getAnswerBuilder($req);
        $rawValue = $answer->get([$question->name])->pluck($question->name)->countBy();
        $value = $questionSelections->pluck('selection_cd')->map(fn($cd) => $rawValue[$cd] ?? 0)->sortKeys();
        
        $response['xlabel'] = $questionSelections->pluck('description');
        $response['ylabel'] = [null];
        $response['value'] = $value->map(fn($va) => [$va])->all();
        $response['repr'] = $questionSelections->pluck('repr');
        return $response;
    }

    private function getCorrelationAnswers($firstQuestionId, $secondQuestionId, $req)
    {
        $firstQuestion = Question::find($firstQuestionId);
        $secondQuestion = Question::find($secondQuestionId);

        if(!($firstQuestion->disp_corr && $secondQuestion->disp_corr)){
            abort(403);
        }

        $firstQuestionSelections = Selection::where('question_id', $firstQuestionId)->get()->sortBy('selection_cd');
        $secondQuestionSelections = Selection::where('question_id', $secondQuestionId)->get()->sortBy('selection_cd');
        $values = [];
        foreach($firstQuestionSelections->pluck('selection_cd') as $cd){
            $answer = $this->getAnswerBuilder($req);
            $rawValue = $answer->where($firstQuestion->name, $cd)->get([$secondQuestion->name])->pluck($secondQuestion->name)->countBy();
            $value = $secondQuestionSelections->pluck('selection_cd')->map(fn($cd) => $rawValue[$cd] ?? 0)->sortKeys()->all();
            $values[] = $value;
        }

        $response['isQuant'] = $secondQuestion->is_quant;
        $response['xlabel'] = $firstQuestionSelections->pluck('description');
        $response['ylabel'] = $secondQuestionSelections->pluck('description');
        $response['value'] = $values;
        $response['repr'] = $secondQuestionSelections->pluck('repr');

        return $response;
    }

    public function questions(Request $params)
    {
        $isSingle = $params->input('isSingle') === 'true';

        $response = [];

        if($isSingle){
            $questionGroups = QuestionGroup::with(['questions' => fn($query) => $query->where('disp_single', true)->orderBy('id', 'asc')])
                ->orderBy('id', 'asc')->get();
        } else {
            $questionGroups = QuestionGroup::with(['questions' => fn($query) => $query->where('disp_corr', true)->orderBy('id', 'asc')])
                ->orderBy('id', 'asc')->get();
        }

        $response['questionGroups'] = $questionGroups;

        return $response;
    }
}
