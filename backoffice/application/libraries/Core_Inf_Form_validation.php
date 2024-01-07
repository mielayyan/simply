<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Core_Inf_Form_validation extends CI_Form_validation {
    
        protected $err_id	= 'id=form_err_';

        /**
         * Custom error id
         *
         * @var string
     */

	public function set_form_error($field, $error) {
		$this->_field_data[$field]['error'] = $error;
	}
    
    /**
     * [set_form_data set input values]
     * @param [type] $field [input name]
     * @param [type] $value [post value]
     */
    public function set_form_data($field, $value) {
        $this->_field_data[$field]['postdata'] = $value;
    }
    
    /**
     * [run_with_redirect description]
     * @param  [type] $redirect_uri [description]
     * @return [redirect or bool] [description]
     */
	public function run_with_redirect($redirect_uri) {
		$result = $this->run();
		if ($result) {
			return TRUE;
		}
        $msg = null;
        $msg_type = null;
        if(!empty($this->CI->session->flashdata('MSG_ARR'))) {
            $msg = $this->CI->session->flashdata('MSG_ARR')['MESSAGE']['DETAIL'];
        }
		$this->CI->session->set_flashdata('form_error_redirect', $this->error_array());
        $this->CI->session->set_flashdata('form_input_values', $_POST);
		$this->CI->redirect($msg, $redirect_uri, $msg_type);
	}

	public function user_exists($str) {
		return $this->CI->validation_model->isUserNameAvailable($str);
	}

	public function valid_time($str) {
		return (bool)preg_match("/^(1[0-2]|0?[1-9]):[0-5][0-9] (AM|PM)$/i", $str);
	}

	public function valid_date($str) {
		$dt = DateTime::createFromFormat("Y-m-d", $str);
		return $dt !== false && !array_sum($dt->getLastErrors());
	}

    /**
     * [validation check date_lessthan_today]
     * @param  [date string] $date [date string will convert to datetime object]
     * @return [bool] true ? greater than current date(true) : lessthan current date(false)
     * @by anas
     */
    public function date_less_than_current_date($date) {
        $dt = DateTime::createFromFormat("Y-m-d", $date)->format('Y-m-d');
        $now = (new DateTime())->format('Y-m-d');
        return $dt >= $now;
    }

	public function not_equals($str, $str2) {
		return $str !== $str2;
	}
        /**
	 * Get Error Message
	 *
	 * Gets the error message associated with a particular field
	 *
	 * @param	string	$field	Field name
	 * @param	string	$prefix	HTML start tag
	 * @param 	string	$suffix	HTML end tag
	 * @return	string
	 */
	public function error($field, $prefix = '', $suffix = '')
    {
        if (empty($this->_field_data[$field]['error']))
        {
            return '';
        }

        if ($prefix === '')
        {
            $prefix = $this->_error_prefix;
        }

        if ($suffix === '')
        {
            $suffix = $this->_error_suffix;
        }

        $id = $this->err_id.$field;
                
                $prefix = substr_replace($prefix,$id, -1, 0);

        return $prefix.$this->_field_data[$field]['error'].$suffix;
    }


	 function valid_url($str){
		$url = prep_url($str);
       	$pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
        if (!preg_match($pattern, $url)){
            $this->set_message('valid_url_format', 'The URL you entered is not correctly formatted.');
            return FALSE;
        }
        return TRUE;
    }

    protected function _prepare_rules($rules)
    {
        $new_rules = array();
        $callbacks = array();

        foreach ($rules as &$rule)
        {
            // Let 'required' always be the first (non-callback) rule
            if ($rule === 'required')
            {
                array_unshift($new_rules, 'required');
            }
            // 'isset' is a kind of a weird alias for 'required' ...
            elseif ($rule === 'isset' && (empty($new_rules) OR $new_rules[0] !== 'required'))
            {
                array_unshift($new_rules, 'isset');
            }
            // The old/classic 'callback_'-prefixed rules
            elseif (is_string($rule) && strncmp('callback_', $rule, 9) === 0)
            {
                $callbacks[] = $rule;
            }
            // Proper callables
            elseif (is_callable($rule))
            {
                $callbacks[] = $rule;
            }
            // "Named" callables; i.e. array('name' => $callable)
            elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
            {
                $callbacks[] = $rule;
            }
            // Everything else goes at the end of the queue
            else
            {
                $new_rules[] = $rule;
            }
        }

        return array_merge($new_rules, $callbacks);
    }

    function valid_array_element($str, $allowed)
    {

      $allowed = explode(';', $allowed);
        if (in_array($str, $allowed)) {
            return TRUE;
        } else {
            //failed rule, set error message and return FALSE
            $this->set_message('valid_array_element', 'The %s field can only be one of the following options: ' . implode(', ', $allowed));

            return FALSE;
        }
    }

    function contains_lowercase($str, $val)
    {
        $val = $val * 1;
        if ($val < 1) {
            return FALSE;
        }
        $exp = "@[a-z]";
        while($val - 1) {
            $exp .= ".*[a-z]";
            $val--;
        }
        $exp .= "@";
        if(preg_match($exp, $str) > 0)
        	return TRUE;
        $this->set_message('contains_lowercase', lang('field_no_min_requirement'));
        return FALSE;
    }

    function contains_uppercase($str, $val)
    {
        $val = $val * 1;
        if ($val < 1) {
            return FALSE;
        }
        $exp = "@[A-Z]";
        while($val - 1) {
            $exp .= ".*[A-Z]";
            $val--;
        }
        $exp .= "@";
        if(preg_match($exp, $str) > 0)
        	return TRUE;
        $this->set_message('contains_uppercase', lang('field_no_min_requirement'));
        return FALSE;
    }

    function contains_number($str, $val)
    {
        $val = $val * 1;
        if ($val < 1)
        {
            return FALSE;
        }
        $exp = "@[0-9]";
        while($val - 1) {
            $exp .= ".*[0-9]";
            $val--;
        }
        $exp .= "@";
        if(preg_match($exp, $str) > 0)
        	return TRUE;
        $this->set_message('contains_number', lang('field_no_min_requirement'));
        return FALSE;
    }

    function contains_sp_char($str, $val)
    {
        $val = $val * 1;
        if ($val < 1)
        {
            return FALSE;
        }
        $exp = "@[\W_]";
        while($val - 1) {
            $exp .= ".*[\W_]";
            $val--;
        }
        $exp .= "@";
        if(preg_match($exp, $str) > 0)
        	return TRUE;
        $this->set_message('contains_sp_char', lang('field_no_min_requirement'));
        return FALSE;
    }

    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
        // If the $_POST data is an array we will run a recursive call
        //
        // Note: We MUST check if the array is empty or not!
        //       Otherwise empty arrays will always pass validation.
        if (is_array($postdata) && ! empty($postdata))
        {
            foreach ($postdata as $key => $val)
            {
                $this->_execute($row, $rules, $val, $key);
            }

            return;
        }

        $rules = $this->_prepare_rules($rules);
        foreach ($rules as $rule)
        {
            $_in_array = FALSE;

            // We set the $postdata variable with the current data in our master array so that
            // each cycle of the loop is dealing with the processed data from the last cycle
            if ($row['is_array'] === TRUE && is_array($this->_field_data[$row['field']]['postdata']))
            {
                // We shouldn't need this safety, but just in case there isn't an array index
                // associated with this cycle we'll bail out
                if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
                {
                    continue;
                }

                $postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
                $_in_array = TRUE;
            }
            else
            {
                // If we get an array field, but it's not expected - then it is most likely
                // somebody messing with the form on the client side, so we'll just consider
                // it an empty field
                $postdata = is_array($this->_field_data[$row['field']]['postdata'])
                    ? NULL
                    : $this->_field_data[$row['field']]['postdata'];
            }

            // Is the rule a callback?
            $callback = $callable = FALSE;
            if (is_string($rule))
            {
                if (strpos($rule, 'callback_') === 0)
                {
                    $rule = substr($rule, 9);
                    $callback = TRUE;
                }
            }
            elseif (is_callable($rule))
            {
                $callable = TRUE;
            }
            elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
            {
                // We have a "named" callable, so save the name
                $callable = $rule[0];
                $rule = $rule[1];
            }

            // Strip the parameter (if exists) from the rule
            // Rules can contain a parameter: max_length[5]
            $param = FALSE;
            if ( ! $callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match))
            {
                $rule = $match[1];
                $param = $match[2];
            }

            // Ignore empty, non-required inputs with a few exceptions ...
            if (
                ($postdata === NULL OR $postdata === '')
                && $callback === FALSE
                && $callable === FALSE
                && ! in_array($rule, array('required', 'isset', 'matches'), TRUE)
            )
            {
                continue;
            }

            // Call the function that corresponds to the rule
            if ($callback OR $callable !== FALSE)
            {
                if ($callback)
                {
                    if ( ! method_exists($this->CI, $rule))
                    {
                        log_message('debug', 'Unable to find callback validation rule: '.$rule);
                        $result = FALSE;
                    }
                    else
                    {
                        // Run the function and grab the result
                        $result = $this->CI->$rule($postdata, $param);
                    }
                }
                else
                {
                    $result = is_array($rule)
                        ? $rule[0]->{$rule[1]}($postdata)
                        : $rule($postdata);

                    // Is $callable set to a rule name?
                    if ($callable !== FALSE)
                    {
                        $rule = $callable;
                    }
                }

                // Re-assign the result to the master data array
                if ($_in_array === TRUE)
                {
                    $this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
                }
                else
                {
                    $this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
                }
            }
            elseif ( ! method_exists($this, $rule))
            {
                // If our own wrapper function doesn't exist we see if a native PHP function does.
                // Users can use any native PHP function call that has one param.
                if (function_exists($rule))
                {
                    // Native PHP functions issue warnings if you pass them more parameters than they use
                    $result = ($param !== FALSE) ? $rule($postdata, $param) : $rule($postdata);

                    if ($_in_array === TRUE)
                    {
                        $this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
                    }
                    else
                    {
                        $this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
                    }
                }
                else
                {
                    log_message('debug', 'Unable to find validation rule: '.$rule);
                    $result = FALSE;
                }
            }
            else
            {
                $result = $this->$rule($postdata, $param);

                if ($_in_array === TRUE)
                {
                    $this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
                }
                else
                {
                    $this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
                }
            }

            // Did the rule test negatively? If so, grab the error.
            if ($result === FALSE)
            {
                // Callable rules might not have named error messages
                if ( ! is_string($rule))
                {
                    $line = $this->CI->lang->line('form_validation_error_message_not_set').'(Anonymous function)';
                }
                else
                {
                    $line = $this->_get_error_message($rule, $row['field']);
                }

                // Is the parameter we are inserting into the error message the name
                // of another field? If so we need to grab its "field label"
                if (isset($this->_field_data[$param], $this->_field_data[$param]['label']))
                {
                    $param = $this->_translate_fieldname($this->_field_data[$param]['label']);
                }

                // Build the error message
                $message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']), $param);

                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if ( ! isset($this->_error_array[$row['field']]))
                {
                    $this->_error_array[$row['field']] = $message;
                    $this->_error_array[$row['field'].'_err'] = $rule;
                }

                return;
            }
        }
    }

}
