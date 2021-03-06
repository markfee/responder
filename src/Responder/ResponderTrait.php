<?php namespace Markfee\Responder\Responder;

use Illuminate\Database\QueryException;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
/*
 * ResponderInterface is the interface for version 2 of the responder.
 * it replaces the need for inheritance with the requirement to implement an interface instead.
 * the functionality of the interface is defined with the ResponderTrait
 */
trait ResponderTrait {
    use TransformTrait;
    use PaginatorTrait;    

    protected function FoundOr404($data, $msg = null) 
    {
        return count($data) 
            ? $this->Found($data) 
            : $this->NotFound($msg);
    }

    /**
     * @return Response
     */
    protected function Found($data = null, $msg = null) 
    {
        return (new Response())
            ->withMessage($msg)
            ->withData($this->transform($data))
            ->withStatusCode(ResponseCodes::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function NotFound($msg = null) 
    {
        return (new Response())
            ->withError($msg)
            ->withStatusCode(ResponseCodes::HTTP_NOT_FOUND);
    }

    /**
     * @return Response
     */
    protected function Created($data = null, $msg = null) 
    {
        return (new Response())
            ->withMessage($msg)
            ->withData($this->transform($data))
            ->withStatusCode(ResponseCodes::HTTP_CREATED);
    }

    /**
     * @return Response
     */
    protected function Deleted($msg = "successful delete") {
        return $this->Found(null, $msg, ResponseCodes::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function Updated($data = null, $msg = "Updated record successfully.") {
        return $this->Found($data, $msg, ResponseCodes::HTTP_OK);
    }



    /**
     * @return Response
     */
    protected function WithError($msg, $statusCode) {
        return (new Response())
            ->withError($msg)
            ->withStatusCode($statusCode);
    }

    /**
     * @return Response
     */
    public function ReferentialIntegrityError($msg = null) {
        return $this->WithError($msg, ResponseCodes::HTTP_CONFLICT);
    }

    /**
     * @return Response
     */
    public function ValidationFailed($msg = null) 
    {
        return (new Response())
            ->withError($msg)
            ->withStatusCode(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return Response
     */
    public function NotAuthorised($msg = "Forbidden request") {
        return $this->WithError($msg, ResponseCodes::HTTP_FORBIDDEN);
    }

    /**
     * @return Response
     */
    public function NotLoggedIn($msg = "You must be logged in ") {
        return $this->WithError($msg, ResponseCodes::HTTP_UNAUTHORIZED);
    }

    private $custom_validation_messages = [];

    public function TransformAndValidateModel($data, $model, $success_callback, $fail_callback = null)
    {
        $rules = empty($model->rules) ? [] : $model->rules;
        $this->custom_validation_messages = empty($model->validation_messages) ? [] : $model->validation_messages;

        return $this->TransformAndValidate($data, $rules, $success_callback);
    }

    public function TransformAndValidate($data, $rules, $success_callback, $fail_callback = null)
    {
        $transformed_data = $this->transformInput($data);
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = \Validator::make($transformed_data, $rules, $this->custom_validation_messages);
        if ($validator->fails()) {
            return $fail_callback ? $fail_callback($validator)
                : $this->ValidationFailed($validator->getMessageBag()->all());
        }
        try {
            return $success_callback($transformed_data);    
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            return (new Response())
                ->withError("The record failed to save")
                ->withStatusCode(ResponseCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function WithQueryParameters($parameter_array)
    {
        \Input::merge($parameter_array);
        return $this;
    }    

    /**
     * @param null $data
     * @param string $filename
     * @param string $sheetname
     * @return a downloaded excel document
     */
    public function Spreadsheet($data, $filename="spreadsheet", $sheetname="sheet1") 
    {
        $excel = \App::make('excel');
        return $excel->create($filename, 
            function($excel) use ($data, $sheetname) 
            {
                $excel->sheet($sheetname, 
                    function($sheet) use ($data)
                    {
                        $row_number = 1;
                        foreach($data as $row_data) {
                            // Add a header Row For the First Row.
                            if ($row_number == 1) {
                                $sheet->row($row_number++, 
                                array_keys($this->transform($row_data)));
                            }
                            // Add a Row for each row of data.
                            $sheet->row($row_number++, $this->transform($row_data));    
                        }
                    }
                );
            }
        )->download('xls');
    }    
}
