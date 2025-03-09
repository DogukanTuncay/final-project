<?php

namespace App\Exceptions;

use Exception;

class UniqueConstraintException extends Exception
{
    /**
     * Constraint adı
     *
     * @var string
     */
    protected $constraint;

    /**
     * Hata kodunu ve constraint adını içeren exception oluşturur
     *
     * @param  string  $constraint
     * @param  string  $message
     * @param  integer $code
     * @param  \Throwable  $previous
     * @return void
     */
    public function __construct($constraint, $message = null, $code = 0, $previous = null)
    {
        $this->constraint = $constraint;
        
        if (is_null($message)) {
            $message = "Unique constraint violation: {$constraint}";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Constraint adını döndürür
     *
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * Course chapter slug hatası mı kontrol eder
     *
     * @return boolean
     */
    public function isCourseChapterSlugViolation()
    {
        return strpos($this->constraint, 'course_chapters_slug_unique') !== false;
    }

    /**
     * Course slug hatası mı kontrol eder
     *
     * @return boolean
     */
    public function isCourseSlugViolation()
    {
        return strpos($this->constraint, 'courses_slug_unique') !== false;
    }
}