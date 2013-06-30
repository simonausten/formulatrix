import unittest
import os

from formulatrix import FX
import inspect
import sys

HTML_PATH = "formhtml"

class TestFormulatrix(unittest.TestCase):
    
    def getHTML(self, html):
        global HTML_PATH
        try:
            with open(os.path.join(HTML_PATH, html+'.html')) as fh:
                return fh.read()
        except:
            # Create a corresponding HTML file here, we'll need it
            with open(os.path.join(HTML_PATH, html+'.html'),'w') as fh:
                return False


    def testFormTag(self):
        
        cases = {'testAction':      {'action':'submit.php'},
                 'testLegend':      {'legend':'Create a user'},
                 'testCustomButton':{'submit':'Create User'}}                 
        
        for _case in cases:
            
            print _case
            
            case = cases[_case]
            html = self.getHTML(_case)
            
            form = FX(**case)
    
            self.maxDiff = None
            self.assertMultiLineEqual(form.getForm(), html)
        
    def testDivFormElements(self):
        
        cases = {'testTextField':           ['name'],
                 'testPasswordField':       ['password'],
                 'testEmailField':          ['email'],
                 'testTextArea':            ['description'],
                 'testCheckboxOn':          [{'name':'opt_in', 'checked':True, 'note':'Check here to opt in'}],
                 'testCheckboxOff':         [{'name':'opt_in', 'note':'Check here to opt in'}],
                 'testSelectByString':      ['user_type(male, female)'],
                 'testSelectByArray':       [{'name':'user_type','options':['male', 'female']}]}                 
        
        for _case in cases:
            
            print _case
            
            case = cases[_case]
            html = self.getHTML(_case)
            
            form = FX()
            form.setFields(*case)
    
            self.maxDiff = None
            self.assertMultiLineEqual(form.getForm(), html)

#         form = FX(action = 'create.php', style = 'form-horizontal', legend = 'Create a new user', button='Press this!')
#         form.setFields('name', 'item_description',
#             {'name':'email', 'placeholder':'Your email'},
#             {'name':'is_banned', 'checked':True, 'note':'Check this'},
#             'sub_starts_on',
#             'time_start',
#             'web_url_homepage',
#             {'name':'blah', 'type':'textarea'},
#             {'name':'user_type', 'options':['mother','maiden','crone']})
         

if __name__ == '__main__':
    
    # For convenience, we create html for each test case if it doesn't exist
    # The relevant test will fail the first time (at least ;-) ) but it
    # stops us having to create files manually
    
#     for f in dir(TestFormulatrix):
#         if f.startswith('test'):
#             try:
#                 with open(os.path.join('formhtml',f+'.html')):pass
#             except:
#                 with open(os.path.join('formhtml',f+'.html'),'w'):pass
                
    
    unittest.main()
    