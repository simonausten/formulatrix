formulatrix
===========

Ultra simple, smart HTML5 form generation in PHP and Python

*Because forms are bad and they need to be disciplined*

Formulatrix (FX for short) lets you generate HTML5 forms in PHP (and Python), by simply supplying the field names of the form. FX takes snake-case (underscore separated) field names, splits them, looks for common substrings, determines what kind of form control would suit them best (text, password, radio, select etc.) and generates a form, with the field names used as names and ids, and correctly titlecased as labels.

Twitter Bootstrap forms coming soon!

Usage
=====

*Python*

    form = FX(legend='Create a User')
    form.addFields('name','date_of_birth','gender','home_country')
    print form.getForm()
    
*PHP*

    TBC
    
*Result:*

    <form class="form-divs" action=""><fieldset>
    <legend>Create a User</legend>
    <div>
    <label for="name">Name</label>
    <input type="text" name="name" id="name" placeholder="" />
    </div>
    <div>
    <label for="date_of_birth">Date of Birth</label>
    <input type="date" name="date_of_birth" id="date_of_birth" placeholder="" />
    </div>
    <div>
    <label for="home_country">Home Country</label>
    <select name="home_country" ><option value="US">Us</option><option value="UK">Uk</option><option value="Robonia">Robonia</option></select>
    </div>
    <div><button type="submit">Submit</button></div>
    </fieldset></form>



Field Name Substrings
=====================

FX currently recognises and maps the following substrings to these field types.

Example : 'user\_home\_country' maps to \<select\>  
Example : 'event\_starts\_on' maps to \<input type='datetime' /\>

'checkbox':['is', 'has', 'opt']  
'color':['color','colour']  
'date':['date']  
'datetime':['start','starts','end','ends']  
'email':['email']  
'file':['file']  
'hidden':['key','secret']  
'image':['pic','image','img']  
'month':['month']  
'number':['num','number','id']  
'password':['password']  
'select':['type', 'country', 'state', 'county']  
'range':['range']  
'radio':['or', 'sex', 'gender']  
'search':['search','term','query']  
'tel':['tel','phone']  
'time':['time']  
'url':['url','link']  
'week':['week']  
'textarea':['note','description']



