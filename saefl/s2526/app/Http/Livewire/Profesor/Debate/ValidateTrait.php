<?php

namespace App\Http\Livewire\Profesor\Debate;

trait ValidateTrait
{    
    protected $rules = [
        'competition.user_id'=>'required|integer',
        'competition.name'=>'required|string',
        'competition.token'=>'required|string',
        'competition.description'=>'required|string',
        'competition.motive'=>'nullable|string',
        'competition.date'=>'required|date',
        'competition.status_active'=>'nullable|string',
        'competition.attachment'=>'nullable|string',
        'competition.context'=>'nullable|string',

        'debate.token' => 'required|string',
        'debate.grado_id' => 'required|integer',
        'debate.seccion_id' => 'required|integer',
        'debate.name' => 'required|string',
        'debate.description' => 'required|string',
        'debate.status_active' => 'nullable|boolean',
        'debate.winner_section_id' => 'nullable|integer',
        'debate.attachment' => 'nullable|string',
        'debate.question_max' => 'required|integer',
        'debate.context' => 'required|integer',

        'group.competition_id' => 'required|integer',
        'group.name' => 'required|string',
        'group.description' => 'required|string',
        'group.attachment' => 'nullable|string',

        'question.debate_id' => 'required|integer',
        'question.category' => 'required|string',
        'question.text' => 'required|string',
        'question.time' => 'required|integer',
        'question.weighting' => 'required|integer',
        'question.observation' => 'required|string',
        'question.status_active' => 'nullable|boolean',
        'question.attachment' => 'nullable|string',
        'question.option_max' => 'required|integer',
        'question.context' => 'required|integer',

        'option.text' => 'required|string',
        'option.observation' => 'nullable|string',
        'option.status_option_correct' => 'nullable|boolean',
        'option.attachment' => 'nullable|string',
        'option.context' => 'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'competition.user_id' => $this->list_comment['user_id'],
            'competition.name' => $this->list_comment['name'],
            'competition.token' => $this->list_comment['token'],
            'competition.description' => $this->list_comment['description'],
            'competition.motive' => $this->list_comment['motive'],
            'competition.date' => $this->list_comment['date'],
            'competition.status_active' => $this->list_comment['status_active'],
            'competition.attachment' => $this->list_comment['attachment'],

            'debate.grado_id' => $this->list_comment_debate['grado_id'],
            'debate.seccion_id' => $this->list_comment_debate['seccion_id'],
            'debate.name' => $this->list_comment_debate['name'],
            'debate.description' => $this->list_comment_debate['description'],
            'debate.status_active' => $this->list_comment_debate['status_active'],
            'debate.winner_section_id' => $this->list_comment_debate['winner_section_id'],
            'debate.attachment' => $this->list_comment_debate['attachment'],
            'debate.question_max' => $this->list_comment_debate['question_max'],

            'group.competition_id' => $this->list_comment_group['competition_id'],
            'group.name' => $this->list_comment_group['name'],
            'group.description' => $this->list_comment_group['description'],
            'group.attachment' => $this->list_comment_group['attachment'],

            'question.debate_id' => $this->list_comment_question['debate_id'],
            'question.category' => $this->list_comment_question['category'],
            'question.text' => $this->list_comment_question['text'],
            'question.time' => $this->list_comment_question['time'],
            'question.weighting' => $this->list_comment_question['weighting'],
            'question.observation' => $this->list_comment_question['observation'],
            'question.status_active' => $this->list_comment_question['status_active'],
            'question.attachment' => $this->list_comment_question['attachment'],
            'question.option_max' => $this->list_comment_question['option_max'],

            'option.text' => $this->list_comment_option['text'],
            'option.observation' => $this->list_comment_option['observation'],
            'option.status_option_correct' => $this->list_comment_option['status_option_correct'],
            'option.attachment' => $this->list_comment_option['attachment'],
        ];
    }
}