<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Closure;
class UniqueDynamicField implements ValidationRule
{
    protected $fieldId;
    protected $column;
    protected $table;
    protected $fieldName;

    public function __construct($fieldId, $table = 'users',$column = 'dynamic_fields',$fieldName)
    {
        $this->fieldId = $fieldId;
        $this->table = $table;
        $this->column = $column;
        $this->fieldName = $fieldName;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isUnique=DB::table($this->table)
        ->whereJsonContains($this->column, [
            'id' => $this->fieldId,
            'value' => $value,
        ])
        ->exists();
        if ($isUnique) {
            $fail(__('messages.unique',['attribute'=>$this->fieldName]));
        }
    }
    public function passes($attribute, $value)
    {
        // Query the database for matching JSON
        return !DB::table($this->table)
            ->whereJsonContains($this->column, [
                'id' => $this->fieldId,
                'value' => $value,
            ])
            ->exists();
    }

    public function message()
    {
        return 'The :attribute must be unique.';
    }
}
