<?php

namespace Fd\HslBundle\Maker;

use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Question\Question;

trait MakerTrait
{
    private function createEntityClassQuestion(string $questionText): Question
    {
        $question = new Question($questionText);
        $question->setValidator([Validator::class, 'notBlank']);
        $question->setAutocompleterValues($this->doctrineHelper->getEntitiesForAutocomplete());

        return $question;
    }
}