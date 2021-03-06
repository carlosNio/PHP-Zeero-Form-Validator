<?php

namespace Zeero\Validator;

use Exception;
use PDO;

use Zeero\Validator\trait\MessageBuilderTrait;

/**
 * Validator
 * 
 * Used to make validations more easy
 * 
 * @author Carlos Bumba git:@CarlosNio
 */

class Validator
{
    use MessageBuilderTrait;

    private static $rules_map;
    private $rules;
    private $messages;

    private static $connection;


    public function __construct(array $rules, array $messages = null)
    {
        $this->rules = $rules;
        $this->messages = $messages;
    }

    /**
     * Add a new rule
     * 
     * @return array
     */
    public static function addRule(Rule $obj)
    {
        self::$rules_map[$obj->name] = $obj->action;
    }

    /**
     * Set the Validator PDO instance
     * 
     * @return void
     */
    public static function connection(PDO $instance)
    {
        self::$connection = $instance;
    }

    /**
     * Return the Validator PDO instance
     * 
     * @return PDO|null
     */
    public static function getConnection()
    {
        return self::$connection;
    }


    /**
     * Return a acossiative array with the validation tests
     * @return array 
     */
    public function tests()
    {
        return $this->tests;
    }


    /**
     * Return a boolena if the validation fails
     * 
     * @return bool
     */
    public function fail()
    {
        return in_array(0, array_values($this->tests));
    }


    /**
     * Return a boolean if the validation was a success
     * 
     * @return bool
     */
    public function success()
    {
        return !$this->fail();
    }


    /**
     * Return a filled array if the validatio fail, otherwise return a empty array
     * 
     * if the WITHKEY parameter be set to false , the result will be a indexed array , otherwise a acossiative array
     * 
     * @return array
     */
    public function errors(bool $withKey = true)
    {
        $messages = $this->buildMessages(
            $this->tests,
            $this->rules,
            $this->messages
        );

        if (!$withKey) $messages = array_values($messages);

        return $messages;
    }


    /**
     * Make the validation using the given data
     * 
     * @return void
     */
    public function make(array $data)
    {
        $list = $this->rules;

        foreach ($list as $field => $rule) {
            $isRequired = false;

            if (strpos($rule, "|")) {

                $list = explode('|', $rule);

                foreach ($list as $rule) {

                    $rule_info = Rule::info($rule);

                    if ($rule_info['name'] == 'required') $isRequired = true;

                    $test = $this->callRuleArgs(
                        $rule_info,
                        $field,
                        $data,
                        $isRequired
                    );

                    $index = "$field.{$rule_info['name']}";

                    if (!$isRequired) $test = true;

                    $this->tests[$index] = $test;
                }

                $isRequired = false;
            } else {
                $rule_info = Rule::info($rule);
                $required = false;

                if ($rule_info['name'] == "required") $required = true;

                $test = $this->callRuleArgs($rule_info,  $field, $data, $required);
                $index = "$field.{$rule_info['name']}";

                if (!$required) $test = true;

                $this->tests[$index] = $test;
            }
        }
    }


    /**
     * call the rule and return the test result
     * 
     * @throws Exception if the rule was not defined
     * @return bool
     */
    private function callRuleArgs(array $info, $field,  $data, $isRequired)
    {
        $name = trim($info['name']);
        $value = $data[$field] ?? null;
        $rule = self::$rules_map[$name] ?? null;

        if (is_null($rule) and $name != "required") {
            throw new Exception("Rule: < $name > is not defined");
        }

        if (is_null($value)) {
            return $isRequired ? false : true;
        }

        if (isset($info['data'])) {
            $var = $info['data'];
            // if the rule require a value from another parameter
            if (is_string($var) && $var[0] == "_")
                $var = $data[str_replace("_", '', $var)] ?? '';
            // for rules that her values are separated by comma
            if ($info['type'] == 'single' && is_array($var)) {
                $merged = array_merge($var, [$value]);
            }

            // print_r($data);

            $test =  call_user_func_array($rule, $merged ?? [$var, $value]);
            return $test;
        } else {
            if ($name != "required")
                return call_user_func_array($rule, [$value, $data]);
            else
                return true;
        }
    }
}


// load the defaults rules definitions
require_once "rules.php";