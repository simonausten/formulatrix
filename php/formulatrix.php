<?php

class FX {

	public static function titlecase($title) {
		// Our array of 'small words' which shouldn't be capitalised if
		// they aren't the first word. Add your own words to taste.
		$smallwordsarray = array(
		'of','a','the','and','an','or','nor','but','is','if','then','else','when',
		'at','from','by','on','off','for','in','out','over','to','into','with'
		);
		
		// Split the string into separate words
		$words = explode(' ', $title);
		
		foreach ($words as $key => $word)
		{
		// If this word is the first, or it's not one of our small words, capitalise it
		// with ucwords().
		if ($key == 0 or !in_array($word, $smallwordsarray))
		$words[$key] = ucwords($word);
		}
		
		// Join the words back into a string
		$newtitle = implode(' ', $words);
		
		return $newtitle;
	}
	
	public function __construct(){
	
		$kwargs = func_get_args();
		
		$this -> action = (isset($kwargs['action'])) ? $kwargs['action'] : '';
		$this -> action = (isset($kwargs['style'])) ? $kwargs['style'] : 'form-divs';
		$this -> action = (isset($kwargs['legend'])) ? $kwargs['legend'] : '';
		$this -> action = (isset($kwargs['submit'])) ? $kwargs['submit'] : 'Submit';
		$this -> action = (isset($kwargs['fields'])) ? $kwargs['fields'] : array();
		
		
		$this -> _keywordmap =array('checkbox' => array('is', 'has', 'opt'),
									'color' => array('color','colour'),
									'date' => array('date'),
									'datetime' => array('start','starts','end','ends'),
									'email' => array('email'),
									'file' => array('file'),
									'hidden' => array('key','secret'),
									'image' => array('pic','image','img'),
									'month' => array('month'),
									'number' => array('num','number','id'),
									'password' => array('password'),
									'select' => array('type', 'country', 'state', 'county'),
									'range' => array('range'),
									'radio' => array('or', 'sex', 'gender'),
									'search' => array('search','term','query'),
									'tel' => array('tel','phone'),
									'time' => array('time'),
									'url' => array('url','link'),
									'week' => array('week'),
									'textarea' => array('note','description'));
									
		$this -> keywords = array();
		foreach ($this -> _keywordmap as $itype => $keywords) {
			foreach ($keywords as $keyword){
				$this -> keywords[$keyword] = $itype;
			}
		}
			
		
		$this -> styles = array('form-divs' => array(
									'label' => '<label for="%(name)s">%(label)s</label>',
									 'text' => '<input type="%(itype)s" name="%(name)s" id="%(name)s" placeholder="%(placeholder)s" />',
									 'textarea' => '<textarea name="%(name)s" id="%(name)s" placeholder="%(placeholder)s" /></textarea>',
									 'open' => '<div>',
									 'close' => '</div>',
									 'class' => 'form-divs',
									 'submit' => '<div><button type="submit">%s</button></div>',
									 'formopen' => '<form class="%s" action="%s"><fieldset>'),
					
		      					'form-horizontal' => array( 
									'label' => '<label for="%(name)s">%(label)s</label>',
									 'text' => '<div class="controls"><input type="%(itype)s" id="%(name)s" name="%(name)s" placeholder="%(placeholder)s"></div>',
									 'textarea' => '<div class="controls"><textarea id="%(name)s" name="%(name)s" placeholder="%(placeholder)s"></textarea></div>',
									 'open' => '<div class="control-group">',
									 'close' => '</div>',
									 'class' => 'form-horizontal',
									 'submit' => '<div class="control-group"><div class="controls"><button type="submit" class="btn">%s</button></div></div>',
									 'formopen' => '<form class="%s" action="%s"><fieldset>'));

	}
		
	public function setFields() {
		
		$fields = func_get_args();
		
		foreach ($fields as $f){
			$this -> addField($f);
		}
		
	}
		
	public function addField($_field) {
		
		$field = array();
		
		if ($this -> gettype($_field) == "string"){
			
			# Handle list types
			$_field = str_replace(' ', '', $_field);
			$listtest = preg_match_all("([A-Za-z0-9_]*)\(([A-Za-z0-9_\,]*)\)", $_field);
			
			if ($listtest){
				$_field = array('name' => $listtest[0][0], 'options' => $listtest[0][1].split(','));
			} else {
				$field['itype'] = $this -> getType($_field);
				$name = $_field;
				$field['placeholder'] = '';
			}
		}
		if ($this -> gettype($_field) == "array" and !isset($_field['itype'])) {
			
			if (!isset($_field['name'])) {
				throw new Exception("'name' not found in $_field dictionary");
			} else {
				$name = $_field['name'];
				$field['itype'] = $this -> getType($name);
			}
			
			$field['placeholder'] = (isset($_field['placeholder'])) ? $_field['placeholder'] : '';
			$field['value'] = (isset($_field['value'])) ? $_field['value'] : '';
			$field['multiple'] = (isset($_field['multiple']) and $_field['multiple'] == TRUE)
								  ? 'multiple' : '';
			$field['checked'] =  (isset($_field['checked']) and $_field['checked'] == TRUE)
								  ? 'checked' : '';
			$field['options'] = (isset($_field['options'])) ? $_field['options'] : array();
		
		}
		
		if (preg_match('[^A-Za-z0-9_]', $name)) {
			throw new Exception("'name' must only have numbers, letters, underscores and brackets (see docs)");
		}
			
		
		$field['name'] = $name;
		$id = $name;
		$field['itype'] = $this -> getType($name);
		$field['label'] = $this -> titlecase(implode(" ", explode("_", $name)));		
		$field['note'] = (isset($_field['note'])) ? $_field['note'] : '';
		
		$this -> fields[] = $field;
		
	}
		
	public function getType($name) {
		
		foreach ($this -> keywords as $keyword){
			
			if (preg_match(sprintf('^%s$|_%s|%s_', $keyword, $keyword, $keyword), $name)) {
				return $this -> keywords[$keyword];
			}
		
		}

		return 'text';
	
	}
		
	public function getForm() {
		
		$form = array();
		
		$form[] = $this -> getFormOpenTag();
		
		if ($this -> legend) {
			$form[] = $this -> getLegendTag();	
		}
		
		foreach ($this -> $fields as $field){
			
			$form[] = $this -> getOpenTag($field);
			
			#if $this -> labels:
			$form[] = $this -> getLabel($field);
			$form[] = $this -> getInput($field);
			
			$form[] = $this -> getCloseTag($field);
		}			
		
		$form[] = $this -> getSubmit();
		$form[] = $this -> getFormCloseTag();
		
		
		return implode('\n', $form);
		
	}
			
	public function getLabel($field) {
		return sprintf($this -> styles[$this -> style]['label'], $field);
	}
	
	public function getInput($field) {
		
		if ($field['itype'] == 'checkbox') {
			return $this -> getCheckboxInput($field);
		} else if ($field['itype'] == 'radio') {
			return $this -> getRadioInput($field);
		} else if ($field['itype'] == 'select') {
			return $this -> getSelectInput($field);
		} else if ($field['itype'] == 'textarea') {
			return $this -> getTextArea($field);
		} else {
			return $this -> getDefaultInput($field);
		}
			
	}
			
	public function getDefaultInput($field) {
		return sprintf($this -> styles[$this -> style]['text'], $field);
	}
	
	public function getTextArea($field) {
		return sprintf($this -> styles[$this -> style]['textarea'], $field);
	}

	public function getCheckboxInput($field) {
		
		if ($this -> style == 'form-divs') {
			return sprintf('<input type="checkbox" name="%(name)s" id="%(name)s" %(checked)s /> %(note)s', $field);
		}
			
	}
		
	public function getRadioInput($field) {
		
		if (count($field['options'])<2) {
			throw new Exception("Radio input must have two or more options");
		}
		
		$radio = "";
		foreach ($field['options'] as $o) {
			if ($this -> style == 'form-divs') {
				$radio .= sprintf('<input type="radio" name="%s" value="%s"> %s', $field['name'], $o, titlecase($o));
			}
		}
			
		return $radio;
	
	}
		
	public function getSelectInput($field) {
		
		if (!isset($field['options'])){
			throw new Exception("Select must have 'option' list");
		} 
		$field['multiple'] = (isset($field['multiple'])) ? $field['multiple'] : '' ;
		
		$select = sprintf('<select name="%(name)s" %(multiple)s>' , $field);
		foreach ($field['options'] as $o) {
			if ($this -> style == 'form-divs') {
				$select .= sprintf('<option value="%s">%s</option>', $o, titlecase($o));
			}
		}
		$select .= "</select>";
		
		return $select;
	
	}
	
	public function getOpenTag($field) {
		return sprintf($this -> styles[$this -> style]['open'] , $field);
	}
	
	public function getCloseTag($field) {
		return $this -> styles[$this -> style]['close'];
	}
		
	public function getFormOpenTag() {
		return sprintf($this -> styles[$this -> style]['formopen'], $this -> style, $this -> action);
	}
	
	public function getFormCloseTag() {
		return "</fieldset></form>";
	}
	
	public function getLegendTag() {
		return sprintf("<legend>%s</legend>", $this -> legend);
	}
	
	public function getSubmit() {
		return sprintf($this -> styles[$this -> style]['submit'], $this -> submit);
	}
	
	public function clear() {
		$this -> fields = array();
	}
}

	
?>