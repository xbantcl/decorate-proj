<?php namespace Decorate\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
*
*/
class Validator
{
    protected $errors;

    public function validate($request,array $rules)
    {
        $basicRules = [
            'sys_p' => v::noWhitespace()->notEmpty(),
            'sys_v' => v::noWhitespace()->notEmpty(),
            'sys_c' => v::noWhitespace()->notEmpty(),
        ];
        $rules = array_merge($rules, $basicRules);
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName(ucfirst($field))->assert($request->getParam($field));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }
        return $this;
    }

    public function failed()
    {
        return !empty($this->errors);
    }

    public function outputError($response)
    {
        if ($this->failed()) {
            return $response->write(json_encode([
                'error_code' => 10001,
                'message' => current(current($this->errors)),
            ]));
        }
    }
}
