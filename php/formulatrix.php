<?php
error_reporting(E_ALL);

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
		
		
		$this -> _keywordmap =array(array('checkbox' => array('is', 'has', 'opt'),
									array('color' => array('color','colour')),
									array('date' => array('date')),
									array('datetime' => array('start','starts','end','ends')),
									array('email' => array('email')),
									array('file' => array('file')),
									array('hidden' => array('key','secret')),
									array('image' => array('pic','image','img')),
									array('month' => array('month')),
									array('number' => array('num','number','id')),
									array('password' => array('password')),
									array('select' => array('type', 'country', 'state', 'county')),
									array('range' => array('range')),
									array('radio' => array('or', 'sex', 'gender')),
									array('search' => array('search','term','query')),
									array('tel' => array('tel','phone')),
									array('time' => array('time')),
									array('url' => array('url','link')),
									array('week' => array('week')),
									array('textarea' => array('note','description'))));
									
		$this -> keywords = array();
		foreach ($this -> _keywordmap as $itype) {
			foreach ($this -> _keywordmap[$itype] as $keyword){
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
			$this -> addField(f);
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
		if ($this -> gettype($_field) == "array" and !isset($_field['itype'])){
			
			if (!isset($_field['name'])){
				throw new Exception("'name' not found in $_field dictionary");
			} else {
				$name = $_field['name'];
				$field['itype'] = $this -> getType(name);
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
		$field['itype'] = $this -> getType($name);
		$field['label'] = $this -> titlecase(implode(" ", explode("_", $name)));	
		$field['note'] = (isset($_field['note'])) ? $_field['note'] : '';
		
		$this -> fields[] = $field;
		
	}
		
	public function getType($name) {
		
		foreach ($this -> keywords as $keyword){			
			if (preg_match(sprintf('^%s$|_%s|%s_', $keyword, $keyword, $keyword), $subject)){
				return $this -> keywords[keyword];
			}
		}
		
		return 'text';
	
	}
		
	public function getForm() {
		
		$form = array();
		
		$form[] = $this -> getFormOpenTag();
		
		if ($this -> legend) {
			$form.append($this -> getLegendTag());
		}
			
		
		foreach ($this -> fields as $field){
			
			$form[] = $this -> getOpenTag(field);
			
			#if $this -> labels:
			$form[] = $this -> getLabel(field);
			$form[] = $this -> getInput(field);
			
			$form[] = $this -> getCloseTag(field);
		}
		
		$form[] = $this -> getSubmit();
		$form[] = $this -> getFormCloseTag();
		
		
		return implode("\n", $form);
		
	}
			
	public function getLabel(field) {
		return $this -> styles[$this -> style]['label'] % field
	}
	
	public function getInput(field) {
		
		if field['itype'] == 'checkbox':
			return $this -> getCheckboxInput(field)
		elif field['itype'] == 'radio':
			return $this -> getRadioInput(field)
		elif field['itype'] == 'select':
			return $this -> getSelectInput(field)
		elif field['itype'] == 'textarea':
			return $this -> getTextArea(field)
		else:
			return $this -> getpublic functionaultInput(field)
			
	}
			
	public function getpublic functionaultInput(field) {
		return $this -> styles[$this -> style]['text'] % field
	}
	
	public function getTextArea(field) {
		return $this -> styles[$this -> style]['textarea'] % field
	}

	public function getCheckboxInput(field) {
		
		if $this -> style == 'form-divs':
			return '<input type="checkbox" name="%(name)s" id="%(name)s" %(checked)s /> %(note)s' % field
			
	}
		
	public function getRadioInput(field) {
		
		if len(field['options'])<2: raise ValueError("Radio input must have two or more options")
		
		radio = ""
		for o in field['options']:
			if $this -> style == 'form-divs':
				radio += '<input type="radio" name="%s" value="%s"> %s' % (field['name'], o, titlecase(o))
			
		return radio
	
	}
		
	public function getSelectInput(field) {
		
		if not field.has_key('options'): raise KeyError("Select must have 'option' list")
		field['multiple'] = field['multiple'] if field.has_key('multiple') else '' 
		
		select = '<select name="%(name)s" %(multiple)s>' % field
		for o in field['options']:
			if $this -> style == 'form-divs':
				select += '<option value="%s">%s</option>' % (o, titlecase(o))
		select += "</select>"
		
		return select
	
	}
	
	public function getOpenTag(field) {
		return $this -> styles[$this -> style]['open'] % field
	}
	
	public function getCloseTag(field) {
		return $this -> styles[$this -> style]['close']
	}
		
	public function getFormOpenTag(self) {
		return $this -> styles[$this -> style]['formopen'] % ($this -> style, $this -> action)
	}
	
	public function getFormCloseTag(self) {
		return "</fieldset></form>"
	}
	
	public function getLegendTag(self) {
		return "<legend>%s</legend>" % $this -> legend
	}
	
	public function getSubmit(self) {
		return $this -> styles[$this -> style]['submit'] % $this -> submit
	}
	
	public function clear(self) {
		fields = array();
	}
}

if __name__ == '__main__':
	form = FX(action = 'create.php', style = 'bootstrap-horizontal', legend = 'Create a new user', submit='Press this!')
	form.setFields('name', 'item_description',
        {'name':'email', 'placeholder':'Your email'},
        {'name':'is_banned', 'checked':True, 'note':'Check this'},
        'sub_starts_on',
        'time_start',
        'web_url_homepage',
        {'name':'blah', 'type':'textarea'},
        {'name':'user_type', 'options':['mother','maiden','crone']})
	print form.getForm()
	
?>