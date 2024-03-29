 
Code Igniter and Smarty Template Engine.
------------------------------------------------------------------

Assembled by Iarfhlaith Kelly, e-mail@iarfhlaith.com. @iarfhlaith.

Date: Nov 2nd, 2011.

This zip file includes a clean working sample of:

- Code Igniter - Version 2.0.3 (http://codeigniter.com/download.php)

- Smarty Template Engine - Version 3.1.4 (http://www.smarty.net/download)

- Bridging code written by http://www.coolphptools.com (http://www.coolphptools.com/userfiles/downloads/codeigniter-smarty-3_1.zip)

To assemble this code I followed this excellent guide: http://www.coolphptools.com/codeigniter-smarty

REQUIRED

------------------------------------------------------------------

In my environment I'm using PHP version 5.3.5 running on Apache 2.2.17.

Note: Smarty version 3 is not backwards compatible with PHP 4.x.

------------------------------------------------------------------

AMENDMENTS

- Template Code Delimiters

In this sample I have changed the left and right template code delimiters from the standard curly
brackets to a combination of tildes and square brackets. As much of the code I write can be Javascript
the default template code delimiters in Smarty can cause a few issues.

So {smarty_variable} becomes [~smarty_variable~].

It's a little more verbose, but gets around having to use {literal} tags everytime you need to add 
Javascript in your .tpl files.

- Error Reporting Level

By default in Smarty 3 template error level reporting is not used. Instead Smarty uses the global error
reporting defined by PHP. As many Smarty templates use code such as:

<snippet>
    [~ if $message ~] Please complete the form. [~ /if ~]
</snippet>


... then in times when $message is not defined and PHP is set to throw an error on a Notice then this
code will display an error. As this code is very common, particularly in legacy .tpl files, I amended
the Smarty.php file again to not throw notices on errors in the Smarty templates.


</end>
