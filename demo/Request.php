<?php

use Zeero\Validator\Form;

class Request extends Form
{
    public function rules()
    {
        return[
                'name' => 'required|min:6',
                'id' => 'required|pattern:digit',
                'photo' => 'required|max-size:15031|mime:js|no-error'
            ];
    }

    public function messages()
    {
        return [
            "required" => "Fill all fields",
            "name.min" => "name must be greatest than {min} chars",
            "id.pattern" => "id must be a digit"
        ];
    }
}
