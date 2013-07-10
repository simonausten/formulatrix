<?php

require('formulatrix.php');

$FX = new FX();
$FX -> setFields('name');
die('asxas');
print_r($FX);

// if __name__ == '__main__':
	// form = FX(action = 'create.php', style = 'bootstrap-horizontal', legend = 'Create a new user', submit='Press this!')
	// form.setFields('name', 'item_description',
        // {'name':'email', 'placeholder':'Your email'},
        // {'name':'is_banned', 'checked':True, 'note':'Check this'},
        // 'sub_starts_on',
        // 'time_start',
        // 'web_url_homepage',
        // {'name':'blah', 'type':'textarea'},
        // {'name':'user_type', 'options':['mother','maiden','crone']})
	// print form.getForm()

?>