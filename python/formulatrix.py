#!/usr/bin/env python
# -*- coding: latin-1 -*-

import random
import unittest
import re
from titlecase import titlecase
	
class FX(object):
	
	def __init__(self, **kwargs):
		
		self.action = kwargs.get('action',"")
		self.style = kwargs.get('style',"form-divs")
		self.legend = kwargs.get('legend',"")
		self.submit = kwargs.get('submit',"Submit")
		self.fields = kwargs.get('fields',[])
		
		
		self._keywordmap ={'checkbox':['is', 'has', 'opt'],
						'color':['color','colour'],
						'date':['date'],
						'datetime':['start','starts','end','ends'],
						'email':['email'],
						'file':['file'],
						'hidden':['key','secret'],
						'image':['pic','image','img'],
						'month':['month'],
						'number':['num','number','id'],
						'password':['password'],
						'select':['type', 'country', 'state', 'county'],
						'range':['range'],
						'radio':['or', 'sex', 'gender'],
						'search':['search','term','query'],
						'tel':['tel','phone'],
						'time':['time'],
						'url':['url','link'],
						'week':['week'],
						'textarea':['note','description']}
		self.keywords = {}
		for itype in self._keywordmap:
			for keyword in self._keywordmap[itype]:
				self.keywords[keyword] = itype
		
		self.styles = {'form-divs':
						{'label':'<label for="%(name)s">%(label)s</label>',
						 'text':'<input type="%(itype)s" name="%(name)s" id="%(name)s" placeholder="%(placeholder)s" />',
						 'textarea':'<textarea name="%(name)s" id="%(name)s" placeholder="%(placeholder)s" /></textarea>',
						 'open':'<div>',
						 'close':'</div>',
						 'class':'form-divs',
						 'submit':'<div><button type="submit">%s</button></div>',
						 'formopen':'<form class="%s" action="%s"><fieldset>'},
					
		      		'form-horizontal':
						{'label':'<label for="%(name)s">%(label)s</label>',
						 'text':'<div class="controls"><input type="%(itype)s" id="%(name)s" name="%(name)s" placeholder="%(placeholder)s"></div>',
						 'textarea':'<div class="controls"><textarea id="%(name)s" name="%(name)s" placeholder="%(placeholder)s"></textarea></div>',
						 'open':'<div class="control-group">',
						 'close':'</div>',
						 'class':'form-horizontal',
						 'submit':'<div class="control-group"><div class="controls"><button type="submit" class="btn">%s</button></div></div>',
						 'formopen':'<form class="%s" action="%s"><fieldset>'}}
		
	def setFields(self, *fields):
		for f in fields:
			self.addField(f)
		
	def addField(self, _field):
		
		field = {}
		
		if isinstance(_field, str):
			
			# Handle list types
			_field = _field.replace(' ','')
			listtest = re.findall("([A-Za-z0-9_]*)\(([A-Za-z0-9_\,]*)\)", _field)
			if listtest:
				_field = {'name':listtest[0][0], 'options':listtest[0][1].split(',')}
			else:
				field['itype'] = self.getType(_field)
				name = _field
				field['placeholder'] = ''
		
		if isinstance(_field, dict) and not _field.get('itype', False):
			if not _field.get('name'):
				raise KeyError("'name' not found in _field dictionary")
			else:
				name = _field['name']
				field['itype'] = self.getType(name)
			field['placeholder'] = _field.get('placeholder', '')
			field['value'] = _field.get('value', '')
			field['multiple'] = 'multiple' if _field.get('multiple', False)==True else ''
			field['checked'] = 'checked' if _field.get('checked', False)==True else ''
			field['options'] = _field.get('options', [])
			field['note'] = _field.get('note', '')
		
		if re.search('[^A-Za-z0-9_]', name):
			raise ValueError("'name' must only have numbers, letters, underscores and brackets (see docs)")
		
		field['name'] = id = name
		field['itype'] = self.getType(name)
		field['label'] = titlecase(" ".join(name.split('_')))
		field['note'] = field.get('note', '')
		
		self.fields.append(field)
		
	def getType(self, name):
		
		for keyword in self.keywords:
			if re.search('^%s$|_%s|%s_' % tuple([keyword]*3), name.lower()):
				return self.keywords[keyword]
		else:
			return 'text'
		
	def getForm(self):
		
		form = []
		
		form.append(self.getFormOpenTag())
		
		if self.legend:form.append(self.getLegendTag())
		
		for field in self.fields:
			
			#print field
			
			form.append(self.getOpenTag(field))
			
			#if self.labels:
			form.append(self.getLabel(field))
			form.append(self.getInput(field))
			
			form.append(self.getCloseTag(field))
		
		form.append(self.getSubmit())
		form.append(self.getFormCloseTag())
		
		
		return "\n".join(form)
			
	def getLabel(self, field):
		return self.styles[self.style]['label'] % field
	
	def getInput(self, field):
		
		if field['itype'] == 'checkbox':
			return self.getCheckboxInput(field)
		elif field['itype'] == 'radio':
			return self.getRadioInput(field)
		elif field['itype'] == 'select':
			return self.getSelectInput(field)
		elif field['itype'] == 'textarea':
			return self.getTextArea(field)
		else:
			return self.getDefaultInput(field)
			
	def getDefaultInput(self, field):
		return self.styles[self.style]['text'] % field
	
	def getTextArea(self, field):
		return self.styles[self.style]['textarea'] % field

	def getCheckboxInput(self, field):
		
		if self.style == 'form-divs':
			return '<input type="checkbox" name="%(name)s" id="%(name)s" %(checked)s /> %(note)s' % field
		
	def getRadioInput(self, field):
		
		if len(field['options'])<2: raise ValueError("Radio input must have two or more options")
		
		radio = ""
		for o in field['options']:
			if self.style == 'form-divs':
				radio += '<input type="radio" name="%s" value="%s"> %s' % (field['name'], o, titlecase(o))
			
		return radio
		
	def getSelectInput(self, field):
		
		if not field.has_key('options'): raise KeyError("Select must have 'option' list")
		field['multiple'] = field['multiple'] if field.has_key('multiple') else '' 
		
		select = '<select name="%(name)s" %(multiple)s>' % field
		for o in field['options']:
			if self.style == 'form-divs':
				select += '<option value="%s">%s</option>' % (o, titlecase(o))
		select += "</select>"
		
		return select
	
	def getOpenTag(self, field):
		return self.styles[self.style]['open'] % field
	
	def getCloseTag(self, field):
		return self.styles[self.style]['close']
			
	def getFormOpenTag(self):
		return self.styles[self.style]['formopen'] % (self.style, self.action)
	
	def getFormCloseTag(self):
		return "</fieldset></form>"
	
	def getLegendTag(self):
		return "<legend>%s</legend>" % self.legend
	
	def getSubmit(self):
		return self.styles[self.style]['submit'] % self.submit
	
	def clear(self):
		fields = []


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
	

