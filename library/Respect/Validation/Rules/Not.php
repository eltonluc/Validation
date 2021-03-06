<?php

namespace Respect\Validation\Rules;

use Respect\Validation\Validatable;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Exceptions\ValidationException;

class Not extends AbstractRule
{

    public $rule;

    public function __construct(Validatable $rule)
    {
        if ($rule instanceof AbstractComposite)
            $rule = $this->absorbComposite($rule);

        $this->rule = $rule;
    }

    public function validate($input)
    {
        if ($this->rule instanceof AbstractComposite)
            return $this->rule->validate($input);

        return!$this->rule->validate($input);
    }

    public function assert($input)
    {
        if ($this->rule instanceof AbstractComposite)
            return $this->rule->assert($input);

        try {
            $this->rule->assert($input);
        } catch (ValidationException $e) {
            return true;
        }

        throw $this->rule
            ->reportError($input)
            ->setMode(ValidationException::MODE_NEGATIVE);
    }

    protected function absorbComposite(AbstractComposite $rule)
    {
        $clone = clone $rule;
        $rules = $clone->getRules();
        $clone->removeRules();

        foreach ($rules as &$r)
            if ($r instanceof AbstractComposite)
                $clone->addRule($this->absorbComposite($r));
            else
                $clone->addRule(new static($r));

        return $clone;
    }

}

/**
 * LICENSE
 *
 * Copyright (c) 2009-2011, Alexandre Gomes Gaigalas.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *
 *     * Neither the name of Alexandre Gomes Gaigalas nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */