<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

/**
 * Description of ApplicationWorkflow
 *
 * @author Administrator
 */
class LetterWorkflow implements \raoul2000\workflow\source\file\IWorkflowDefinitionProvider{
    public function getDefinition() 
    {
        return [
            'initialStatusId' => 'new',
            'status' => [
                'new' => [
                    'transition' => ['assigned']
                ],
                'assigned' => [
                    'transition' => ['completed', 're-assigned']
                ],
                're-assigned' => [
                    'transition' => ['completed']
                ],
                'completed' => [
                    'transition' => ['completed']
                ],
            ]
        ];
    }
}
