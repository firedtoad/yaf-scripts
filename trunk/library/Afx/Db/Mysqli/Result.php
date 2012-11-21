<?php
class Afx_Db_Mysqli_Result
{

    /**
     * @var mysqli_result
     */
    public $result;

    public $__result_array = array();

    public function recordCount ()
    {
        return $this->result->num_rows;
    }

    public function row ($n = 0)
    {
        $i = 0;
        $result = $this->result;
        $result_array = array();
        if ($this->result)
        {
            while ($i <= $n && $r = $result->fetch_assoc())
            {
                $result_array[] = $r;
            }
        }
        return isset($result_array[$n]) ? $result_array[$n] : array();
    }

    public function result ()
    {
        if (count($this->__result_array))
        {
            return $this->__result_array;
        }
        $result = $this->__fetch_all();
        return $result;
    }

    public function fieldCount ()
    {
        return $this->result->field_count;
    }

    private function __fetch_all ()
    {
        if(count($this->__result_array))return $this->__result_array;
        $result = $this->result;
        $result_array = array();
        if ($result)
        {
            while (FALSE != ($r = $result->fetch_assoc()))
            {
                $result_array[] = $r;
            }
        }
        $this->__result_array = $result_array;
        return $result_array;
    }
}